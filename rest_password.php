<?php
session_start();
include('connection.php');

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['token'])) {
    $token = $_GET['token'];

    // Validate the token
    $query = $conn->query("SELECT * FROM `users` WHERE `reset_token` = '$token' AND `token_expiry` > NOW()");
    if ($query->num_rows > 0) {
        // Token is valid
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['password'])) {
            $password = $_POST['password'];
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Update password in the database
            $conn->query("UPDATE `users` SET `password` = '$hashedPassword', `reset_token` = NULL, `token_expiry` = NULL WHERE `reset_token` = '$token'");
            $message = "Your password has been reset. <a href='login.php'>Login here</a>";
        }
    } else {
        $message = "The reset link is invalid or has expired.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <div class="card mx-auto" style="max-width: 400px;">
    <div class="card-body">
      <h4 class="text-center">Reset Password</h4>
      <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
      <?php endif; ?>
      <?php if (isset($token) && empty($message)): ?>
        <form action="" method="post">
          <div class="mb-3">
            <label for="password" class="form-label">New Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Enter your new password" required>
          </div>
          <button type="submit" class="btn btn-primary">Reset Password</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
