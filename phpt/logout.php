
<?php
session_start();
require 'config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Update is_loggedin status in the database
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare('UPDATE users SET is_loggedin = FALSE WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $stmt = $pdo->prepare('UPDATE users SET is_online = FALSE WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
}

// Destroy session
session_destroy();
header('Location: login.php');
exit;
?>