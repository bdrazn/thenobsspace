<?php
session_start();
require 'config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'bigdump.php'

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NoBSSpace - Social</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
<script>function focusCommentField(postId) {
    setTimeout(() => {
        const commentInput = document.querySelector(`#postModal${postId} input[name="comment_content"]`);
        if (commentInput) {
            commentInput.focus();
        } else {
            console.error(`Comment input field not found for post ID: ${postId}`);
        }
    }, 500); // Adjust timeout for modal animation if needed
}</script>
</head>
<body>
    
    <!-- Header -->
    <div class="header">
   <header class="bg-primary text-white py-3 d-flex justify-content-between align-items-center shadow-lg p-3 mb-3 bg-white rounded">
        <h3 class="ms-1 shadow-sm bg-grey" style="color:black;">NOBSSpace</h3>
        <div class="me-1">
   
    <a href="apps.php" class="btn btn-light btn-sm">Apps</a>
    <a href="usp.php" class="btn btn-light btn-sm">Blog</a>
    <a href="logout.php" class="btn btn-light btn-sm">Sign Out</a><a href="notifications.php" class="text-black notification-badge">
                <i class="fas fa-bell"></i>
                <?php if ($notificationsCount > 0): ?>
                    <span class="badge bg-danger" style="display: block"><?= $notificationsCount ?></span>
                <?php endif; ?>
            </a>
        </div>
    </header>
    </div>

    <!-- Main Section -->
    <main class="container my-5">
        <!-- New Post Form -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Create a New Post</h5>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <textarea class="form-control" name="content" rows="3" placeholder="What's on your mind?" required></textarea>
                    </div>
                    <div class="mb-3">
                        <input type="file" name="picture" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" name="new_post" class="btn btn-primary">Post</button>
                </form>
            </div>
        </div>

        <!-- Display Posts -->
        <?php foreach ($posts as $post): ?>
<div class="card mb-3" id="post-<?= $post['id'] ?>">
    <div class="card-header d-flex justify-content-between">
        <span><?= htmlspecialchars($post['username']) ?>
            <span class="online-status <?= $post['is_online'] ? 'online' : 'offline' ?>"></span>
        </span>
        <!-- Delete Button (Visible only to post owner or admin) -->
        <?php if ($post['user_id'] == $_SESSION['user_id'] || $_SESSION['user_type'] === 'admin'): ?>
            <button 
                class="btn btn-danger btn-sm" 
                onclick="deletePost(<?= $post['id'] ?>)">
                Delete
            </button>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php if (!empty($post['picture'])): ?>
            <img src="<?= htmlspecialchars($post['picture']) ?>" class="post-image" alt="Post Image">
        <?php endif; ?>
        <p><?= nl2br(htmlspecialchars(substr($post['content'], 0, 100))) ?>...</p>
        <!-- Like and Comment Icons -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <button 
                class="btn btn-light like-btn" 
                data-post-id="<?= $post['id'] ?>" 
                data-liked="<?= in_array($post['id'], $userLikedPosts) ? 'true' : 'false' ?>">
                <i class="fas fa-thumbs-up"></i> <span id="like-count-<?= $post['id'] ?>"><?= $post['like_count'] ?></span>
            </button>
            <button 
                class="btn btn-light" 
                data-bs-toggle="modal" 
                data-bs-target="#postModal<?= $post['id'] ?>" 
                onclick="focusCommentField(<?= $post['id'] ?>)">
                <i class="fas fa-comment"></i> <?= $post['comment_count'] ?>
            </button>
        </div>
    </div>
</div>





            <!-- Post Modal -->
<div class="modal fade" id="postModal<?= $post['id'] ?>" tabindex="-1" aria-labelledby="postModalLabel<?= $post['id'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="postModalLabel<?= $post['id'] ?>"><?= htmlspecialchars($post['username']) ?>'s Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (!empty($post['picture'])): ?>
                    <img src="<?= htmlspecialchars($post['picture']) ?>" class="post-image mb-3" alt="Post Image">
                <?php endif; ?>
                <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                <p>
    <i class="fas fa-thumbs-up"></i> <span id="modal-like-count-<?= $post['id'] ?>"><?= $post['like_count'] ?></span>
    <i class="fas fa-comment ms-3"></i> <?= $post['comment_count'] ?>
</p>

                <!-- Add Comment Form -->
                <form method="POST" class="mb-3">
                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                    <div class="input-group">
                        <input type="text" name="comment_content" class="form-control" placeholder="Add a comment..." required>
                        <button type="submit" name="add_comment" class="btn btn-primary">Comment</button>
                    </div>
                </form>

                <!-- Display Comments -->
                <h6>Comments</h6>
                <?php
                $stmt = $pdo->prepare('SELECT 
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
                $stmt->execute([$post['id']]);
                $comments = $stmt->fetchAll();
                ?>
                <?php if (empty($comments)): ?>
                    <p>No comments yet. Be the first to comment!</p>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="mb-2">
                            <strong><?= htmlspecialchars($comment['username']) ?>:</strong>
                            <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                            <small class="text-muted">Posted on <?= htmlspecialchars($comment['created_at']) ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


        <?php endforeach; ?>
    </main>

    <!-- Floating Footer -->
    <footer class="bg-primary text-white text-center py-3">
        <p>&copy; 2024 NoBSSpace. <a href="privacy-policy.html" class="text-warning">Privacy Policy</a></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
      <script>
function deletePost(postId) {
    if (confirm("Are you sure you want to delete this post?")) {
        fetch('delete_post.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: postId }),
        })
        .then(response => response.text()) // Use .text() for plain text response
        .then(data => {
            const postElement = document.getElementById(`post-${postId}`);
            if (postElement) {
                postElement.remove();
            }
            alert(data); // Display the plain text response
        })
        .catch(err => console.error('Error:', err));
    }
}


    </script>
<script>
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', () => {
            const postId = button.getAttribute('data-post-id');
            const isLiked = button.getAttribute('data-liked') === 'true';

            fetch(`like_post.php?post_id=${postId}`, {
                method: 'GET',
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the like count in the modal if it exists
                    const modalLikeCount = document.getElementById(`modal-like-count-${postId}`);
                    if (modalLikeCount) {
                        modalLikeCount.textContent = data.new_like_count;
                    }

                    // Update the like count in the main post
                    const likeCount = document.getElementById(`like-count-${postId}`);
                    if (likeCount) {
                        likeCount.textContent = data.new_like_count;
                    }

                    // Update the button's liked state
                    if (data.action === 'liked') {
                        button.setAttribute('data-liked', 'true');
                    } else if (data.action === 'unliked') {
                        button.setAttribute('data-liked', 'false');
                    }
                } else {
                    alert('Failed to update like status.');
                }
            })
            .catch(err => console.error(err));
        });
    });
</script>






</body>
</html>
