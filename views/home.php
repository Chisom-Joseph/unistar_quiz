<!-- views/home.php -->
<?php ob_start(); ?>
<h1>Welcome to Quiz App</h1>
<p><a href="?page=register">Sign Up</a> | <a href="?page=login">Log In</a></p>
<?php $content = ob_get_clean(); include 'views/layouts/main.php'; ?>