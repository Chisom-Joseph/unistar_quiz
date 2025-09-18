<?php
ob_start();
require_once 'config/constants.php';
require_once 'includes/auth.php';
require_once 'classes/User.php';
require_once 'classes/Admin.php';

$user = new User();
$admin = new Admin();feat(admin): enhance dashboard with metrics and Bootstrap UI

- Integrated User and Admin classes for data fetching
- Added role validation and improved error handling
- Displayed key metrics: total users, quizzes, courses, and active users
- Replaced plain HTML with Bootstrap cards and responsive layout
- Added offcanvas menu trigger for mobile navigation


try {
    $userData = $user->getUserData($_SESSION['user_id']);
    if ($userData['role'] !== 'admin') {
        error_log("Non-admin user_id: {$_SESSION['user_id']} attempted to access admin dashboard");
        header('Location: ' . SITE_URL . '/?page=dashboard');
        exit;
    }
    // Fetch metrics
    $totalUsers = $admin->getTotalUsers();
    $totalQuizzes = $admin->getTotalQuizzes();
    $totalCourses = $admin->getTotalCourses();
    $activeUsers = $admin->getActiveUsers();
} catch (Exception $e) {
    error_log("Error in admin dashboard: " . $e->getMessage());
    $error = $e->getMessage();
}
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Admin Dashboard</h1>
        <button class="btn btn-primary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar">
            Menu
        </button>
    </div>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <p class="card-text display-4"><?php echo $totalUsers ?? 0; ?></p>
                        <p class="card-text">Active Users: <?php echo $activeUsers ?? 0; ?></p>
                        <a href="<?php echo SITE_URL; ?>/?page=admin_users" class="btn btn-outline-primary">Manage Users</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Total Quizzes</h5>
                        <p class="card-text display-4"><?php echo $totalQuizzes ?? 0; ?></p>
                        <a href="<?php echo SITE_URL; ?>/?page=admin_quizzes" class="btn btn-outline-primary">Manage Quizzes</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Total Courses</h5>
                        <p class="card-text display-4"><?php echo $totalCourses ?? 0; ?></p>
                        <a href="<?php echo SITE_URL; ?>/?page=admin_courses" class="btn btn-outline-primary">Manage Courses</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include 'views/layouts/main.php';
?>