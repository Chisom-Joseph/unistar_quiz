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
            error_log("Database connection failed: " . $e->getMessage());
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
}
?>