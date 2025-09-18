// admin/dashboard.php
<?php
session_start();
require_once '../includes/functions.php';
if ($_SESSION['role'] !== 'admin') header('Location: ../login.php');

// Analytics
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$active_users = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM sessions WHERE last_activity > DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
// More stats

// Use HTML canvas for charts (vanilla JS)

// Links to manage_*.php
?>