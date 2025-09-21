<?php
// views/user/quiz_results.php
ob_start();
require_once 'config/constants.php';
require_once 'includes/auth.php';
require_once 'classes/User.php';
require_once 'classes/Quiz.php';

$user = new User();
$quiz = new Quiz();

try {
    if (!$user->isLoggedIn()) {
        header('Location: ' . SITE_URL . '/?page=login&error=not_logged_in');
        exit;
    }
    $attemptId = isset($_GET['attempt_id']) ? (int)$_GET['attempt_id'] : 0;
    if ($attemptId <= 0) {
        throw new Exception("Invalid attempt ID");
    }
    $attempt = $quiz->getAttemptDetails($attemptId);
    if (empty($attempt)) {
        throw new Exception("Attempt not found");
    }
} catch (Exception $e) {
    error_log("Error in quiz results page: " . $e->getMessage());
    $error = $e->getMessage();
}
?>
<div class="container">
    <h1 class="my-4">Quiz Results - <?php echo htmlspecialchars($attempt['quiz_name']); ?></h1>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
    <?php else: ?>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Your Score: <?php echo $attempt['score']; ?></h5>
                <p class="card-text">Answers Submitted:</p>
                <pre><?php print_r($attempt['answers']); ?></pre>
            </div>
        </div>
        <a href="?page=dashboard" class="btn btn-primary">Back to Dashboard</a>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include 'views/layouts/dashboard.php';
?>