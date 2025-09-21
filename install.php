<?php
// install.php
require_once 'config/constants.php';
require_once 'classes/Database.php';

error_log("Starting installation process, session_id=" . session_id());

// Initialize PDO without specifying a database (for creating unistar_quiz)
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET,
        DB_USER,
        DB_PASS,
        DB_OPTIONS
    );
} catch (PDOException $e) {
    error_log("Failed to connect to MySQL server: " . $e->getMessage());
    die('Installation failed: Could not connect to MySQL server. Check config/constants.php.');
}

// Create database
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE " . DB_NAME);
} catch (PDOException $e) {
    error_log("Failed to create/use database: " . $e->getMessage());
    die('Installation failed: Could not create database.');
}

// Execute schema.sql
$schemaFile = __DIR__ . '/sql/schema.sql';
if (!file_exists($schemaFile)) {
    error_log("Schema file not found: $schemaFile");
    die('Installation failed: Schema file not found.');
}

try {
    $schemaSql = file_get_contents($schemaFile);
    $pdo->exec($schemaSql);
    error_log("Database schema applied successfully");
} catch (PDOException $e) {
    error_log("Failed to apply schema: " . $e->getMessage());
    die('Installation failed: Could not apply schema.');
}

// Insert sample quiz data using Database class
try {
    $db = Database::getInstance();
    $db->exec("
        INSERT INTO courses (name, description) 
        VALUES ('General Knowledge', 'A course on various topics')
        ON DUPLICATE KEY UPDATE name = name;

        INSERT INTO quizzes (course_id, name, attempts_allowed, timer_minutes) 
        VALUES (1, 'Basic Trivia', 3, 0)
        ON DUPLICATE KEY UPDATE name = name;

        INSERT INTO questions (quiz_id, text, options, correct_option_index, explanation, score) 
        VALUES 
        (1, 'What is the capital of France?', '[\"Paris\", \"London\", \"Berlin\", \"Madrid\"]', 0, 'Paris is the capital city of France.', 5),
        (1, 'Which planet is known as the Red Planet?', '[\"Jupiter\", \"Mars\", \"Venus\", \"Mercury\"]', 1, 'Mars is called the Red Planet due to its reddish appearance.', 5)
        ON DUPLICATE KEY UPDATE text = text;
    ");
    error_log("Sample quiz data inserted successfully");
} catch (PDOException $e) {
    error_log("Failed to insert sample quiz data: " . $e->getMessage());
    die('Installation failed: Could not insert sample quiz data.');
}

// Create admin user
try {
    $adminPassword = 'admin123';
    $adminPasswordHash = password_hash($adminPassword, PASSWORD_BCRYPT);
    $db->exec("
        INSERT INTO users (username, email, password_hash, full_name, is_active, is_verified, role) 
        VALUES ('admin', 'admin@quizapp.test', '$adminPasswordHash', 'Admin User', 1, 1, 'admin')
        ON DUPLICATE KEY UPDATE email = email;
    ");
    error_log("Admin user created successfully");
} catch (PDOException $e) {
    error_log("Failed to create admin user: " . $e->getMessage());
    die('Installation failed: Could not create admin user.');
}

// Optionally delete install.php for security
if (unlink(__FILE__)) {
    error_log("install.php deleted successfully");
} else {
    error_log("Failed to delete install.php");
}

// Redirect to homepage
header('Location: ' . SITE_URL . '/?page=home&message=installed');
exit;
?>