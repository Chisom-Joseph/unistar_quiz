<?php $questions = $_SESSION['current_quiz']['questions']; $current = $_SESSION['current_question']; $q = $questions[$current]; $opts = json_decode($q['options'], true); ?>
<form id="quiz-form" method="POST" action="/?page=submit_quiz">
    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf_token'] ?>">
    <h2>Question <?= $current + 1 ?> / <?= count($questions) ?></h2>
    <div class="progress-bar" style="width: <?= (($current+1)/count($questions))*100 ?>%"></div>
    <p><?= $q['text'] ?></p>
    <?php foreach ($opts as $i => $opt): ?>
    <label><input type="radio" name="answer_<?= $q['id'] ?>" value="<?= $i ?>"> <?= htmlspecialchars($opt) ?></label><br>
    <?php endforeach; ?>
    <button type="button" onclick="nextQuestion()">Next</button>
    <?php if ($current == count($questions)-1): ?>
    <button type="submit">Submit Quiz</button>
    <?php endif; ?>
</form>

<script>
function nextQuestion() {
    // Collect answers so far
    const formData = new FormData(document.getElementById('quiz-form'));
    // POST to next_question or location.href = '/?page=next_question'
    window.location.href = '/?page=next_question';
}
<?php if ($flags->get('enable_timer')): ?>
const timer = <?= $quiz['timer_minutes'] ?>;
startTimer(timer);
<?php endif; ?>
</script>