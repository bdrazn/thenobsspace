
<?php
require 'config.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    

    // Check if the email already exists
    $stmt = $pdo->prepare('SELECT * FROM users WHERE Email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // Check if the email already exists
    $stmt = $pdo->prepare('SELECT * FROM users WHERE Username = ?');
    $stmt->execute([$username]);
    $user2 = $stmt->fetch();

    if ($user || $user2) {
        $message = "Email or Username is already registered. Please try again.";
    }
    else {
        // Insert the new user
        $stmt = $pdo->prepare('INSERT INTO users (Email, username, password) VALUES (?, ?, ?)');
        $stmt->execute([$email, $username, $password]);
        $message = "Account created successfully. You can now log in.";
        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <h3 class="text-center">Sign Up</h3>
            <?php if (!empty($message)): ?>
              <div class="alert alert-info"><?= $message ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Username</label>
                <input type="username" class="form-control" id="username" name="username" required>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">Sign Up</button>
            </form>
            <div class="text-center mt-3">
              <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
