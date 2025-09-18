// admin/export.php
<?php
$type = $_GET['type'];  // users, quizzes, attempts
if ($type === 'users') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=users.csv');
    $stmt = $pdo->query("SELECT id, username, email, full_name, created_at FROM users");
    $fp = fopen('php://output', 'w');
    fputcsv($fp, ['ID', 'Username', 'Email', 'Name', 'Created']);
    while ($row = $stmt->fetch()) {
        fputcsv($fp, $row);
    }
    fclose($fp);
}
?>