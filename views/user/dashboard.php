<?php
// views/user/dashboard.php
ob_start();
require_once 'config/constants.php';
require_once 'includes/auth.php';
require_once 'classes/User.php';
require_once 'classes/Quiz.php';

$user = new User();
$quiz = new Quiz();

try {
    $userData = $user->getUserData($_SESSION['user_id']);
    $quizzes = $quiz->getAvailableQuizzes($_SESSION['user_id']);
} catch (Exception $e) {
    error_log("Error in user dashboard: " . $e->getMessage());
    $error = $e->getMessage();
}
?>
<div class="container">
    <h1 class="my-4">User Dashboard - <?php echo htmlspecialchars($userData['full_name']); ?></h1>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <img src="<?php echo SITE_URL . "/public/uploads/" . htmlspecialchars($userData['profile_pic']); ?>" alt="Profile Picture" class="rounded-circle mb-3" style="object-fit: cover;" width="150" height="150">
                    <h5 class="card-title mt-3"><?php echo htmlspecialchars($userData['full_name']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($userData['email']); ?></p>
                    <a href="?page=profile" class="btn btn-primary">Edit Profile</a>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Available Quizzes</h5>
                    <?php if (empty($quizzes)): ?>
                        <p>No available quizzes.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($quizzes as $q): ?>
                                <a href="?page=quiz&id=<?php echo $q['id']; ?>" class="list-group-item list-group-item-action">
                                    <h5 class="mb-1"><?php echo htmlspecialchars($q['name']); ?></h5>
                                    <p class="mb-1">Course: <?php echo htmlspecialchars($q['course_name']); ?></p>
                                    <small>Attempts left: <?php echo $q['attempts_allowed'] - $q['attempts_count']; ?> | Timer: <?php echo $q['timer_minutes']; ?> min</small>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'views/layouts/main.php';
?>