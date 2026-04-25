<?php
session_start();
require 'includes/db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        $stmt = $conn->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc();
                    if (password_verify($password, $user['password_hash'])) {
                        $_SESSION['logged_in'] = true;
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['role'] = $user['role'];
                        header("Location: dashboard.php");
                        exit();
                    } else {
                        $error = 'Invalid credentials';
                    }
                } else {
                    $error = 'Invalid credentials';
                }
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CareLink | Student Counseling Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #0056b3;
      --secondary: #ffc107;
      --light: #ffffff;
      --dark: #343a40;
      --danger: #dc3545;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body, html {
      height: 100%;
      font-family: 'Poppins', sans-serif;
      background: url('images/background.jpg') no-repeat center center/cover;
      background-attachment: fixed;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
    }

    .overlay {
      background: rgba(255, 255, 255, 0.4); /* More transparent */
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      z-index: 1;
      backdrop-filter: blur(4px); /* Optional: light blur for 'glass' effect */
    }

    .login-wrapper {
      position: relative;
      z-index: 2;
      max-width: 450px;
      width: 90%;
      background: rgba(255, 255, 255, 0.85); /* Slightly transparent background for login box */
      padding: 3rem 2rem;
      border-radius: 15px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
      text-align: center;
      animation: slideIn 1s ease forwards;
    }

    @keyframes slideIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .logo {
      width: 140px;
      height: auto;
      margin-bottom: 1rem;
      object-fit: contain;
      border: none;
    }

    h1 {
      font-size: 2rem;
      color: var(--primary);
      margin-bottom: 0.5rem;
    }

    p.welcome-text {
      color: var(--dark);
      margin-bottom: 2rem;
      font-size: 1rem;
    }

    .form-group {
      margin-bottom: 1.5rem;
      text-align: left;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      color: var(--dark);
      font-weight: 500;
    }

    .form-control {
      width: 100%;
      padding: 0.8rem;
      border: 1px solid #ced4da;
      border-radius: 8px;
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: var(--primary);
      outline: none;
      box-shadow: 0 0 0 0.2rem rgba(0, 86, 179, 0.2);
    }

    .btn-login {
      width: 100%;
      padding: 0.8rem;
      background: var(--primary);
      color: var(--light);
      font-size: 1rem;
      font-weight: bold;
      border: none;
      border-radius: 30px;
      cursor: pointer;
      transition: background 0.3s;
      margin-top: 1rem;
    }

    .btn-login:hover {
      background: #004494;
    }

    .error-message {
      background: rgba(220, 53, 69, 0.1);
      color: var(--danger);
      padding: 0.75rem;
      border-left: 5px solid var(--danger);
      border-radius: 8px;
      margin-bottom: 1.5rem;
      font-size: 0.9rem;
      text-align: left;
    }

    .login-footer {
      margin-top: 2rem;
      font-size: 0.9rem;
      color: var(--dark);
    }

    .login-footer a {
      color: var(--primary);
      font-weight: 600;
      text-decoration: none;
    }

    .login-footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>

<div class="overlay"></div>

<div class="login-wrapper">
  <img src="images/cputlogo.jpeg" alt="CareLink Logo" class="logo">
  <h1>Welcome Back!</h1>
  <p class="welcome-text">We're here to support you. Please sign in to continue your journey with us.</p>

  <?php if (!empty($error)): ?>
    <div class="error-message">
      <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
    </div>
  <?php endif; ?>

  <form action="login.php" method="POST">
    <div class="form-group">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required>
    </div>

    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
    </div>

    <button type="submit" class="btn-login">Sign In</button>

    <div class="login-footer">
      Don't have an account? <a href="register.php">Register here</a>
    </div>
  </form>
</div>

</body>
</html>

