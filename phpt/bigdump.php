<?php

require 'config.php';


$message = '';




if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['announcement_title'])) {
    $title = $_POST['announcement_title'];
    $content = $_POST['announcement_content'];

    $stmt = $pdo->prepare('INSERT INTO announcements (title, content) VALUES (?, ?)');
    $stmt->execute([$title, $content]);
    header('Location: socials.php');
    exit;
}

$newestStories = [];
$stmt = $pdo->query('
    SELECT id, title, picture 
    FROM stories 
    ORDER BY created_at DESC 
    LIMIT 3
');
$newestStories = $stmt->fetchAll();

// Fetch newest announcements for the slider (limit 3)
$newestAnnouncements = [];
$stmt = $pdo->query('
    SELECT id, title, content 
    FROM announcements 
    ORDER BY created_at DESC 
    LIMIT 3
');
$newestAnnouncements = $stmt->fetchAll();

$userLikedPosts = [];
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare('SELECT post_id FROM likes WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $userLikedPosts = array_column($stmt->fetchAll(), 'post_id');
}

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}





// Fetch posts with user info
$posts = [];
$stmt = $pdo->query('
    SELECT posts.*, users.username, users.is_online 
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    ORDER BY posts.created_at DESC
');
$posts = $stmt->fetchAll();

// Handle new post submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_post'])) {
    $content = $_POST['content'];
    $userId = $_SESSION['user_id'];

    // Handle image upload
    $picture = null;
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $fileName = uniqid() . '_' . basename($_FILES['picture']['name']);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['picture']['tmp_name'], $targetFile)) {
            $picture = $targetFile;
        }
    }

    // Insert the new post
    $stmt = $pdo->prepare('INSERT INTO posts (user_id, content, picture) VALUES (?, ?, ?)');
    $stmt->execute([$userId, $content, $picture]);
    header('Location: social.php');
    exit;
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['content']);
    $userId = $_SESSION['user_id']; // Assuming user is logged in

    // Check if a picture is uploaded
    if (!empty($_FILES['picture']['name'])) {
        // File upload logic
        $targetDir = "uploads/"; // Directory for uploads
        $fileName = time() . "_" . basename($_FILES['picture']['name']); // Unique file name
        $targetFilePath = $targetDir . $fileName;

        // Validate and move the uploaded file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['picture']['type'], $allowedTypes)) {
            if (move_uploaded_file($_FILES['picture']['tmp_name'], $targetFilePath)) {
                $picturePath = $targetFilePath;
            } else {
                $message = "Error uploading the image.";
            }
        } else {
            $message = "Invalid file type. Please upload a JPEG, PNG, or GIF image.";
        }
    } else {
        // Use default image if no file is uploaded
        $picturePath = "img/placeholder.jpg"; // Path to your default image
    }

    // Insert story into the database
    if (empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO stories (user_id, title, content, picture, created_at) VALUES (?, ?, ?, ?, NOW())");
        if ($stmt->execute([$userId, $title, $content, $picturePath])) {
            $message = "Story created successfully!";
            header('Location: usp.php');
        } else {
            $message = "Error creating story.";
        }
    }
}

// Handle comments submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $postId = $_POST['post_id'];
    $commentContent = $_POST['comment_content'];
    $userId = $_SESSION['user_id'];

    // Insert the comment
    $stmt = $pdo->prepare('INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)');
    $stmt->execute([$postId, $userId, $commentContent]);

    // Notify the post owner
    $stmt = $pdo->prepare('SELECT user_id FROM posts WHERE id = ?');
    $stmt->execute([$postId]);
    $postOwnerId = $stmt->fetchColumn();

    if ($postOwnerId != $userId) {
        $stmt = $pdo->prepare('INSERT INTO notifications (user_id, message) VALUES (?, ?)');
        $stmt->execute([$postOwnerId, "Your post received a new comment."]);
    }
    header('Location: social.php');
    exit;
}

// Fetch notifications count for logged-in user
$notificationsCount = 0;
$stmt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = FALSE');
$stmt->execute([$_SESSION['user_id']]);
$notificationsCount = $stmt->fetchColumn();

// Fetch posts with like and comment counts
$posts = [];
$stmt = $pdo->query('
    SELECT posts.*, users.username, users.is_online, 
           (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count, 
           (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) AS comment_count
    FROM posts 
    JOIN users ON posts.user_id = users.id
    ORDER BY posts.created_at DESC
');
$posts = $stmt->fetchAll();



// Handle likes
if (isset($_GET['like_post'])) {
    $postId = $_GET['like_post'];
    $userId = $_SESSION['user_id'];

    // Check if the user has already liked the post
    $stmt = $pdo->prepare('SELECT * FROM likes WHERE post_id = ? AND user_id = ?');
    $stmt->execute([$postId, $userId]);

    if (!$stmt->fetch()) {
        // Add the like
        $stmt = $pdo->prepare('INSERT INTO likes (post_id, user_id) VALUES (?, ?)');
        $stmt->execute([$postId, $userId]);

        // Notify the post owner
        $stmt = $pdo->prepare('SELECT user_id FROM posts WHERE id = ?');
        $stmt->execute([$postId]);
        $postOwnerId = $stmt->fetchColumn();

        if ($postOwnerId != $userId) {
            $stmt = $pdo->prepare('INSERT INTO notifications (user_id, message) VALUES (?, ?)');
            $stmt->execute([$postOwnerId, "Your post received a like."]);
        }
    }
    header('Location: social.php');
    exit;
}
?>