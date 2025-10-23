<?php
// index.php --
session_start();
require_once 'config/constants.php';
require_once 'includes/auth.php';
require_once 'classes/User.php';
require_once 'classes/Admin.php';
require_once 'classes/Quiz.php';
require_once 'classes/Payment.php';

$user = new User();
$admin = new Admin();
$quiz = new Quiz();
$payment = new Payment();

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    if (!$user->isLoggedIn() && !in_array($page, ['home', 'about', 'contact', 'login', 'register', 'verify', 'forgot_password', 'reset_password'])) {
        error_log("Unauthenticated access to page=$page, redirecting to login");
        header('Location: ' . SITE_URL . '/?page=login&error=not_logged_in');
        exit;
    }

    switch ($page) {
        case 'home':
            include 'views/home.php';
            break;
        case 'about':
            include 'views/about.php';
            break;
        case 'contact':
            include 'views/contact.php';
            break;
        case 'login':
            include 'views/auth/login.php';
            break;
        case 'register':
            include 'views/auth/register.php';
            break;
        case 'verify':
            include 'views/auth/verify.php';
            break;
        case 'forgot_password':
            include 'views/auth/forgot_password.php';
            break;
        case 'reset_password':
            include 'views/auth/reset_password.php';
            break;
        case 'dashboard':
            include 'views/user/dashboard.php';
            break;
        case 'profile':
            include 'views/user/profile.php';
            break;
        case 'quiz':
            include 'views/user/quiz.php';
            break;
        case 'quiz_results':
            include 'views/user/quiz_results.php';
            break;
        case 'admin_dashboard':
            if (!Admin::isAdmin($_SESSION['user_id'])) {
                error_log("Unauthorized access to admin dashboard, user_id: {$_SESSION['user_id']}");
                header('Location: ' . SITE_URL . '/?page=login&error=not_authorized');
                exit;
            }
            include 'views/admin/dashboard.php';
            break;
        case 'admin_users':
            if (!Admin::isAdmin($_SESSION['user_id'])) {
                error_log("Unauthorized access to admin users, user_id: {$_SESSION['user_id']}");
                header('Location: ' . SITE_URL . '/?page=login&error=not_authorized');
                exit;
            }
            include 'views/admin/users.php';
            break;
        case 'admin_quizzes':
            if (!Admin::isAdmin($_SESSION['user_id'])) {
                error_log("Unauthorized access to admin quizzes, user_id: {$_SESSION['user_id']}");
                header('Location: ' . SITE_URL . '/?page=login&error=not_authorized');
                exit;
            }
            include 'views/admin/quizzes.php';
            break;
        case 'admin_courses':
            if (!Admin::isAdmin($_SESSION['user_id'])) {
                error_log("Unauthorized access to admin courses, user_id: {$_SESSION['user_id']}");
                header('Location: ' . SITE_URL . '/?page=login&error=not_authorized');
                exit;
            }
            include 'views/admin/courses.php';
            break;
        case 'admin_questions':
            if (!Admin::isAdmin($_SESSION['user_id'])) {
                error_log("Unauthorized access to admin questions, user_id: {$_SESSION['user_id']}");
                header('Location: ' . SITE_URL . '/?page=login&error=not_authorized');
                exit;
            }
            include 'views/admin/questions.php';
            break;
        case 'logout':
            $user->logout();
            header('Location: ' . SITE_URL . '/?page=login&message=logged_out');
            exit;
        default:
            error_log("Unknown page requested: $page");
            include 'views/errors/404.php';
            break;
    }
} catch (Exception $e) {
    error_log("Error in index.php: " . $e->getMessage());
    include 'views/errors/500.php';
}
?>