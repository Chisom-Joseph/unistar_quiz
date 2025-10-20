<?php
// views/admin/users.php
ob_start();
require_once 'config/constants.php';
require_once 'classes/User.php';
require_once 'classes/Admin.php';

$user = new User();
$admin = new Admin();
if (!Admin::isAdmin($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/?page=login&error=not_authorized');
    exit;
}

$users = $user->pdo->query("
    SELECT u.*, s.name AS school_name, f.name AS faculty_name, d.name AS department_name
    FROM users u
    LEFT JOIN schools s ON u.school_id = s.id
    LEFT JOIN faculties f ON u.faculty_id = f.id
    LEFT JOIN departments d ON u.department_id = d.id
")->fetchAll(PDO::FETCH_ASSOC);

$schools = $user->getSchools();
$faculties = $user->getAllFaculties();
$departments = $user->getAllDepartments();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    try {
        $userId = $_POST['user_id'] ?? 0;
        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'full_name' => trim($_POST['full_name'] ?? ''),
            'school_id' => $_POST['school_id'] ?? null,
            'faculty_id' => $_POST['faculty_id'] ?? null,
            'department_id' => $_POST['department_id'] ?? null,
            'level' => $_POST['level'] ?? null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'is_verified' => isset($_POST['is_verified']) ? 1 : 0,
            'role' => $_POST['role'] ?? 'user'
        ];
        if ($user->updateUser($userId, $data)) {
            $success = 'User updated successfully.';
        } else {
            $error = 'Failed to update user.';
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<main>
    <div class="container pt-120 pb-120">
        <h2>Manage Users</h2>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>School</th>
                    <th>Faculty</th>
                    <th>Department</th>
                    <th>Level</th>
                    <th>Role</th>
                    <th>Active</th>
                    <th>Verified</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($u['username']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><?php echo htmlspecialchars($u['school_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($u['faculty_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($u['department_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($u['level'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($u['role']); ?></td>
                        <td><?php echo $u['is_active'] ? 'Yes' : 'No'; ?></td>
                        <td><?php echo $u['is_verified'] ? 'Yes' : 'No'; ?></td>
                        <td>
                            <button class="btn it-btn small" data-bs-toggle="modal" data-bs-target="#editUserModal<?php echo $u['id']; ?>">Edit</button>
                        </td>
                    </tr>
                </tbody>
        </table>

        <?php foreach ($users as $u): ?>
            <div class="modal fade" id="editUserModal<?php echo $u['id']; ?>" tabindex="-1" aria-labelledby="editUserModalLabel<?php echo $u['id']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editUserModalLabel<?php echo $u['id']; ?>">Edit User: <?php echo htmlspecialchars($u['full_name']); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST">
                            <div class="modal-body">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                <div class="mb-3">
                                    <label for="full_name_<?php echo $u['id']; ?>" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="full_name_<?php echo $u['id']; ?>" name="full_name" value="<?php echo htmlspecialchars($u['full_name']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="username_<?php echo $u['id']; ?>" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username_<?php echo $u['id']; ?>" name="username" value="<?php echo htmlspecialchars($u['username']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="email_<?php echo $u['id']; ?>" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email_<?php echo $u['id']; ?>" name="email" value="<?php echo htmlspecialchars($u['email']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="school_id_<?php echo $u['id']; ?>" class="form-label">School</label>
                                    <select class="form-select" id="school_id_<?php echo $u['id']; ?>" name="school_id">
                                        <option value="">Select School</option>
                                        <?php foreach ($schools as $school): ?>
                                            <option value="<?php echo $school['id']; ?>" <?php echo $u['school_id'] == $school['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($school['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="faculty_id_<?php echo $u['id']; ?>" class="form-label">Faculty</label>
                                    <select class="form-select" id="faculty_id_<?php echo $u['id']; ?>" name="faculty_id">
                                        <option value="">Select Faculty</option>
                                        <?php foreach ($faculties as $faculty): ?>
                                            <option value="<?php echo $faculty['id']; ?>" <?php echo $u['faculty_id'] == $faculty['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($faculty['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="department_id_<?php echo $u['id']; ?>" class="form-label">Department</label>
                                    <select class="form-select" id="department_id_<?php echo $u['id']; ?>" name="department_id">
                                        <option value="">Select Department</option>
                                        <?php foreach ($departments as $department): ?>
                                            <option value="<?php echo $department['id']; ?>" <?php echo $u['department_id'] == $department['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($department['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="level_<?php echo $u['id']; ?>" class="form-label">Level</label>
                                    <select class="form-select" id="level_<?php echo $u['id']; ?>" name="level">
                                        <option value="">Select Level</option>
                                        <option value="100" <?php echo $u['level'] == '100' ? 'selected' : ''; ?>>100</option>
                                        <option value="200" <?php echo $u['level'] == '200' ? 'selected' : ''; ?>>200</option>
                                        <option value="300" <?php echo $u['level'] == '300' ? 'selected' : ''; ?>>300</option>
                                        <option value="400" <?php echo $u['level'] == '400' ? 'selected' : ''; ?>>400</option>
                                        <option value="500" <?php echo $u['level'] == '500' ? 'selected' : ''; ?>>500</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="role_<?php echo $u['id']; ?>" class="form-label">Role</label>
                                    <select class="form-select" id="role_<?php echo $u['id']; ?>" name="role">
                                        <option value="user" <?php echo $u['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                                        <option value="admin" <?php echo $u['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active_<?php echo $u['id']; ?>" name="is_active" <?php echo $u['is_active'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_active_<?php echo $u['id']; ?>">Active</label>
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="is_verified_<?php echo $u['id']; ?>" name="is_verified" <?php echo $u['is_verified'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_verified_<?php echo $u['id']; ?>">Verified</label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn it-btn large" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn it-btn large">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <script>
                const faculties<?php echo $u['id']; ?> = <?php echo json_encode($faculties); ?>;
                const departments<?php echo $u['id']; ?> = <?php echo json_encode($departments); ?>;

                document.getElementById('school_id_<?php echo $u['id']; ?>').addEventListener('change', function() {
                    const schoolId = this.value;
                    const facultySelect = document.getElementById('faculty_id_<?php echo $u['id']; ?>');
                    const departmentSelect = document.getElementById('department_id_<?php echo $u['id']; ?>');
                    facultySelect.innerHTML = '<option value="">Select Faculty</option>';
                    departmentSelect.innerHTML = '<option value="">Select Department</option>';
                    departmentSelect.disabled = true;

                    if (schoolId) {
                        facultySelect.disabled = false;
                        faculties<?php echo $u['id']; ?>.filter(f => f.school_id == schoolId).forEach(faculty => {
                            const option = document.createElement('option');
                            option.value = faculty.id;
                            option.textContent = faculty.name;
                            facultySelect.appendChild(option);
                        });
                    } else {
                        facultySelect.disabled = true;
                    }
                });

                document.getElementById('faculty_id_<?php echo $u['id']; ?>').addEventListener('change', function() {
                    const facultyId = this.value;
                    const departmentSelect = document.getElementById('department_id_<?php echo $u['id']; ?>');
                    departmentSelect.innerHTML = '<option value="">Select Department</option>';

                    if (facultyId) {
                        departmentSelect.disabled = false;
                        departments<?php echo $u['id']; ?>.filter(d => d.faculty_id == facultyId).forEach(department => {
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
        <?php endforeach; ?>
    </div>
</main>
<?php
$content = ob_get_clean();
include 'views/layouts/main.php';
?>