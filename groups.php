<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$db = new mysqli('localhost', 'root', '', 'cput_carelink');
if ($db->connect_error) {
    die("Database connection failed: " . $db->connect_error);
}

$group_sessions = [];
$query = "SELECT * FROM appointments WHERE counseling_type = 'Group' ORDER BY date, time_slot";
$result = $db->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $group_sessions[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Group Hub - CareLink</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #005BAA; /* Your website's original blue */
            --secondary: #FFD700;
            --light-bg: #f0f2f5;
            --card-bg: #ffffff;
        }
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--light-bg);
        }
        .navbar {
            background-color: var(--primary);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0.5rem 1rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .navbar a:hover {
            color: var(--secondary);
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 1rem;
        }
        .header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .header h2 {
            color: var(--primary);
            margin: 0.5rem 0;
        }
        .group-grid {
            display: grid;
            gap: 2rem;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }
        .group-card {
            background: var(--card-bg);
            border-left: 6px solid var(--primary);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            padding: 1.5rem;
            transition: transform 0.2s ease;
        }
        .group-card:hover {
            transform: translateY(-5px);
        }
        .group-title {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        .group-time {
            font-weight: 500;
            margin-bottom: 0.3rem;
        }
        .group-info {
            font-size: 0.95rem;
            color: #444;
            margin-bottom: 0.5rem;
        }
        .no-groups {
            text-align: center;
            color: #777;
            font-style: italic;
            margin-top: 3rem;
        }
        .tag {
            display: inline-block;
            background: var(--secondary);
            color: #000;
            padding: 0.2rem 0.6rem;
            font-size: 0.75rem;
            font-weight: bold;
            border-radius: 4px;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>

<!-- Navigation -->
<div class="navbar">
    <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
    <a href="library.php"><i class="fas fa-book"></i> Library</a>
    <a href="appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a>
    <a href="scheduled.php"><i class="fas fa-list"></i> Scheduled</a>
    <a href="group.php"><i class="fas fa-users"></i> Group Hub</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Page Content -->
<div class="container">
    <div class="header">
        <h2>Student Group Support Hub</h2>
        <p>Connect. Share. Grow. Join one of our weekly group sessions below.</p>
    </div>

    <?php if (empty($group_sessions)): ?>
        <div class="no-groups">No group sessions available at the moment. Check back soon!</div>
    <?php else: ?>
        <div class="group-grid">
            <?php foreach ($group_sessions as $session): ?>
                <div class="group-card">
                    <div class="group-title">
                        <?php echo htmlspecialchars($session['group_name'] ?? 'Support Group'); ?>
                    </div>
                    <div class="group-time">
                        <?php echo date("l, j F Y", strtotime($session['date'])); ?> @ <?php echo $session['time_slot']; ?>
                    </div>
                    <div class="group-info">
                        Campus: <strong><?php echo htmlspecialchars($session['campus']); ?></strong><br>
                        Facilitator: <?php echo htmlspecialchars($session['name']); ?>
                    </div>
                    <span class="tag"><?php echo ucfirst(strtolower($session['counseling_type'])); ?> Session</span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
