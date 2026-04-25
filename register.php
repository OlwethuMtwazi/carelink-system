<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'includes/db_connect.php'; // Ensure this path is correct

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = 'Username already taken. Please choose another.';
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $created_at = date('Y-m-d H:i:s');
        $is_staff = ($role === 'staff') ? 1 : 0;

        $insert_stmt = $conn->prepare("INSERT INTO users (username, password_hash, role, created_at, is_staff) VALUES (?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("ssssi", $username, $password_hash, $role, $created_at, $is_staff);

        if ($insert_stmt->execute()) {
            header("Location: login.php?message=registered");
            exit();
        } else {
            $error = "Error: " . $insert_stmt->error;
        }

        $insert_stmt->close();
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - CareLink</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body, html {
      height: 100%;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #0056b3, #003c7e, #001f4d);
      background-size: 400% 400%;
      animation: gradientBG 10s ease infinite;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
    }

    @keyframes gradientBG {
      0% {background-position: 0% 50%;}
      50% {background-position: 100% 50%;}
      100% {background-position: 0% 50%;}
    }

    .container {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      padding: 3rem 2rem;
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
      width: 90%;
      max-width: 420px;
      text-align: center;
      animation: fadeIn 1.5s ease forwards;
      color: #ffffff;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .logo {
      width: 120px;
      height: auto;
      margin-bottom: 1.5rem;
      filter: drop-shadow(0 0 5px #ffffffaa);
    }

    h1 {
      font-size: 2rem;
      margin-bottom: 1rem;
      color: #ffffff;
    }

    .form-group {
      margin-bottom: 1.5rem;
      text-align: left;
    }

    label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: #e0e0e0;
    }

    input[type="text"], input[type="password"], select {
      width: 100%;
      padding: 0.8rem;
      border: none;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.2);
      color: #ffffff;
      font-size: 1rem;
      transition: background 0.3s;
    }

    input[type="text"]::placeholder,
    input[type="password"]::placeholder {
      color: #d1d1d1;
    }

    input[type="text"]:focus,
    input[type="password"]:focus,
    select:focus {
      background: rgba(255, 255, 255, 0.3);
      outline: none;
    }

    button {
      width: 100%;
      padding: 0.9rem;
      background: #ffffff;
      color: #003c7e;
      font-weight: 600;
      font-size: 1rem;
      border: none;
      border-radius: 30px;
      margin-top: 1rem;
      cursor: pointer;
      transition: 0.3s ease;
    }

    button:hover {
      background: #003c7e;
      color: #ffffff;
    }

    .link {
      margin-top: 1.5rem;
      font-size: 0.9rem;
    }

    .link a {
      color: #ffffff;
      font-weight: 600;
      text-decoration: underline;
    }

    .link a:hover {
      color: #ffc107;
    }

    .error {
      background: rgba(220, 53, 69, 0.2);
      color: #f8d7da;
      padding: 0.75rem;
      border-radius: 10px;
      margin-bottom: 1.5rem;
      font-size: 0.9rem;
    }
  </style>
</head>

<body>

<div class="container">
  <img src="images/cputlogo.jpeg" alt="CPUT Logo" class="logo">
  
  <h1>Create Your Account</h1>

  <?php if (!empty($error)): ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <form action="register.php" method="POST">
    <div class="form-group">
      <label for="username">Username</label>
      <input type="text" name="username" id="username" required placeholder="Enter your username">
    </div>

    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" name="password" id="password" required placeholder="Create a password">
    </div>

    <div class="form-group">
      <label for="role">Role</label>
      <select name="role" id="role" required>
        <option value="student">Student</option>
        <option value="staff">Staff</option>
      </select>
    </div>

    <button type="submit">Sign Up</button>

    <div class="link">
      Already have an account? <a href="login.php">Login here</a>
    </div>
  </form>
</div>

</body>
</html>
