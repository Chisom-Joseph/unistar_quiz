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
                            <h4 class="it-signup-title">sign up</h4>
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            <div class="it-signup-input-wrap mb-40">
                                <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                                <div class="it-signup-input mb-20">
                                    <input type="text" placeholder="Full Name *" name="full_name" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" required>
                                </div>
                                <div class="it-signup-input mb-20">
                                    <input type="text" placeholder="Full Name *" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                                </div>
                                <div class="it-signup-input mb-20">
                                    <input type="email" placeholder="Email *" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                                </div>
                                <div class="it-signup-input mb-20">
                                    <input type="password" placeholder="Password *" name="password" required>
                                </div>
                                <div class="it-signup-input mb-20">
                                    <input type="password" placeholder="Confirm Password *" name="confirm_password" required>
                                </div>
                                <div class="it-signup-inputd mb-20">
                                    <label for="profile_pic" class="form-label">Profile Picture (JPEG, PNG, GIF, max 2MB)</label>
                                    <input class="form-control" style="height: auto;" type="file" name="profile_pic" placeholder="Profile Picture (JPEG, PNG, GIF, max 2MB)" accept="image/jpeg,image/png,image/gif" required>
                                    <?php if (isset($_FILES['profile_pic']['name']) && !empty($_FILES['profile_pic']['name'])): ?>
                                        <div class="form-text text-primary">File selected: <?php echo htmlspecialchars($_FILES['profile_pic']['name']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="it-signup-btn mb-40">
                                <button class="it-btn large">sign up</button>
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