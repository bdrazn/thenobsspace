<?php
require 'config.php';

if (isset($_GET['id'])) {
    $storyId = (int)$_GET['id'];
    $stmt = $pdo->prepare('SELECT title, content, picture, created_at FROM stories WHERE id = ?');
    $stmt->execute([$storyId]);
    $story = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($story) {
        header('Content-Type: application/json');
        echo json_encode($story);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Story not found']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
}
?>
