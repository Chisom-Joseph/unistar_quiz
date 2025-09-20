<?php
// views/admin/dashboard.php
ob_start();
require_once 'config/constants.php';
require_once 'includes/auth.php';
require_once 'classes/User.php';
require_once 'classes/Admin.php';

$user = new User();
$admin = new Admin();

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
<style>
    .hover-white-text-white:hover {
        color: white;
    }
</style>
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
				<div class="widget-stat card">
					<div class="card-body p-4">
						<div class="media ai-icon">
							<span class="me-3 bgl-primary text-primary">
								<!-- <i class="ti-user"></i> -->
								<svg id="icon-customers" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
									<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
									<circle cx="12" cy="7" r="4"></circle>
								</svg>
							</span>
							<div class="media-body">
								<p class="mb-1">Users</p>
								<h4 class="mb-0"><?php echo $totalUsers ?? 0; ?></h4>
								<a href="/?page=admin_users" class="badge badge-primary hover-white-text-white">Manage</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="widget-stat card">
					<div class="card-body p-4">
						<div class="media ai-icon">
							<span class="me-3 bgl-warning text-warning">
								<svg id="icon-orders" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text">
									<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
									<polyline points="14 2 14 8 20 8"></polyline>
									<line x1="16" y1="13" x2="8" y2="13"></line>
									<line x1="16" y1="17" x2="8" y2="17"></line>
									<polyline points="10 9 9 9 8 9"></polyline>
								</svg>
							</span>
							<div class="media-body">
                                <p class="mb-1">Quizzes</p>
								<h4 class="mb-0"><?php echo $totalQuizzes ?? 0; ?></h4>
                                <a href="/?page=admin_quizzes" class="badge badge-warning hover-white-text-white">Manage</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="widget-stat card">
					<div class="card-body p-4">
						<div class="media ai-icon">
							<span class="me-3 bgl-success text-success">
								<svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
									<ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
									<path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
									<path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
								</svg>
							</span>
							<div class="media-body">
								<p class="mb-1">Courses</p>
								<h4 class="mb-0"><?php echo $totalCourses ?? 0; ?></h4>
                                <a href="/?page=admin_courses" class="badge badge-success hover-white-text-white">Manage</a>
							</div>
						</div>
					</div>
				</div>
			</div>
        </div>
    <?php endif; ?>
</div>


<?php
$content = ob_get_clean();
include 'views/layouts/dashboard.php';
?>