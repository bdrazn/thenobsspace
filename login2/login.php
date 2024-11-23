<?php
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['user_id'])) {
    header('Location: /phpt/social.php');
    exit;
}
?>
