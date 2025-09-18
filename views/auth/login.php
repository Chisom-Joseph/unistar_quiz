<?php
// views/auth/login.php
ob_start();
require_once 'config/constants.php';
require_once 'includes/auth.php';
error_log("Loading login page, error=" . ($_GET['error'] ?? 'none') . ", session_id=" . session_id());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Quiz App</title>
</head>
<body>
<h1>Login</h1>
<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
<?php elseif (isset($_GET['message']) && $_GET['message'] === 'logged_out'): ?>
    <p style="color: green;">You have been logged out successfully.</p>
<?php elseif (isset($_GET['error']) && $_GET['error'] === 'not_logged_in'): ?>
    <p style="color: red;">Please log in to access the dashboard.</p>
<?php endif; ?>
<form method="POST" action="?page=login">
    <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>
    <button type="submit">Login</button>
    <p>Forgot password? <a href="?page=forgot">Reset Password</a></p>
    <p>Don't have an account? <a href="?page=register">Register</a></p>
</form>
</body>
</html>
<?php
$content = ob_get_clean();
include 'views/layouts/main.php';
?>