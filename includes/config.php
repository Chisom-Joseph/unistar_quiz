// includes/config.php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');  // Change in production
define('DB_PASS', '');      // Change in production
define('DB_NAME', 'quiz_app');
define('SITE_URL', 'https://yourdomain.com/');  // Enforce HTTPS
define('PAYSTACK_SECRET_KEY', 'sk_test_yourkey');  // From PayStack dashboard
define('PAYMENT_AMOUNT', 500000);  // In kobo (₦5000)
define('EMAIL_FROM', 'no-reply@yourdomain.com');
define('SESSION_TIMEOUT', 1800);  // 30 minutes

// Error logging
ini_set('display_errors', 0);  // Off in production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/errors.log');

// HTTPS enforcement
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit;
}
?>