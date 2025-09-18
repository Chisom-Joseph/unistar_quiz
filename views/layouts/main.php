<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz App</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <header>
        <nav><!-- Links: Home, Dashboard, Profile, Logout --></nav>
    </header>
    <main><?php echo $content; ?></main>  <!-- Include specific view here in router -->
    <footer><!-- Copyright --></footer>
    <script src="/public/js/app.js"></script>
</body>
</html>