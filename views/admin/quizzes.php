<?php
// views/admin/quizzes.php
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
        error_log("Non-admin user_id: {$_SESSION['user_id']} attempted to access admin quizzes");
        header('Location: ' . SITE_URL . '/?page=dashboard');
        exit;
    }
    $quizStats = $admin->getQuizStats();
} catch (Exception $e) {
    error_log("Error in admin quizzes: " . $e->getMessage());
    $error = $e->getMessage();
}
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Manage Quizzes</h1>
        <button class="btn btn-primary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar">
            Menu
        </button>
    </div>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
    <?php else: ?>
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Quiz Statistics</h5>
                <?php if (empty($quizStats)): ?>
                    <p class="card-text">No quizzes found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Quiz ID</th>
                                    <th>Name</th>
                                    <th>Attempts</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($quizStats as $stat): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($stat['id']); ?></td>
                                        <td><?php echo htmlspecialchars($stat['name']); ?></td>
                                        <td><?php echo $stat['attempts']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include 'views/layouts/main.php';
?>