<?php
session_start();
require 'config.php';

if (!isset($_SESSION['loggedin']) || !isset($_GET['post_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$postId = (int) $_GET['post_id'];
$userId = $_SESSION['user_id'];

// Check if the user has already liked the post
$stmt = $pdo->prepare('SELECT * FROM likes WHERE post_id = ? AND user_id = ?');
$stmt->execute([$postId, $userId]);
$existingLike = $stmt->fetch();

if ($existingLike) {
    // Unlike the post
    $stmt = $pdo->prepare('DELETE FROM likes WHERE post_id = ? AND user_id = ?');
    $stmt->execute([$postId, $userId]);
    $action = 'unliked';
} else {
    // Like the post
    $stmt = $pdo->prepare('INSERT INTO likes (post_id, user_id) VALUES (?, ?)');
    $stmt->execute([$postId, $userId]);
    $action = 'liked';
}

// Get the updated like count
$stmt = $pdo->prepare('SELECT COUNT(*) FROM likes WHERE post_id = ?');
$stmt->execute([$postId]);
$newLikeCount = $stmt->fetchColumn();

echo json_encode(['success' => true, 'action' => $action, 'new_like_count' => $newLikeCount]);
exit;
?>

