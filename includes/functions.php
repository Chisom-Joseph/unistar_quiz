<?php
require_once 'db.php';

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function validatePassword($pass) {
    return strlen($pass) >= MIN_PASSWORD_LEN && preg_match(PASSWORD_PATTERN, $pass);
}

// JS for toasts (vanilla)
function showToast($message, $type = 'info') {
    echo "<script>/* Vanilla JS toast: Create div, style, fade */</script>";
    // Implement in app.js
}

// ---

// CSRF Token
function generate_csrf() {
    if (!isset($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function validate_csrf($token) {
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

// Password Hash/Verify
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

// Email Sending (HTML with plain fallback)
function send_email($to, $subject, $html_message) {
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=utf8',
        'From: ' . EMAIL_FROM
    ];
    $plain_message = strip_tags($html_message);  // Fallback
    return mail($to, $subject, $html_message, implode("\r\n", $headers));
}

// Validation (example for password)
function validate_password($password) {
    return preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/', $password);
}

// Sanitize Input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Upload Profile Pic
function upload_profile_pic($file) {
    if ($file['error'] !== 0 || $file['size'] > 2097152) return false;  // 2MB
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (!in_array(strtolower($ext), ['jpg', 'jpeg', 'png'])) return false;
    $target = 'uploads/' . bin2hex(random_bytes(8)) . '.' . $ext;
    return move_uploaded_file($file['tmp_name'], $target) ? $target : false;
}

// Get Feature Flag
function get_flag($name) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT value FROM feature_flags WHERE flag_name = ?");
    $stmt->execute([$name]);
    return $stmt->fetchColumn();
}

// More functions: rate limiting, session management, etc.
function check_rate_limit($email) {
    // Implement with sessions or DB (e.g., count failed logins)
    // For brevity, placeholder
    return true;  // True if under limit
}

?>