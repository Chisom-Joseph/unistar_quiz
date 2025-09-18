// admin/settings.php
<?php
// Form to toggle flags
if (POST) {
    foreach (flags as flag) {
        $value = $_POST[$flag];
        $stmt = $pdo->prepare("UPDATE feature_flags SET value = ? WHERE flag_name = ?");
        $stmt->execute([$value, $flag]);
    }
}
?>