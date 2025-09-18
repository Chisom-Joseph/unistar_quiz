<?php
// views/admin/dashboard.php
ob_start();
require_once 'config/constants.php';
if (!isset($_SESSION['user_id'])) {
    error_log("Unauthorized access to admin dashboard, redirecting to login");
    header('Location: ' . SITE_URL . '/?page=login&error=not_logged_in');
    exit;
}
$userData = $user->getUserData($_SESSION['user_id']);
if ($userData['role'] !== 'admin') {
    error_log("Non-admin user_id: " . $_SESSION['user_id'] . " attempted to access admin dashboard");
    header('Location: ' . SITE_URL . '/?page=dashboard');
    exit;
}
error_log("Admin dashboard loaded for user_id: " . $_SESSION['user_id']);
?>
<h1>Admin Dashboard - <?php echo htmlspecialchars($userData['full_name']); ?></h1>
<p>Email: <?php echo htmlspecialchars($userData['email']); ?></p>
<p><a href="?page=logout">Logout</a></p>
<h2>Admin Actions</h2>
<ul>
    <li><a href="?page=admin_users">Manage Users</a></li>
    <li><a href="?page=admin_quizzes">Manage Quizzes</a></li>
    <li><a href="?page=admin_courses">Manage Courses</a></li>
</ul>
<?php
$content = ob_get_clean();
include 'views/layouts/main.php';
?>