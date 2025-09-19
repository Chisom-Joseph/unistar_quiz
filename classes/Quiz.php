<?php
// classes/Quiz.php
require_once 'classes/Database.php';
require_once 'config/constants.php';

class Quiz {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function getAvailableQuizzes($userId) {
        $stmt = $this->pdo->prepare("
            SELECT q.id, q.name, q.attempts_allowed, q.timer_minutes, c.name AS course_name, 
                   COUNT(a.id) AS attempts_count
            FROM quizzes q
            LEFT JOIN courses c ON q.course_id = c.id
            LEFT JOIN attempts a ON q.id = a.quiz_id AND a.user_id = ?
            GROUP BY q.id
            HAVING attempts_count < q.attempts_allowed OR q.attempts_allowed = 0
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function startAttempt($userId, $quizId) {
        $stmt = $this->pdo->prepare("
            INSERT INTO attempts (user_id, quiz_id, start_time, answers)
            VALUES (?, ?, NOW(), '[]')
        ");
        $stmt->execute([$userId, $quizId]);
        return $this->pdo->lastInsertId();
    }

    public function submitAttempt($attemptId, $answers) {
        $stmt = $this->pdo->prepare("
            SELECT q.id AS question_id, q.correct_option_index, q.score
            FROM questions q
            WHERE q.quiz_id = (SELECT quiz_id FROM attempts WHERE id = ?)
        ");
        $stmt->execute([$attemptId]);
        $questions = $stmt->fetchAll();
        $totalScore = 0;
        foreach ($questions as $question) {
            $submittedAnswer = $answers[$question['question_id']] ?? -1;
            if ($submittedAnswer == $question['correct_option_index']) {
                $totalScore += $question['score'];
            }
        }
        $stmt = $this->pdo->prepare("
            UPDATE attempts
            SET end_time = NOW(), score = ?, answers = ?
            WHERE id = ?
        ");
        $stmt->execute([$totalScore, json_encode($answers), $attemptId]);
        return $totalScore;
    }

    public function getQuizDetails($quizId) {
        $stmt = $this->pdo->prepare("
            SELECT q.id, q.name, q.attempts_allowed, q.timer_minutes, c.name AS course_name
            FROM quizzes q
            LEFT JOIN courses c ON q.course_id = c.id
            WHERE q.id = ?
        ");
        $stmt->execute([$quizId]);
        return $stmt->fetch();
    }

    public function getQuestionsForQuiz($quizId) {
        $stmt = $this->pdo->prepare("
            SELECT id, text, options, explanation
            FROM questions
            WHERE quiz_id = ?
            ORDER BY id
        ");
        $stmt->execute([$quizId]);
        $questions = $stmt->fetchAll();
        foreach ($questions as &$question) {
            $question['options'] = json_decode($question['options'], true);
        }
        return $questions;
    }

    public function getAttemptDetails($attemptId) {
        $stmt = $this->pdo->prepare("
            SELECT a.score, a.answers, q.name AS quiz_name
            FROM attempts a
            LEFT JOIN quizzes q ON a.quiz_id = q.id
            WHERE a.id = ?
        ");
        $stmt->execute([$attemptId]);
        $attempt = $stmt->fetch();
        $attempt['answers'] = json_decode($attempt['answers'], true);
        return $attempt;
    }
}
?>