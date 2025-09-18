<?php
// views/auth/register.php
ob_start();
require_once 'config/constants.php';
require_once 'includes/auth.php';
if (isset($_SESSION['user_id'])) {
    $user = new User();
    try {
        $userData = $user->getUserData($_SESSION['user_id']);
        $dashboardPage = $userData['role'] === 'admin' ? 'admin_dashboard' : 'dashboard';
        error_log("Redirecting logged-in user_id: {$_SESSION['user_id']} from register to $dashboardPage");
        header('Location: ' . SITE_URL . '/?page=' . $dashboardPage);
        exit;
    } catch (Exception $e) {
        error_log("Error fetching user data for user_id: {$_SESSION['user_id']}, redirecting to login: " . $e->getMessage());
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
error_log("CSRF token in register form: " . ($_SESSION['csrf_token'] ?? 'unset') . ", session_id=" . session_id());
error_log("Form method: " . $_SERVER['REQUEST_METHOD']);
?>
<div class="card shadow-sm">
    <div class="card-body">
        <h1 class="card-title text-center mb-4">Register</h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="?page=register" enctype="multipart/form-data">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" name="username" id="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" id="password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
            </div>
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" name="full_name" id="full_name" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="profile_pic" class="form-label">Profile Picture (JPEG, PNG, GIF, max 2MB)</label>
                <input type="file" class="form-control" name="profile_pic" id="profile_pic" accept="image/jpeg,image/png,image/gif">
                <?php if (isset($_FILES['profile_pic']['name']) && !empty($_FILES['profile_pic']['name'])): ?>
                    <div class="form-text text-primary">File selected: <?php echo htmlspecialchars($_FILES['profile_pic']['name']); ?></div>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
            <div class="mt-3 text-center">
                <p>Already have an account? <a href="?page=login">Login</a></p>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'views/layouts/main.php';
?>