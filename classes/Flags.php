<?php
// classes/Flags.php
require_once 'classes/Database.php';

class Flags {
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

    public function getAll() {
        $stmt = $this->pdo->prepare("SELECT * FROM feature_flags");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function set($flagName, $value) {
        $stmt = $this->pdo->prepare("UPDATE feature_flags SET value = ? WHERE flag_name = ?");
        $stmt->execute([(int)$value, $flagName]);
    }
}
?>