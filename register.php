<?php
session_start();
include('connection.php');

// Initialize variables
$registration_error = '';
$registration_success = '';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Check for errors
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $registration_error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $registration_error = 'Invalid email format.';
    } elseif ($password !== $confirm_password) {
        $registration_error = 'Passwords do not match.';
    } else {
        // Check if email already exists
        $query = $conn->query("SELECT * FROM `users` WHERE `email`='$email'");
        if ($query->num_rows > 0) {
            $registration_error = 'Email already registered. Please login.';
        } else {
            // Insert the user into the database
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $insert_query = $conn->query("INSERT INTO `users` (`name`, `email`, `password`) VALUES ('$name', '$email', '$hashed_password')");
            if ($insert_query) {
                $registration_success = 'Registration successful! You can now <a href="login.php">login</a>.';
            } else {
                $registration_error = 'An error occurred. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom Styles -->
  <style>
    body {
      background-color: #e8f5e9;
      font-family: 'Arial', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .card {
      width: 100%;
      max-width: 450px;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }
    .card-header {
      background-color: #43a047;
      color: white;
      font-size: 1.5rem;
      font-weight: bold;
      text-align: center;
      padding: 1rem;
      border-radius: 12px 12px 0 0;
    }
    .form-control {
      border-radius: 8px;
      padding: 0.75rem;
      font-size: 1rem;
    }
    .btn-primary {
      background-color: #43a047;
      border: none;
      border-radius: 8px;
      padding: 0.75rem;
      font-size: 1rem;
      width: 100%;
      transition: all 0.3s ease;
    }
    .btn-primary:hover {
      background-color: #2e7d32;
    }
    .alert-error, .alert-success {
      border-radius: 8px;
      padding: 0.75rem;
      font-size: 0.9rem;
      text-align: center;
      margin-bottom: 1rem;
    }
    .alert-error {
      background-color: #ffcdd2;
      color: #b71c1c;
    }
    .alert-success {
      background-color: #c8e6c9;
      color: #2e7d32;
    }
    .form-label {
      font-weight: bold;
      color: #37474f;
    }
    .card-body {
      padding: 2rem;
    }
    .form-footer {
      text-align: center;
      margin-top: 1rem;
    }
    .form-footer a {
      color: #43a047;
      text-decoration: none;
      font-weight: bold;
    }
    .form-footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="card">
  <div class="card-header">
    Create an Account
  </div>
  <div class="card-body">
    <?php if (!empty($registration_error)): ?>
      <div class="alert-error">
        <?php echo $registration_error; ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($registration_success)): ?>
      <div class="alert-success">
        <?php echo $registration_success; ?>
      </div>
    <?php endif; ?>
    <form action="" method="post">
      <div class="mb-3">
        <label for="name" class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" id="name" placeholder="Enter your full name" >
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" id="email" placeholder="Enter your email" >
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" class="form-control" id="password" placeholder="Create a password" >
      </div>
      <div class="mb-3">
        <label for="confirm_password" class="form-label">Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Confirm your password" >
      </div>
      <button type="submit" class="btn btn-primary">Register</button>
    </form>
    <div class="form-footer">
      <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
  </div>
</div>

<!-- Bootstrap JS (Optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
