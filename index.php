<?php
// Start session if not already started
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareLink - Welcome</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 90%;
        }
        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 1.5rem;
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        a.button {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            margin: 0.5rem;
            background-color: #ffffff;
            color: #2575fc;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s, color 0.3s;
        }
        a.button:hover {
            background-color: #2575fc;
            color: #ffffff;
        }
    </style>
</head>
<body>

    <div class="container">
        <img src="images/cputlogo.jpeg" alt="CareLink Logo" class="logo">
        <h1>Welcome to CareLink</h1>
        <p>Your portal to better support, connection, and service delivery.</p>

        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
            <a href="dashboard.php" class="button">Go to Dashboard</a>
            <a href="logout.php" class="button">Logout</a>
        <?php else: ?>
            <a href="login.php" class="button">Login</a>
            <a href="register.php" class="button">Register</a>
        <?php endif; ?>
    </div>

</body>
</html>


