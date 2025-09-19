<?php
// views/user/quiz.php
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
    $quizId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($quizId <= 0) {
        throw new Exception("Invalid quiz ID");
    }
    $quizDetails = $quiz->getQuizDetails($quizId);
    $questions = $quiz->getQuestionsForQuiz($quizId);
    if (empty($quizDetails)) {
        throw new Exception("Quiz not found");
    }
    // Handle quiz submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_quiz'])) {
        try {
            validateCsrf($_POST['csrf'] ?? '');
            $answers = $_POST['answers'] ?? [];
            $attemptId = $_POST['attempt_id'] ?? 0;
            $score = $quiz->submitAttempt($attemptId, $answers);
            $success = "Quiz submitted successfully. Your score: $score";
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    } else {
        // Start new attempt
        $attemptId = $quiz->startAttempt($_SESSION['user_id'], $quizId);
    }
} catch (Exception $e) {
    error_log("Error in quiz page: " . $e->getMessage());
    $error = $e->getMessage();
}
?>
<div class="container">
    <h1 class="my-4"><?php echo htmlspecialchars($quizDetails['name']); ?> - <?php echo htmlspecialchars($quizDetails['course_name']); ?></h1>
    <p>Attempts Allowed: <?php echo $quizDetails['attempts_allowed']; ?> | Timer: <?php echo $quizDetails['timer_minutes']; ?> minutes</p>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif (isset($success)): ?>
        <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($success); ?></div>
    <?php else: ?>
        <form method="POST" action="?page=quiz&id=<?php echo $quizId; ?>">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <input type="hidden" name="submit_quiz" value="1">
            <input type="hidden" name="attempt_id" value="<?php echo $attemptId; ?>">
            <?php foreach ($questions as $index => $question): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Question <?php echo $index + 1; ?>: <?php echo htmlspecialchars($question['text']); ?></h5>
                        <div class="list-group">
                            <?php foreach ($question['options'] as $optionIndex => $option): ?>
                                <label class="list-group-item">
                                    <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="<?php echo $optionIndex; ?>" required>
                                    <?php echo htmlspecialchars($option); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary">Submit Quiz</button>
        </form>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include 'views/layouts/main.php';
?>