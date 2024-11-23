<?php
session_start();
require 'config.php';
echo 'loaded';
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in and the necessary data is provided
if (!isset($_SESSION['loggedin']) || !isset($_POST['post_id']) || !isset($_POST['content'])) {
    exit('Invalid request');
}

// Sanitize input
$postId = (int) $_POST['post_id'];
$content = trim($_POST['content']);
$userId = $_SESSION['user_id'];

try {
    // Add the comment to the database
    $stmt = $pdo->prepare('INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)');
    $stmt->execute([$postId, $userId, $content]);

    // Fetch the updated comments
    $stmt = $pdo->prepare(
        'SELECT 
            comments.id AS id, 
            comments.content AS content, 
            comments.created_at AS created_at, 
            users.username AS username
        FROM 
            comments
        JOIN 
            users ON comments.user_id = users.id
        WHERE 
            comments.post_id = ?
        ORDER BY 
            comments.created_at DESC'
    );
    $stmt->execute([$postId]);
    $comments = $stmt->fetchAll();

    // Render the updated comments
    foreach ($comments as $comment): ?>
        <div class="mb-2">
            <strong><?= htmlspecialchars($comment['username']) ?>:</strong>
            <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
            <small class="text-muted">Posted on <?= htmlspecialchars($comment['created_at']) ?></small>
        </div>
    <?php endforeach;
} catch (PDOException $e) {
    // Display the error message on screen for debugging
    echo '<div style="color: red; font-weight: bold;">Database Error: ' . htmlspecialchars($e->getMessage()) . '</div>';

    // Log the error to a file for additional debugging
    error_log('Database error: ' . $e->getMessage());
    exit;
}
?>
