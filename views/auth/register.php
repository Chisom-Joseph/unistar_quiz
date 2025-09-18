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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Quiz App</title>
</head>
<body>
<h1>Register</h1>
<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>
<form method="POST" action="?page=register" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>
    <label for="confirm_password">Confirm Password:</label>
    <input type="password" name="confirm_password" id="confirm_password" required>
    <label for="full_name">Full Name:</label>
    <input type="text" name="full_name" id="full_name" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" required>
    <label for="profile_pic">Profile Picture (JPEG, PNG, GIF, max 2MB):</label>
    <input type="file" name="profile_pic" id="profile_pic" accept="image/jpeg,image/png,image/gif">
    <?php if (isset($_FILES['profile_pic']['name']) && !empty($_FILES['profile_pic']['name'])): ?>
        <p style="color: blue;">File selected: <?php echo htmlspecialchars($_FILES['profile_pic']['name']); ?></p>
    <?php endif; ?>
    <button type="submit">Register</button>
    <p>Already have an account? <a href="?page=login">Login</a></p>
</form>
</body>
</html>
<?php
$content = ob_get_clean();
include 'views/layouts/main.php';
?>