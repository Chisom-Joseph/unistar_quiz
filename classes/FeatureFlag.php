<?php
require_once 'config/database.php';

class FeatureFlag {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function get($flagName) {
        $stmt = $this->pdo->prepare("SELECT value FROM feature_flags WHERE flag_name = ?");
        $stmt->execute([$flagName]);
        $row = $stmt->fetch();
        return $row ? (bool)$row['value'] : false;
    }

    public function set($flagName, $value) {
        // Admin only check in controller
        $stmt = $this->pdo->prepare("INSERT INTO feature_flags (flag_name, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = ?");
        $stmt->execute([$flagName, (int)$value, (int)$value]);
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM feature_flags");
        return $stmt->fetchAll();
    }
}
?>