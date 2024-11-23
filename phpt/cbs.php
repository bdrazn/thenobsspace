<?php
session_start();
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['user_id'])) {
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
  <title>Create Story</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body> 
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h3 class="text-center">Create a New Story</h3>
            <!-- Display success or error messages -->
            <?php if (!empty($message)): ?>
              <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
              <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
              </div>
              <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
              </div>
              <div class="mb-3">
                <label for="picture" class="form-label">Header Picture</label>
                <input type="file" class="form-control" id="picture" name="picture" accept="image/*">
              </div>
              <span style="float:left">
                <button type="submit" class="btn btn-primary w-100">Add Story</button><br>
              </span>
              <span style="float:right">
                <button 
                    type="button" 
                    class="btn btn-secondary w-100" 
                    onclick="location.href='usp.php'">
                    Close
                </button><br>
              </span>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
