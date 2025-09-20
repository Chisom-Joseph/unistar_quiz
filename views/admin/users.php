<?php
// views/admin/users.php
ob_start();
require_once 'config/constants.php';
require_once 'includes/auth.php';
require_once 'classes/User.php';
require_once 'classes/Admin.php';

$user = new User();
$admin = new Admin();

try {
    $userData = $user->getUserData($_SESSION['user_id']);
    if ($userData['role'] !== 'admin') {
        error_log("Non-admin user_id: {$_SESSION['user_id']} attempted to access admin users");
        header('Location: ' . SITE_URL . '/?page=dashboard');
        exit;
    }
    // Handle user status toggle
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_user'])) {
        try {
            validateCsrf($_POST['csrf'] ?? '');
            $admin->toggleUserStatus($_POST['user_id'], $_POST['is_active']);
            $success = "User status updated successfully.";
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
    // Handle CSV export
    if (isset($_GET['action']) && $_GET['action'] === 'export_csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="users_export.csv"');
        $admin->exportUsersCSV();
        exit;
    }
    $users = $admin->getAllUsers();
} catch (Exception $e) {
    error_log("Error in admin users: " . $e->getMessage());
    $error = $e->getMessage();
}
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Manage Users</h1>
        <div>
            <button class="btn btn-primary d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar">
                Menu
            </button>
            <a href="?page=admin_users&action=export_csv" class="btn btn-outline-primary">Export CSV</a>
        </div>
    </div>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif (isset($success)): ?>
        <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">User List</h4>
            </div>
            <div class="card-body">
            <?php if (empty($users)): ?>
                <p class="card-text">No users found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table id="example3" class="display" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Full Name</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><img class="rounded-circle" width="35" height="35" src="<?php echo SITE_URL . "/public/uploads/" . htmlspecialchars($userData['profile_pic']); ?>" alt="" style="object-fit:cover;"></td>
                                    <td><?php echo htmlspecialchars($u['username']); ?></td>
                                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                                    <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($u['role']); ?></td>
                                    <td><?php echo $u['is_active'] ? '<span class="badge light badge-success">Active</span>' : '<span class="badge light badge-warning">Inactive</span>'; ?></td>
                                    <td>
                                        <form method="POST" action="?page=admin_users" class="d-inline">
                                            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                            <input type="hidden" name="is_active" value="<?php echo $u['is_active'] ? 0 : 1; ?>">
                                            <input type="hidden" name="toggle_user" value="1">
                                            <button type="submit" class="btn btn-xs btn-<?php echo $u['is_active'] ? 'danger' : 'success'; ?>">
                                                <?php echo $u['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'views/layouts/dashboard.php';
?>