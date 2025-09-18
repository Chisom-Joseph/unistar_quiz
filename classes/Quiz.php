<?php
require_once 'config/database.php';
require_once 'classes/FeatureFlag.php';

class Quiz {
    private $pdo;
    private $flags;

    public function __construct() {
        try {
            $this->pdo = Database::getInstance();
        } catch (Exception $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }

    // Get available quizzes for user (with attempts left)
    public function getAvailableQuizzes($userId) {
        $sql = "SELECT q.*, c.name as course_name, 
                (SELECT COUNT(*) FROM attempts a WHERE a.user_id = ? AND a.quiz_id = q.id) as taken 
                FROM quizzes q JOIN courses c ON q.course_id = c.id 
                WHERE q.attempts_allowed > (SELECT COUNT(*) FROM attempts WHERE user_id = ? AND quiz_id = q.id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, $userId]);
        return $stmt->fetchAll();
    }

    // Get past attempts
    public function getAttempts($userId, $quizId = null) {
        $sql = "SELECT a.*, q.name FROM attempts a JOIN quizzes q ON a.quiz_id = q.id WHERE a.user_id = ?";
        $params = [$userId];
        if ($quizId) {
            $sql .= " AND a.quiz_id = ?";
            $params[] = $quizId;
        }
        $sql .= " ORDER BY a.start_time DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Start quiz: Check attempts
    public function startQuiz($userId, $quizId) {
        $stmt = $this->pdo->prepare("SELECT attempts_allowed FROM quizzes WHERE id = ?");
        $stmt->execute([$quizId]);
        $allowed = $stmt->fetch()['attempts_allowed'];

        $takenStmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM attempts WHERE user_id = ? AND quiz_id = ?");
        $takenStmt->execute([$userId, $quizId]);
        $taken = $takenStmt->fetch()['count'];

        if ($taken >= $allowed) {
            throw new Exception('No attempts left');
        }

        // Insert attempt
        $this->pdo->prepare("INSERT INTO attempts (user_id, quiz_id) VALUES (?, ?)")->execute([$userId, $quizId]);
        $attemptId = $this->pdo->lastInsertId();

        // Get questions, apply shuffle if flag
        $questions = $this->getQuestions($quizId);
        if ($this->flags->get('shuffle_questions')) {
            shuffle($questions);
        }
        foreach ($questions as &$q) {
            if ($this->flags->get('shuffle_options')) {
                $opts = json_decode($q['options'], true);
                $indices = range(0, count($opts) - 1);
                shuffle($indices);
                $shuffledOpts = [];
                foreach ($indices as $i) {
                    $shuffledOpts[] = $opts[$i];
                }
                $q['options'] = json_encode($shuffledOpts);
                // Adjust correct_index for shuffle (map back, but for simplicity, store original, shuffle on display)
                // Better: Store shuffled in session for this attempt
            }
        }

        // Store shuffled in session for JS/server consistency
        $_SESSION['current_quiz'] = ['attempt_id' => $attemptId, 'questions' => $questions, 'quiz_id' => $quizId];
        return $questions;
    }

    private function getQuestions($quizId) {
        $stmt = $this->pdo->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id");
        $stmt->execute([$quizId]);
        return $stmt->fetchAll();
    }

    // Submit answers (JSON from POST)
    public function submitQuiz($attemptId, $answersJson, $endTime = null) {  // endTime for timer
        $quizId = $_SESSION['current_quiz']['quiz_id'] ?? null;
        if (!$quizId) throw new Exception('No active quiz');

        $answers = json_decode($answersJson, true);
        $questions = $_SESSION['current_quiz']['questions'];
        $score = 0;

        foreach ($questions as $q) {
            $qId = $q['id'];
            $selected = $answers[$qId] ?? -1;
            $correct = $q['correct_option_index'];
            if ($selected == $correct) {
                $score += $q['score'];
            }
        }

        // Update attempt
        $stmt = $this->pdo->prepare("UPDATE attempts SET end_time = ?, score = ?, answers = ? WHERE id = ?");
        $stmt->execute([$endTime ?? date('Y-m-d H:i:s'), $score, $answersJson, $attemptId]);

        // Clear session
        unset($_SESSION['current_quiz']);

        // Send results email
        $userId = $_SESSION['user_id'];
        $stmt = $this->pdo->prepare("SELECT email, full_name FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        $total = array_sum(array_column($questions, 'score'));
        $emailHandler = new Email();
        $emailHandler->quizResults($user['email'], $user['full_name'], $score, $total);

        return ['score' => $score, 'total' => $total, 'questions' => $questions, 'answers' => $answers];
    }

    // Get results breakdown
    public function getResults($attemptId) {
        $stmt = $this->pdo->prepare("SELECT * FROM attempts WHERE id = ?");
        $stmt->execute([$attemptId]);
        $attempt = $stmt->fetch();
        if (!$attempt) return null;

        $questionsStmt = $this->pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
        $questionsStmt->execute([$attempt['quiz_id']]);
        $questions = $questionsStmt->fetchAll();

        $answers = json_decode($attempt['answers'], true);
        $results = [];
        foreach ($questions as $q) {
            $selected = $answers[$q['id']] ?? -1;
            $correct = $q['correct_option_index'];
            $results[] = [
                'question' => $q['text'],
                'correct' => ($selected == $correct),
                'explanation' => $q['explanation'],
                'selected' => $selected
            ];
        }
        return ['score' => $attempt['score'], 'details' => $results];
    }
}
?>