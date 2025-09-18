<?php
// classes/Admin.php
require_once 'classes/Database.php';
require_once 'classes/Flags.php';
require_once 'config/constants.php';

class Admin {
    private $pdo;
    public $flags;

    public function __construct() {
        try {
            $this->pdo = Database::getInstance();
            $this->flags = new Flags();
        } catch (\Throwable $th) {
            error_log("Database connection failed: " . $th->getMessage());
            throw new Exception("Database connection failed");
        }
    }

    public function getTotalQuizzes() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM quizzes");
        return $stmt->fetchColumn();
    }

    public function getTotalCourses() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM courses");
        return $stmt->fetchColumn();
    }

    public static function isAdmin($userId) {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row && $row['role'] === 'admin';
    }

    public function getTotalUsers() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getActiveUsers() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE is_active = 1");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getQuizStats() {
        $stmt = $this->pdo->prepare("SELECT q.id, q.name, COUNT(a.id) as attempts 
            FROM quizzes q LEFT JOIN attempts a ON q.id = a.quiz_id 
            GROUP BY q.id, q.name");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllUsers() {
        $stmt = $this->pdo->prepare("SELECT * FROM users ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function toggleUserStatus($userId, $active) {
        $stmt = $this->pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
        $stmt->execute([(int)$active, $userId]);
    }

    public function createCourse($name, $description) {
        $stmt = $this->pdo->prepare("INSERT INTO courses (name, description) VALUES (?, ?)");
        $stmt->execute([$name, $description]);
    }

    public function exportUsersCSV() {
        $stmt = $this->pdo->prepare("SELECT username, email, full_name, created_at FROM users");
        $stmt->execute();
        $users = $stmt->fetchAll();
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Username', 'Email', 'Full Name', 'Created At']);
        foreach ($users as $user) {
            fputcsv($out, [$user['username'], $user['email'], $user['full_name'], $user['created_at']]);
        }
        fclose($out);
    }

    public function getAllCourses() {
        $stmt = $this->pdo->prepare("SELECT * FROM courses ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllQuizzes() {
        $stmt = $this->pdo->prepare("
            SELECT q.id, q.name, q.course_id, q.attempts_allowed, q.timer_minutes, 
                   c.name AS course_name, COUNT(a.id) AS attempts
            FROM quizzes q
            LEFT JOIN courses c ON q.course_id = c.id
            LEFT JOIN attempts a ON q.id = a.quiz_id
            GROUP BY q.id, q.name, q.course_id, q.attempts_allowed, q.timer_minutes, c.name
            ORDER BY q.id DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function createQuiz($course_id, $name, $attempts_allowed, $timer_minutes) {
        $stmt = $this->pdo->prepare("
            INSERT INTO quizzes (course_id, name, attempts_allowed, timer_minutes)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([(int)$course_id, $name, (int)$attempts_allowed, (int)$timer_minutes]);
    }

    public function updateQuiz($quiz_id, $course_id, $name, $attempts_allowed, $timer_minutes) {
        $stmt = $this->pdo->prepare("
            UPDATE quizzes 
            SET course_id = ?, name = ?, attempts_allowed = ?, timer_minutes = ?
            WHERE id = ?
        ");
        $stmt->execute([(int)$course_id, $name, (int)$attempts_allowed, (int)$timer_minutes, (int)$quiz_id]);
    }

    public function deleteQuiz($quiz_id) {
        $stmt = $this->pdo->prepare("DELETE FROM attempts WHERE quiz_id = ?");
        $stmt->execute([(int)$quiz_id]);
        $stmt = $this->pdo->prepare("DELETE FROM questions WHERE quiz_id = ?");
        $stmt->execute([(int)$quiz_id]);
        $stmt = $this->pdo->prepare("DELETE FROM quizzes WHERE id = ?");
        $stmt->execute([(int)$quiz_id]);
    }

    public function getQuestionsByQuiz($quiz_id) {
        $stmt = $this->pdo->prepare("
            SELECT id, text, options, correct_option_index, score, explanation
            FROM questions
            WHERE quiz_id = ?
            ORDER BY id
        ");
        $stmt->execute([(int)$quiz_id]);
        $questions = $stmt->fetchAll();
        foreach ($questions as &$question) {
            $question['options'] = json_decode($question['options'], true);
        }
        return $questions;
    }

    public function getAllQuestions() {
        $stmt = $this->pdo->prepare("
            SELECT q.id, q.quiz_id, q.text, q.options, q.correct_option_index, q.score, q.explanation, 
                   z.name AS quiz_name
            FROM questions q
            LEFT JOIN quizzes z ON q.quiz_id = z.id
            ORDER BY q.id DESC
        ");
        $stmt->execute();
        $questions = $stmt->fetchAll();
        foreach ($questions as &$question) {
            $question['options'] = json_decode($question['options'], true);
        }
        return $questions;
    }

    public function createQuestion($quiz_id, $text, $correct_option_index, $options, $score, $explanation) {
        if (!is_array($options) || count(array_filter($options, 'strlen')) < 2 || !in_array($correct_option_index, [0, 1, 2, 3])) {
            throw new Exception("Invalid question data: At least 2 non-empty options required, and correct_option_index must be 0–3");
        }
        $options = array_slice($options, 0, 4); // Ensure max 4 options
        $stmt = $this->pdo->prepare("
            INSERT INTO questions (quiz_id, text, options, correct_option_index, score, explanation)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([(int)$quiz_id, $text, json_encode($options), (int)$correct_option_index, (int)$score, $explanation]);
    }

    public function updateQuestion($question_id, $text, $correct_option_index, $options, $score, $explanation) {
        if (!is_array($options) || count(array_filter($options, 'strlen')) < 2 || !in_array($correct_option_index, [0, 1, 2, 3])) {
            throw new Exception("Invalid question data: At least 2 non-empty options required, and correct_option_index must be 0–3");
        }
        $options = array_slice($options, 0, 4); // Ensure max 4 options
        $stmt = $this->pdo->prepare("
            UPDATE questions 
            SET text = ?, options = ?, correct_option_index = ?, score = ?, explanation = ?
            WHERE id = ?
        ");
        $stmt->execute([$text, json_encode($options), (int)$correct_option_index, (int)$score, $explanation, (int)$question_id]);
    }

    public function deleteQuestion($question_id) {
        $stmt = $this->pdo->prepare("DELETE FROM questions WHERE id = ?");
        $stmt->execute([(int)$question_id]);
    }
}
?>