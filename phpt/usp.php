
<?php
session_start();
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require 'config.php';
require 'bigdump.php';

// Fetch stories for the logged-in user
$stories = [];
$stmt = $pdo->prepare('SELECT * FROM stories WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$_SESSION['user_id']]);
$stories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Stories - NoBSSpace</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
  <style>
    html, body {
      height: 100%;
    }
    body {
      display: flex;
      flex-direction: column;
    }
    main {
      flex: 1;
    }
    .modal-content img {
      max-width: 100%;
      height: auto;
    }
  </style>
</head>
<body>
 <!-- Header -->
    <div class="header">
    <header class="bg-primary text-white py-3 d-flex justify-content-between align-items-center shadow-lg p-3 mb-3 bg-white rounded">
        <h1 class="ms-1 shadow-sm bg-grey" style="color:black;">NoBSSpace</h1>
        <div class="me-1">
    <a href="apps.php" class="btn btn-light btn-sm">Home</a>
    <a href="social.php" class="btn btn-light btn-sm">Socials</a>
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

<main class="container-fluid">
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar with Accordion -->
        <div class="col-md-3 sidebar bg-light shadow-sm">
            <h4 class="text-center py-3">My Stories</h4>
            <div class="accordion" id="storyAccordion">
                <?php if (empty($stories)): ?>
                    <div class="alert alert-info text-center">
                        No stories found. Start writing your first story!
                    </div>
                <?php else: ?>
                    <?php foreach ($stories as $story): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?= $story['id'] ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $story['id'] ?>" aria-expanded="false" aria-controls="collapse<?= $story['id'] ?>">
                                    <?= htmlspecialchars($story['title']) ?>
                                </button>
                            </h2>
                            <div id="collapse<?= $story['id'] ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $story['id'] ?>" data-bs-parent="#storyAccordion">
                                <div class="accordion-body">
                                    <?php if (!empty($story['picture'])): ?>
                                        <img src="<?= htmlspecialchars($story['picture']) ?>" class="img-fluid mb-3" alt="Story Image">
                                    <?php endif; ?>
                                    <p><?= nl2br(htmlspecialchars(substr($story['content'], 0, 100))) ?>...</p>
                                    <button class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#storyModal<?= $story['id'] ?>">Read More</button>
                                </div>
                            </div>
                        </div>

                        <!-- Modal for full story -->
                        <div class="modal fade" id="storyModal<?= $story['id'] ?>" tabindex="-1" aria-labelledby="storyModalLabel<?= $story['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="storyModalLabel<?= $story['id'] ?>"><?= htmlspecialchars($story['title']) ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <?php if (!empty($story['picture'])): ?>
                                            <img src="<?= htmlspecialchars($story['picture']) ?>" class="img-fluid mb-3" alt="Story Image">
                                        <?php endif; ?>
                                        <p><?= nl2br(htmlspecialchars($story['content'])) ?></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>





  
</main>


  <!-- Sticky Footer -->
  <footer class="bg-primary text-white text-center py-3">
    <p>&copy; 2024 NoBSSpace. <a href="privacy-policy.html" class="text-warning">Privacy Policy</a></p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Fetch all sidebar story items
        const storyItems = document.querySelectorAll('.story-item');
        const storyContent = document.getElementById('storyContent');

        // Add click event to each story item
        storyItems.forEach(item => {
            item.addEventListener('click', () => {
                const storyId = item.getAttribute('data-id');

                // Fetch story details using AJAX
                fetch(`get-story.php?id=${storyId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Update the story content area
                        storyContent.innerHTML = `
                           
                            ${data.picture ? `<img src="${data.picture}" alt="${data.title}" height=55% width=55%>` : ''}
                             <h2>${data.title}</h2>
                            <p class="mt-3">${data.content}</p>
                            <p class="text-muted">Created on: ${data.created_at} </p>
                        `;
                    })
                    .catch(error => {
                        console.error('Error fetching story:', error);
                        storyContent.innerHTML = `<p class="text-danger">Failed to load story details.</p>`;
                    });
            });
        });
    });
</script>

</body>
</html>
