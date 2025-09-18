<?php
// index.php
require_once 'config/constants.php';
require_once 'includes/auth.php';
require_once 'classes/User.php';
require_once 'classes/Quiz.php';
require_once 'classes/Admin.php';
require_once 'classes/Payment.php';
require_once 'classes/Flags.php';

$page = $_GET['page'] ?? 'home';
error_log("Requested page: $page, session_id=" . session_id() . ", user_id=" . ($_SESSION['user_id'] ?? 'none'));

$user = new User();
$quiz = new Quiz();
$admin = new Admin();
$payment = new Payment();

// Redirect logged-in users from auth pages
if (isset($_SESSION['user_id']) && in_array($page, ['login', 'register', 'verify', 'forgot', 'reset'])) {
    try {
        $userData = $user->getUserData($_SESSION['user_id']);
        $dashboardPage = $userData['role'] === 'admin' ? 'admin_dashboard' : 'dashboard';
        error_log("Redirecting logged-in user_id: {$_SESSION['user_id']} from $page to $dashboardPage");
        header('Location: ' . SITE_URL . '/?page=' . $dashboardPage);
        exit;
    } catch (Exception $e) {
        error_log("Invalid user_id: {$_SESSION['user_id']} on $page, clearing session: " . $e->getMessage());
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

switch ($page) {
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("Register submission: session_id=" . session_id() . ", submitted_csrf=" . ($_POST['csrf'] ?? 'none') . ", post_data=" . json_encode($_POST));
            try {
                validateCsrf($_POST['csrf'] ?? '');
                $userId = $user->register($_POST);
                header('Location: ' . SITE_URL . '/?page=verify&user=' . $userId);
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        include 'views/auth/register.php';
        break;
    case 'login':
        error_log("Loading login page");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("Login submission: session_id=" . session_id() . ", submitted_csrf=" . ($_POST['csrf'] ?? 'none') . ", post_data=" . json_encode($_POST));
            try {
                validateCsrf($_POST['csrf'] ?? '');
                if (!isset($_POST['email']) || !isset($_POST['password'])) {
                    throw new Exception('Email and password are required');
                }
                $userId = $user->login($_POST['email'], $_POST['password']);
                $userData = $user->getUserData($userId);
                $dashboardPage = $userData['role'] === 'admin' ? 'admin_dashboard' : 'dashboard';
                header('Location: ' . SITE_URL . '/?page=' . $dashboardPage);
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        include 'views/auth/login.php';
        break;
    case 'logout':
        error_log("Logout initiated for user_id: " . ($_SESSION['user_id'] ?? 'none') . ", session_id=" . session_id());
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        error_log("New CSRF token generated after logout: " . $_SESSION['csrf_token'] . ", session_id=" . session_id());
        header('Location: ' . SITE_URL . '/?page=login&message=logged_out');
        exit;
        break;
    case 'dashboard':
        error_log("Loading user dashboard page");
        if (!isset($_SESSION['user_id'])) {
            error_log("Unauthorized access to dashboard, redirecting to login");
            header('Location: ' . SITE_URL . '/?page=login&error=not_logged_in');
            exit;
        }
        $userData = $user->getUserData($_SESSION['user_id']);
        if ($userData['role'] === 'admin') {
            error_log("Admin user_id: {$_SESSION['user_id']} redirected to admin_dashboard");
            header('Location: ' . SITE_URL . '/?page=admin_dashboard');
            exit;
        }
        include 'views/user/dashboard.php';
        break;
    case 'admin_dashboard':
        error_log("Loading admin dashboard page");
        if (!isset($_SESSION['user_id'])) {
            error_log("Unauthorized access to admin dashboard, redirecting to login");
            header('Location: ' . SITE_URL . '/?page=login&error=not_logged_in');
            exit;
        }
        $userData = $user->getUserData($_SESSION['user_id']);
        if ($userData['role'] !== 'admin') {
            error_log("Non-admin user_id: {$_SESSION['user_id']} attempted to access admin dashboard");
            header('Location: ' . SITE_URL . '/?page=dashboard');
            exit;
        }
        include 'views/admin/dashboard.php';
        break;
    default:
        error_log("Default case triggered for page: $page");
        include 'views/home.php';
        break;
}
?>