

<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}
require 'config.php';
require 'bigdump.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Main Page - NoBSSpace</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
<style>
    .card{
       
        max-width:50%;
    }
    .scrollable-div {
    width: 100%; /* Set desired width */
    max-height: 300px; /* Set desired maximum height */
    overflow-y: scroll; /* Adds vertical scrolling */
    overflow-x: hidden; /* Hide horizontal scrolling */
    border: 1px solid #ddd; /* Optional: Add a border for clarity */
    padding: 10px; /* Optional: Add some padding */
    background-color: #f8f9fa; /* Optional: Light gray background */
}
</style>
</head>
<body>
     <!-- Header -->
    <div class="header">
   <header class="bg-primary text-white py-3 d-flex justify-content-between align-items-center shadow-lg p-3 mb-3 bg-white rounded">
        <h3 class="ms-1 shadow-sm bg-grey" style="color:black;">NOBSSpace</h3>
        <div class="me-1 shadow-lg border-bottom">
   
    <a href="social.php" class="btn btn-light btn-sm">Socials</a>
    
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
  <main class="container my-2">
    <div class="row">
      <!-- Linked Card -->
      <div class="col-md-4">
        <div class="card shadow-sm">
          <img src="img/gut.jpeg" class="card-img-top" alt="Quiz Thumbnail">
          <div class="card-body scrollable-div">
            <h5 class="card-title">What Gut Type are you?</h5>
            
            <a href="apps/guttype/index.html" class="btn btn-primary">Take the Quiz</a>
          </div>
        </div>
      </div>
    </div>
  </main>

<!-- Floating Footer -->
    <footer class="bg-primary text-white text-center py-3">
        <p>&copy; 2024 NoBSSpace. <a href="privacy-policy.html" class="text-warning">Privacy Policy</a></p>
    </footer>
</body>
</html>

