
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
$stmt = $pdo->prepare('SELECT * FROM stories ORDER BY created_at DESC');
$stmt->execute();
$stories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NoBSSpace</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
  <style>
/* Sticky Header */
.header {
    position: sticky;
    top: 0;
    z-index: 1030; /* Ensures header is below modal */
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Sidebar */
.sidebar {
    height: 100vh;
    overflow-y: auto;
    position: sticky;
    top: 0;
    border-right: 1px solid #ddd;
    padding: 20px 15px;
    background-color: #f8f9fa;
}

/* Modal Styling */
.modal {
    z-index: 1055; /* Ensure modal is above header */
}

.modal-backdrop {
    z-index: 1050; /* Ensure backdrop is behind the modal */
}

/* Accordion Button Styling */
.accordion-button {
    font-weight: bold;
    background-color: #f8f9fa;
    color: #333;
    transition: all 0.3s ease;
}

.accordion-button:hover {
    background-color: #007bff;
    color: white;
}

.accordion-button:not(.collapsed) {
    background-color: #007bff;
    color: white;
}

/* Accordion Body Styling */
.accordion-body img {
    max-width: 100%;
    border-radius: 5px;
    margin-bottom: 15px;
}
/* Ensure the modal appears above the backdrop */
.modal {
    z-index: 1055; /* Ensure it's above everything else */
    opacity: 1; /* Ensure the modal is fully visible */
}

/* Modal backdrop (dimmed background) */
.modal-backdrop {
    z-index: 1050; /* Ensure it's below the modal but above the page */
    background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black */
}

/* Prevent the entire page from being dimmed */
body.modal-open {
    overflow: hidden; /* Disable scrolling when the modal is open */
}

/* Fix for header staying above the backdrop */
.header {
    z-index: 1030; /* Header below the modal backdrop and modal */
}

/* Table Styling */
.table-hover tbody tr:hover {
    background-color: #f8f9fa; /* Highlight row on hover */
    cursor: pointer;
}

/* Modal Styling */
.modal-body img {
    max-width: 100%;
    margin-bottom: 20px;
    border-radius: 8px;
}

table {
    max-height: 400px; /* Set a fixed height for the scrollable area */
    overflow-y: auto; /* Enable vertical scrolling */
    
    margin-bottom: 20px; /* Add spacing below the table */
}

body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    margin: 0; /* Remove default margin */
}

main {
    flex-grow: 1; /* Push the footer to the bottom */
}

footer {
    background-color: #007bff; /* Primary color for footer */
    color: white;
    padding: 10px 20px;
    text-align: center;
    
}
/* Add shadow to the card */
.card {
    border: 1px solid #ddd; /* Subtle border */
    border-radius: 8px; /* Rounded corners */
    overflow: hidden; /* Ensure content stays inside */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Light shadow */
}

/* Card Header */
.card-header {
    font-weight: bold;
    text-align: center;
}

/* Table Styling */
.table {
    margin: 0; /* Remove default margin inside card */
    border-spacing: 0; /* Ensure compact spacing */
}

.table thead th {
    background-color: #f8f9fa; /* Light header background */
    border-bottom: 2px solid #ddd; /* Header underline */
}

.table-hover tbody tr:hover {
    background-color: #f1f1f1; /* Highlight row on hover */
    cursor: pointer; /* Make it feel interactive */
}

/* Responsive Table Scroll */
.table-responsive {
    overflow-x: auto; /* Horizontal scrolling for smaller screens */
}

/* Optional Styling for Table Rows */
.table tbody tr td {
    vertical-align: middle; /* Center-align text vertically */
    padding: 12px; /* Add some spacing */
}

  </style>
</head>
<body>
 <!-- Header -->
    <div class="">
    <header class="bg-primary text-white py-3 d-flex justify-content-between align-items-center shadow-lg p-3 mb-3 bg-white rounded">
        <h1 class="ms-1 shadow-sm bg-grey" style="color:black;">NoBSSpace</h1>
        <div class="me-1">
    <a href="social.php" class="btn btn-light btn-sm">Social</a>
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

<main><div class="container my-5">
    
        <div class="d-flex justify-content-between align-items-center mb-4">
        
        <a href="cbs.php" class="btn btn-success">Add NEW</a>
    </div>
    <div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"></h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Blog Entry</th>
                            <th>Content</th>
                            <th>Date Posted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stories)): ?>
                            <tr>
                                <td colspan="3" class="text-center">No stories found. Start writing your first story!</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($stories as $story): ?>
                                <tr data-bs-toggle="modal" data-bs-target="#storyModal<?= $story['id'] ?>">
                                    <td><?= htmlspecialchars($story['title']) ?></td>
                                    <td><?= nl2br(htmlspecialchars(substr($story['content'], 0, 100))) ?>...</td>
                                    <td><?= htmlspecialchars($story['created_at']) ?></td>
                                </tr>

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
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</div></main>







  



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
<script>
    // Ensure the modal is properly initialized
    document.addEventListener('DOMContentLoaded', function () {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.addEventListener('show.bs.modal', function () {
                document.body.classList.add('modal-open');
            });

            modal.addEventListener('hidden.bs.modal', function () {
                document.body.classList.remove('modal-open');
            });
        });
    });
</script>


</body>
</html>
