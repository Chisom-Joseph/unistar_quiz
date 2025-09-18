<?php
// includes/auth.php
require_once 'config/constants.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    error_log("New CSRF token generated: " . $_SESSION['csrf_token'] . ", session_id=" . session_id());
}

function validateCsrf($token) {
    if (empty($token)) {
        error_log("CSRF validation failed: No token provided, session_id=" . session_id() . ", session_token=" . ($_SESSION['csrf_token'] ?? 'unset'));
        throw new Exception('CSRF token missing');
    }
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        error_log("CSRF validation failed: submitted='$token', session_token=" . ($_SESSION['csrf_token'] ?? 'unset') . ", session_id=" . session_id());
        throw new Exception('CSRF token invalid');
    }
    error_log("CSRF validation succeeded: token=$token, session_id=" . session_id());
    // Regenerate CSRF token after successful validation
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    error_log("New CSRF token generated post-validation: " . $_SESSION['csrf_token'] . ", session_id=" . session_id());
}

// Session timeout handling (only for authenticated users)
if (isset($_SESSION['user_id']) && isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    error_log("Session timed out for user_id: " . $_SESSION['user_id']);
    session_unset();
    session_destroy();
    session_start();
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    error_log("New CSRF token generated after timeout: " . $_SESSION['csrf_token'] . ", session_id=" . session_id());
}
$_SESSION['last_activity'] = time();
?>