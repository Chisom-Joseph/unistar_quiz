<?php
// views/user/profile.php
ob_start();
require_once 'config/constants.php';
require_once 'includes/auth.php';
require_once 'classes/User.php';

$user = new User();

try {
    if (!$user->isLoggedIn()) {
        error_log("Unauthorized access to profile page, redirecting to login");
        header('Location: ' . SITE_URL . '/?page=login&error=not_logged_in');
        exit;
    }
    $userData = $user->getUserData($_SESSION['user_id']);

    // Handle profile update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
        try {
            validateCsrf($_POST['csrf'] ?? '');
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $full_name = trim($_POST['full_name']);
            $user->updateProfile($_SESSION['user_id'], $username, $email, $full_name);
            $success = "Profile updated successfully.";
            // Refresh user data
            $userData = $user->getUserData($_SESSION['user_id']);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }

    // Handle password change
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
        try {
            validateCsrf($_POST['csrf'] ?? '');
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            if ($new_password !== $confirm_password) {
                throw new Exception("New password and confirmation do not match");
            }
            $user->changePassword($_SESSION['user_id'], $current_password, $new_password);
            $success = "Password changed successfully.";
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
} catch (Exception $e) {
    error_log("Error in profile page: " . $e->getMessage());
    $error = $e->getMessage();
}
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">User Profile</h1>
        <button class="btn btn-primary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar">
            Menu
        </button>
    </div>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif (isset($success)): ?>
        <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <img src="<?php echo SITE_URL . "/public/uploads/" . htmlspecialchars($userData['profile_pic']); ?>" alt="Profile Picture" class="rounded-circle mb-3" style="object-fit: cover;" width="150" height="150">
                    <h5 class="card-title"><?php echo htmlspecialchars($userData['full_name']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($userData['username']); ?></p>
                    <p class="card-text"><?php echo htmlspecialchars($userData['email']); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Update Profile</h5>
                    <form method="POST" action="?page=profile" enctype="multipart/form-data">
                        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                        <input type="hidden" name="update_profile" value="1">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" id="username" value="<?php echo htmlspecialchars($userData['username']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="full_name" id="full_name" value="<?php echo htmlspecialchars($userData['full_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="profile_pic" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" name="profile_pic" id="profile_pic" accept="image/jpeg,image/png,image/gif">
                            <small class="form-text text-muted">Max 2MB, JPEG/PNG/GIF only</small>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Change Password</h5>
                    <form method="POST" action="?page=profile">
                        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                        <input type="hidden" name="change_password" value="1">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" name="current_password" id="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" name="new_password" id="new_password" required>
                            <small class="form-text text-muted">Minimum 8 characters</small>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'views/layouts/dashboard.php';
?>