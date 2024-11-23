<?php
session_start();
require 'config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Validate the request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Invalid request method";
    exit;
}

// Read and decode the JSON request body
$requestBody = file_get_contents('php://input');
$data = json_decode($requestBody, true);
$userid = $_SESSION['user_id'];

if ($data === null || !isset($data['id'])) {
    echo "Invalid or missing post ID";
    exit;
}

// Sanitize the post ID
$postId = (int) $data['id'];

// Verify if the user owns the post or is an admin
$stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
$stmt->execute([$postId]);
$post = $stmt->fetch();

if (!$post) {
    echo "Post not found";
    exit;
}

if ($post['user_id'] != $_SESSION['user_id'] && $_SESSION['user_type'] !== 'admin') {
    echo "Unauthorized";
    exit;
}

// Delete the post
$stmt = $pdo->prepare('DELETE FROM comments WHERE post_id = ?');
$stmt->execute([$postId]);
$stmt = $pdo->prepare('DELETE FROM likes WHERE post_id = ?');
$stmt->execute([$postId]);
$stmt = $pdo->prepare('DELETE FROM posts WHERE id = ?');
if ($stmt->execute([$postId])) {
    echo "Post with ID $postId deleted successfully";
} else {
    echo "Failed to delete post";
}
exit;

