<?php
// views/admin/questions.php
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
        error_log("Non-admin user_id: {$_SESSION['user_id']} attempted to access admin questions");
        header('Location: ' . SITE_URL . '/?page=dashboard');
        exit;
    }
    // Handle question creation
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_question'])) {
        try {
            validateCsrf($_POST['csrf'] ?? '');
            $options = [$_POST['option1'], $_POST['option2'], $_POST['option3'], $_POST['option4']];
            $admin->createQuestion($_POST['quiz_id'], $_POST['question_text'], $_POST['correct_option'], $options, $_POST['score'], $_POST['explanation']);
            $success = "Question created successfully.";
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
    // Handle question update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_question'])) {
        try {
            validateCsrf($_POST['csrf'] ?? '');
            $options = [$_POST['option1'], $_POST['option2'], $_POST['option3'], $_POST['option4']];
            $admin->updateQuestion($_POST['question_id'], $_POST['question_text'], $_POST['correct_option'], $options, $_POST['score'], $_POST['explanation']);
            $success = "Question updated successfully.";
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
    // Handle question deletion
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_question'])) {
        try {
            validateCsrf($_POST['csrf'] ?? '');
            $admin->deleteQuestion($_POST['question_id']);
            $success = "Question deleted successfully.";
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
    // Fetch questions and quizzes
    $questions = $admin->getAllQuestions();
    $quizzes = $admin->getAllQuizzes();
} catch (Exception $e) {
    error_log("Error in admin questions: " . $e->getMessage());
    $error = $e->getMessage();
}
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Manage Questions</h1>
        <button class="btn btn-primary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar">
            Menu
        </button>
    </div>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif (isset($success)): ?>
        <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Create Question</h5>
            <form method="POST" action="?page=admin_questions">
                <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <input type="hidden" name="create_question" value="1">
                <div class="mb-3">
                    <label for="quiz_id" class="form-label">Quiz</label>
                    <select class="form-select" name="quiz_id" id="quiz_id" required>
                        <option value="">Select a quiz</option>
                        <?php foreach ($quizzes as $quiz): ?>
                            <option value="<?php echo $quiz['id']; ?>" <?php echo isset($_POST['quiz_id']) && $_POST['quiz_id'] == $quiz['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($quiz['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="question_text" class="form-label">Question Text</label>
                    <textarea class="form-control" name="question_text" id="question_text" rows="3" required><?php echo isset($_POST['question_text']) ? htmlspecialchars($_POST['question_text']) : ''; ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Options</label>
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><?php echo $i; ?></span>
                            <input type="text" class="form-control" name="option<?php echo $i; ?>" id="option<?php echo $i; ?>" value="<?php echo isset($_POST['option' . $i]) ? htmlspecialchars($_POST['option' . $i]) : ''; ?>" <?php echo $i <= 2 ? 'required' : ''; ?>>
                        </div>
                    <?php endfor; ?>
                </div>
                <div class="mb-3">
                    <label for="correct_option" class="form-label">Correct Option</label>
                    <select class="form-select" name="correct_option" id="correct_option" required>
                        <option value="">Select correct option</option>
                        <?php for ($i = 0; $i <= 3; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo isset($_POST['correct_option']) && $_POST['correct_option'] == $i ? 'selected' : ''; ?>><?php echo $i + 1; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="score" class="form-label">Score</label>
                    <input type="number" class="form-control" name="score" id="score" value="<?php echo isset($_POST['score']) ? htmlspecialchars($_POST['score']) : '1'; ?>" min="1" required>
                </div>
                <div class="mb-3">
                    <label for="explanation" class="form-label">Explanation</label>
                    <textarea class="form-control" name="explanation" id="explanation" rows="3"><?php echo isset($_POST['explanation']) ? htmlspecialchars($_POST['explanation']) : ''; ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Add Question</button>
            </form>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Question List</h5>
            <?php if (empty($questions)): ?>
                <p class="card-text">No questions found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Quiz</th>
                                <th>Question</th>
                                <th>Correct Option</th>
                                <th>Score</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($questions as $question): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($question['id']); ?></td>
                                    <td><?php echo htmlspecialchars($question['quiz_name'] ?? 'No Quiz'); ?></td>
                                    <td><?php echo htmlspecialchars($question['text']); ?></td>
                                    <td><?php echo htmlspecialchars($question['correct_option_index'] + 1); ?> (<?php echo htmlspecialchars($question['options'][$question['correct_option_index']] ?? 'N/A'); ?>)</td>
                                    <td><?php echo htmlspecialchars($question['score']); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editQuestionModal<?php echo $question['id']; ?>">Edit</button>
                                        <form method="POST" action="?page=admin_questions" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this question?');">
                                            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                                            <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
                                            <input type="hidden" name="delete_question" value="1">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
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
    <!-- Edit Question Modals -->
    <?php foreach ($questions as $question): ?>
        <div class="modal fade" id="editQuestionModal<?php echo $question['id']; ?>" tabindex="-1" aria-labelledby="editQuestionModalLabel<?php echo $question['id']; ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editQuestionModalLabel<?php echo $question['id']; ?>">Edit Question</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="?page=admin_questions">
                            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                            <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
                            <input type="hidden" name="update_question" value="1">
                            <div class="mb-3">
                                <label for="edit_quiz_id_<?php echo $question['id']; ?>" class="form-label">Quiz</label>
                                <select class="form-select" name="quiz_id" id="edit_quiz_id_<?php echo $question['id']; ?>" required>
                                    <option value="">Select a quiz</option>
                                    <?php foreach ($quizzes as $quiz): ?>
                                        <option value="<?php echo $quiz['id']; ?>" <?php echo $question['quiz_id'] == $quiz['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($quiz['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_question_text_<?php echo $question['id']; ?>" class="form-label">Question Text</label>
                                <textarea class="form-control" name="question_text" id="edit_question_text_<?php echo $question['id']; ?>" rows="3" required><?php echo htmlspecialchars($question['text']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Options</label>
                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                    <div class="input-group mb-2">
                                        <span class="input-group-text"><?php echo $i; ?></span>
                                        <input type="text" class="form-control" name="option<?php echo $i; ?>" id="edit_option<?php echo $i; ?>_<?php echo $question['id']; ?>" value="<?php echo htmlspecialchars($question['options'][$i - 1] ?? ''); ?>" <?php echo $i <= 2 ? 'required' : ''; ?>>
                                    </div>
                                <?php endfor; ?>
                            </div>
                            <div class="mb-3">
                                <label for="edit_correct_option_<?php echo $question['id']; ?>" class="form-label">Correct Option</label>
                                <select class="form-select" name="correct_option" id="edit_correct_option_<?php echo $question['id']; ?>" required>
                                    <option value="">Select correct option</option>
                                    <?php for ($i = 0; $i <= 3; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo $question['correct_option_index'] == $i ? 'selected' : ''; ?>><?php echo $i + 1; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_score_<?php echo $question['id']; ?>" class="form-label">Score</label>
                                <input type="number" class="form-control" name="score" id="edit_score_<?php echo $question['id']; ?>" value="<?php echo htmlspecialchars($question['score']); ?>" min="1" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_explanation_<?php echo $question['id']; ?>" class="form-label">Explanation</label>
                                <textarea class="form-control" name="explanation" id="edit_explanation_<?php echo $question['id']; ?>" rows="3"><?php echo htmlspecialchars($question['explanation'] ?? ''); ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Question</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php
$content = ob_get_clean();
include 'views/layouts/main.php';
?>