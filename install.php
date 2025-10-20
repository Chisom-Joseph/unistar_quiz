<?php
// install.php
require_once 'config/constants.php';
require_once 'classes/Database.php';

function outputError($message) {
    error_log("Install error: " . $message);
    die('<div style="color: red; font-weight: bold;">Installation failed: ' . htmlspecialchars($message) . '</div><br><a href="javascript:history.back()">Go Back</a>');
}

echo "<h3>Starting installation...</h3>";
error_log("Starting installation process, session_id=" . session_id());

// Initialize PDO without specifying a database
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET,
        DB_USER,
        DB_PASS,
        DB_OPTIONS
    );
    echo "<p>Connected to MySQL successfully.</p>";
} catch (PDOException $e) {
    outputError("Could not connect to MySQL server: " . $e->getMessage() . ". Check config/constants.php.");
}

// Create database
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE " . DB_NAME);
    echo "<p>Database '" . DB_NAME . "' created/selected successfully.</p>";
} catch (PDOException $e) {
    outputError("Could not create/use database: " . $e->getMessage());
}

// Execute schema.sql
$schemaFile = __DIR__ . '/sql/schema.sql';
if (!file_exists($schemaFile)) {
    outputError("Schema file not found: $schemaFile");
}

try {
    $schemaSql = file_get_contents($schemaFile);
    $statements = array_filter(array_map('trim', explode(';', $schemaSql)), fn($s) => !empty($s));
    $statementCount = count($statements);
    echo "<p>Applying $statementCount schema statements...</p>";
    foreach ($statements as $index => $statement) {
        try {
            $pdo->exec($statement);
            echo "<p>Statement " . ($index + 1) . " executed successfully.</p>";
        } catch (PDOException $e) {
            outputError("Failed to execute statement " . ($index + 1) . ": " . $e->getMessage() . "<br>Statement: " . htmlspecialchars(substr($statement, 0, 200)) . "...");
        }
    }
    echo "<p>Database schema applied successfully!</p>";
} catch (Exception $e) {
    outputError("Could not apply schema: " . $e->getMessage());
}

// Insert sample school, faculty, department data
try {
    $db = Database::getInstance();
    $db->exec("
        INSERT INTO schools (name) 
        VALUES ('Unistar University') 
        ON DUPLICATE KEY UPDATE name = name;

        INSERT INTO faculties (school_id, name) 
        VALUES 
        ((SELECT id FROM schools WHERE name = 'Unistar University'), 'Faculty of Science'),
        ((SELECT id FROM schools WHERE name = 'Unistar University'), 'Faculty of Arts') 
        ON DUPLICATE KEY UPDATE name = name;

        INSERT INTO departments (faculty_id, name) 
        VALUES 
        ((SELECT id FROM faculties WHERE name = 'Faculty of Science'), 'Computer Science'),
        ((SELECT id FROM faculties WHERE name = 'Faculty of Science'), 'Physics'),
        ((SELECT id FROM faculties WHERE name = 'Faculty of Arts'), 'English') 
        ON DUPLICATE KEY UPDATE name = name;
    ");
    echo "<p>Sample school data inserted successfully.</p>";
} catch (PDOException $e) {
    echo "<p>Warning: Could not insert sample school data: " . $e->getMessage() . "</p>";
}

// Insert sample quiz data
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
    echo "<p>Sample quiz data inserted successfully.</p>";
} catch (PDOException $e) {
    echo "<p>Warning: Could not insert sample quiz data: " . $e->getMessage() . "</p>";
}

// Create admin user
try {
    $adminPassword = 'admin123';
    $adminPasswordHash = password_hash($adminPassword, PASSWORD_BCRYPT);
    $db = Database::getInstance();
    $db->exec("
        INSERT INTO users (username, email, password_hash, full_name, is_active, is_verified, role) 
        VALUES ('admin', 'admin@unistar.test', '$adminPasswordHash', 'Admin User', 1, 1, 'admin')
        ON DUPLICATE KEY UPDATE email = email;
    ");
    echo "<p>Admin user created successfully: admin@unistar.test / admin123</p>";
} catch (PDOException $e) {
    echo "<p>Warning: Could not create admin user: " . $e->getMessage() . "</p>";
}

// Optionally delete install.php
if (unlink(__FILE__)) {
    error_log("install.php deleted successfully");
    echo "<p>install.php deleted for security.</p>";
} else {
    error_log("Failed to delete install.php");
    echo "<p>Warning: Could not delete install.php. Please delete manually for security.</p>";
}

// Success message and redirect
echo "<p><strong>Installation completed successfully!</strong></p>";
echo "<p>Redirecting to homepage in 3 seconds...</p>";
echo "<script>setTimeout(function() { window.location.href = '" . SITE_URL . "/?page=home&message=installed'; }, 3000);</script>";
exit;
?>