<?php
// views/user/dashboard.php
ob_start();
require_once 'config/constants.php';
if (!isset($_SESSION['user_id'])) {
    error_log("Unauthorized access to user dashboard, redirecting to login");
    header('Location: ' . SITE_URL . '/?page=login&error=not_logged_in');
    exit;
}
$userData = $user->getUserData($_SESSION['user_id']);
error_log("User dashboard loaded for user_id: " . $_SESSION['user_id']);
?>
<h1>Welcome, <?php echo htmlspecialchars($userData['full_name']); ?>!</h1>
<p>Email: <?php echo htmlspecialchars($userData['email']); ?></p>
<p><a href="?page=logout">Logout</a></p>
<h2>Available Quizzes</h2>
<?php
$quizzes = $quiz->getAvailableQuizzes($_SESSION['user_id']);
if (empty($quizzes)) {
    echo "<p>No quizzes available.</p>";
} else {
    foreach ($quizzes as $q) {
        echo "<p><a href='?page=quiz&id=" . $q['id'] . "'>" . htmlspecialchars($q['name']) . "</a></p>";
    }
}
?>
<?php
$content = ob_get_clean();
include 'views/layouts/main.php';
?>