<?php
// views/auth/register.php
ob_start();
require_once 'config/constants.php';
require_once 'includes/auth.php';
require_once 'classes/User.php';

$user = new User();
$error = '';
$success = '';

try {
    if ($user->isLoggedIn()) {
        $dashboardPage = $_SESSION['role'] === 'admin' ? 'admin_dashboard' : 'dashboard';
        error_log("Redirecting logged-in user_id: {$_SESSION['user_id']} from register to $dashboardPage");
        header('Location: ' . SITE_URL . '/?page=' . $dashboardPage);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Debug POST and FILES data
        error_log("POST data: " . print_r($_POST, true));
        error_log("FILES data: " . print_r($_FILES, true));

        try {
            validateCsrf($_POST['csrf'] ?? '');
            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
            $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';

            // Prepare data array for User::register
            $data = [
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'confirm_password' => $confirm_password,
                'full_name' => $full_name
            ];

            // Validate fields
            if ($username === '' || $email === '' || $password === '' || $full_name === '') {
                throw new Exception("All required fields must be filled");
            }
            if (strlen($password) < 8) {
                throw new Exception("Password must be at least 8 characters long");
            }
            if ($password !== $confirm_password) {
                throw new Exception("Passwords do not match");
            }

            // Register user (profile picture handled in User::register)
            $userId = $user->register($data);
            if ($userId) {
                $success = "Registration successful! Please log in.";
                error_log("Registration successful for email: $email, user_id: $userId, redirecting to login");
                header('Location: ' . SITE_URL . '/?page=login&success=registered');
                exit;
            }
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            $error = $e->getMessage();
        }
    }
} catch (Exception $e) {
    error_log("Error in register page: " . $e->getMessage());
    $error = "An unexpected error occurred";
}
error_log("CSRF token in register form: " . ($_SESSION['csrf_token'] ?? 'unset') . ", session_id=" . session_id());
?>
<main>
    <!-- signup-area-start -->
    <div class="it-signup-area pt-120 pb-120">
        <div class="container">
            <div class="it-signup-bg p-relative">
                <div class="it-signup-thumb d-none d-lg-block">
                    <img src="/public/img/contact/signup.jpg" alt="">
                </div>
                <div class="row">
                    <div class="col-xl-6 col-lg-6">
                        <form method="POST" action="?page=register" enctype="multipart/form-data">
                            <div class="it-signup-wrap">
                                <h4 class="it-signup-title">Sign Up</h4>
                                <?php if ($error): ?>
                                    <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
                                <?php endif; ?>
                                <div class="it-signup-input-wrap mb-40">
                                    <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                                    <input type="hidden" name="register" value="1">
                                    <div class="it-signup-input mb-20">
                                        <input type="text" placeholder="Full Name *" name="full_name" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" required>
                                    </div>
                                    <div class="it-signup-input mb-20">
                                        <input type="text" placeholder="Username *" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                                    </div>
                                    <div class="it-signup-input mb-20">
                                        <input type="email" placeholder="Email *" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                                    </div>
                                    <div class="it-signup-input mb-20">
                                        <input type="password" placeholder="Password *" name="password" required>
                                        <small class="form-text text-muted">Minimum 8 characters, including letters, numbers, and special characters</small>
                                    </div>
                                    <div class="it-signup-input mb-20">
                                        <input type="password" placeholder="Confirm Password *" name="confirm_password" required>
                                    </div>
                                    <div class="it-signup-input mb-20">
                                        <label for="profile_pic" class="form-label">Profile Picture (JPEG, PNG, GIF, max 2MB)</label>
                                        <input class="form-control" style="height: auto;" type="file" name="profile_pic" id="profile_pic" accept="image/jpeg,image/png,image/gif">
                                        <?php if (isset($_FILES['profile_pic']['name']) && !empty($_FILES['profile_pic']['name'])): ?>
                                            <div class="form-text text-primary">File selected: <?php echo htmlspecialchars($_FILES['profile_pic']['name']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="it-signup-btn mb-40">
                                    <button class="it-btn large" type="submit">Sign Up</button>
                                </div>
                                <div class="it-signup-text">
                                    <span>Already have an account? <a href="?page=login">Sign In</a></span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- signup-area-end -->
</main>
<?php
$content = ob_get_clean();
include 'views/layouts/main.php';
?>