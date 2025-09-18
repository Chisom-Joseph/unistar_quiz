<?php
// views/auth/login.php
ob_start();
require_once 'config/constants.php';
require_once 'includes/auth.php';
error_log("Loading login page, error=" . ($_GET['error'] ?? 'none') . ", session_id=" . session_id());
?>
<div class="card shadow-sm">
    <div class="card-body">
        <h1 class="card-title text-center mb-4">Login</h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif (isset($_GET['message']) && $_GET['message'] === 'logged_out'): ?>
            <div class="alert alert-success" role="alert">You have been logged out successfully.</div>
        <?php elseif (isset($_GET['error']) && $_GET['error'] === 'not_logged_in'): ?>
            <div class="alert alert-danger" role="alert">Please log in to access the dashboard.</div>
        <?php endif; ?>
        <form method="POST" action="?page=login">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" id="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
            <div class="mt-3 text-center">
                <p>Forgot password? <a href="?page=forgot">Reset Password</a></p>
                <p>Don't have an account? <a href="?page=register">Register</a></p>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'views/layouts/main.php';
?>