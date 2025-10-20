<?php
// views/auth/register.php
ob_start();
require_once 'config/constants.php';
require_once 'includes/auth.php';
require_once 'classes/User.php';

$user = new User();
$schools = $user->getSchools();
$faculties = $user->getAllFaculties();
$departments = $user->getAllDepartments();
$error = '';
$success = '';

try {
    if ($user->isLoggedIn()) {
        $dashboardPage = isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ? 'admin_dashboard' : 'dashboard';
        error_log("Redirecting logged-in user_id: {$_SESSION['user_id']} from register to $dashboardPage");
        header('Location: ' . SITE_URL . '/?page=' . $dashboardPage);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
        error_log("POST data: " . print_r($_POST, true));
        error_log("FILES data: " . print_r($_FILES, true));

        try {
            validateCsrf($_POST['csrf'] ?? '');
            $data = [
                'full_name' => trim($_POST['full_name'] ?? ''),
                'username' => trim($_POST['username'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'confirm_password' => $_POST['confirm_password'] ?? '',
                'school_id' => $_POST['school_id'] ?? '',
                'faculty_id' => $_POST['faculty_id'] ?? '',
                'department_id' => $_POST['department_id'] ?? '',
                'level' => $_POST['level'] ?? ''
            ];

            if (empty($data['full_name']) || empty($data['username']) || empty($data['email']) || empty($data['password']) ||
                empty($data['school_id']) || empty($data['faculty_id']) || empty($data['department_id']) || empty($data['level'])) {
                throw new Exception("All required fields must be filled");
            }
            if (strlen($data['password']) < 8) {
                throw new Exception("Password must be at least 8 characters long");
            }
            if ($data['password'] !== $data['confirm_password']) {
                throw new Exception("Passwords do not match");
            }

            $userId = $user->register($data);
            if ($userId) {
                $success = "Registration successful! Please check your email to verify your account.";
                error_log("Registration successful for email: {$data['email']}, user_id: $userId, redirecting to login");
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
                                <h4 class="it-signup-title">Register for quiz</h4>
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
                                        <select class="form-select" name="school_id" id="school_id" required>
                                            <option value="">Select School *</option>
                                            <?php foreach ($schools as $school): ?>
                                                <option value="<?php echo $school['id']; ?>" <?php echo isset($_POST['school_id']) && $_POST['school_id'] == $school['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($school['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="it-signup-input mb-20">
                                        <select class="form-select" name="faculty_id" id="faculty_id" required disabled>
                                            <option value="">Select Faculty *</option>
                                        </select>
                                    </div>
                                    <div class="it-signup-input mb-20">
                                        <select class="form-select" name="department_id" id="department_id" required disabled>
                                            <option value="">Select Department *</option>
                                        </select>
                                    </div>
                                    <div class="it-signup-input mb-20">
                                        <select class="form-select" name="level" id="level" required>
                                            <option value="">Select Level *</option>
                                            <option value="100" <?php echo isset($_POST['level']) && $_POST['level'] == '100' ? 'selected' : ''; ?>>100</option>
                                            <option value="200" <?php echo isset($_POST['level']) && $_POST['level'] == '200' ? 'selected' : ''; ?>>200</option>
                                            <option value="300" <?php echo isset($_POST['level']) && $_POST['level'] == '300' ? 'selected' : ''; ?>>300</option>
                                            <option value="400" <?php echo isset($_POST['level']) && $_POST['level'] == '400' ? 'selected' : ''; ?>>400</option>
                                            <option value="500" <?php echo isset($_POST['level']) && $_POST['level'] == '500' ? 'selected' : ''; ?>>500</option>
                                        </select>
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
    <script>
        // Faculty and department data
        const faculties = <?php echo json_encode($faculties); ?>;
        const departments = <?php echo json_encode($departments); ?>;

        // Dynamic faculty and department filtering
        document.getElementById('school_id').addEventListener('change', function() {
            const schoolId = this.value;
            const facultySelect = document.getElementById('faculty_id');
            const departmentSelect = document.getElementById('department_id');
            facultySelect.innerHTML = '<option value="">Select Faculty *</option>';
            departmentSelect.innerHTML = '<option value="">Select Department *</option>';
            departmentSelect.disabled = true;

            if (schoolId) {
                facultySelect.disabled = false;
                faculties.filter(f => f.school_id == schoolId).forEach(faculty => {
                    const option = document.createElement('option');
                    option.value = faculty.id;
                    option.textContent = faculty.name;
                    facultySelect.appendChild(option);
                });
            } else {
                facultySelect.disabled = true;
            }
        });

        document.getElementById('faculty_id').addEventListener('change', function() {
            const facultyId = this.value;
            const departmentSelect = document.getElementById('department_id');
            departmentSelect.innerHTML = '<option value="">Select Department *</option>';

            if (facultyId) {
                departmentSelect.disabled = false;
                departments.filter(d => d.faculty_id == facultyId).forEach(department => {
                    const option = document.createElement('option');
                    option.value = department.id;
                    option.textContent = department.name;
                    departmentSelect.appendChild(option);
                });
            } else {
                departmentSelect.disabled = true;
            }
        });
    </script>
</main>
<?php
$content = ob_get_clean();
include 'views/layouts/main.php';
?>