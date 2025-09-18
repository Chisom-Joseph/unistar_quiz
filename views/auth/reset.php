<?php
// views/auth/reset.php
ob_start();
?>
<h1>Forgot Password</h1>
<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>
<?php if (isset($success)): ?>
    <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
<?php endif; ?>
<form method="POST" action="?page=forgot">
    <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>
    <button type="submit">Send Reset Link</button>
    <p><a href="?page=login">Back to Login</a></p>
</form>
<?php
$content = ob_get_clean();
include 'views/layouts/main.php';
?>