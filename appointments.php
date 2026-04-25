<?php
session_start();

// Protect the page
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Database connection - Update these with your actual credentials
$db = new mysqli('localhost', 'root', '', 'cput_carelink');
if ($db->connect_error) {
    die("<div style='color:red;padding:20px;border:1px solid #f00;margin:20px;'>
        <h3>Database Connection Error</h3>
        <p>Error: ".$db->connect_error."</p>
        <p>Please check your database credentials and ensure:</p>
        <ol>
            <li>MySQL service is running</li>
            <li>Database 'cput_carelink' exists</li>
            <li>Table 'appointments' has all required columns</li>
        </ol>
        </div>");
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $student_id = $db->real_escape_string($_POST['username']); // Using username as student_id
    $name = $db->real_escape_string($_POST['username']); // Using same value for name
    $campus = $db->real_escape_string($_POST['campus']);
    $date = $db->real_escape_string($_POST['date']);
    $time_slot = $db->real_escape_string($_POST['time']);
    $counseling_type = $db->real_escape_string($_POST['counseling_type']);
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

    // Insert into database - matching your table structure
    $sql = "INSERT INTO appointments (user_id, name, student_id, campus, date, time_slot, counseling_type, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Scheduled')";
    
    $stmt = $db->prepare($sql);
    if ($stmt === false) {
        $error = "Prepare failed: " . $db->error;
    } else {
        $stmt->bind_param("issssss", $user_id, $name, $student_id, $campus, $date, $time_slot, $counseling_type);
        
        if ($stmt->execute()) {
            // START OF ADDED CODE - JUST THESE 2 LINES
            $new_appointment_id = $stmt->insert_id;
            $_SESSION['upcoming_appointment_id'] = $new_appointment_id;
            // END OF ADDED CODE
            
            $success = "Appointment booked successfully!";
            // Clear form after success
            $_POST = array();
        } else {
            $error = "Error booking appointment: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!-- EVERYTHING BELOW THIS LINE REMAINS COMPLETELY UNCHANGED -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Appointment - CareLink</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 1rem;
        }
        .navbar {
            background-color: #003C7E;
            padding: 1rem;
            display: flex;
            justify-content: center;
            gap: 2rem;
        }
        .navbar a {
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .container {
            background: #ffffff;
            margin: 2rem auto;
            padding: 2rem;
            border-radius: 12px;
            max-width: 500px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        h2 {
            text-align: center;
            color: #003C7E;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #003C7E;
        }
        input[type="text"], select, input[type="date"] {
            width: 100%;
            padding: 0.7rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
        }
        button {
            width: 100%;
            padding: 0.75rem;
            background-color: #003C7E;
            color: white;
            font-size: 1rem;
            font-weight: bold;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            margin-top: 1rem;
        }
        button:hover {
            background-color: #0056b3;
        }
        .booklets {
            margin-top: 2rem;
        }
        .booklets h3 {
            color: #003C7E;
            margin-bottom: 1rem;
        }
        .booklets img {
            width: 120px;
            margin-right: 1rem;
        }
        .counseling-type {
            display: flex;
            gap: 1rem;
            margin-top: 0.5rem;
        }
        .counseling-option {
            display: flex;
            align-items: center;
        }
        .counseling-option input {
            margin-right: 0.5rem;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
    <script>
        $(function() {
            $("#date").datepicker({
                dateFormat: 'yy-mm-dd',
                minDate: 1,
                maxDate: '+3M'
            });
        });
    </script>
</head>
<body>

<!-- Navigation -->
<div class="navbar">
    <a href="dashboard.php">Home</a>
    <a href="library.php">Library</a>
    <a href="appointments.php">Appointments</a>
    <a href="scheduled.php">Scheduled</a>
</div>

<!-- Appointment Form -->
<div class="container">
    <h2>Book Appointment</h2>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="appointments.php" method="POST">
        <div class="form-group">
            <label for="username">Student/Staff Number</label>
            <input type="text" id="username" name="username" 
                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                   placeholder="e.g. 220345678" required>
        </div>

        <div class="form-group">
            <label for="campus">Campus</label>
            <select id="campus" name="campus" required>
                <option value="">Select</option>
                <option value="District Six" <?php echo (isset($_POST['campus']) && $_POST['campus'] == 'District Six') ? 'selected' : ''; ?>>District Six</option>
                <option value="Bellville" <?php echo (isset($_POST['campus']) && $_POST['campus'] == 'Bellville') ? 'selected' : ''; ?>>Bellville</option>
                <option value="Wellington" <?php echo (isset($_POST['campus']) && $_POST['campus'] == 'Wellington') ? 'selected' : ''; ?>>Wellington</option>
                <option value="Granger Bay" <?php echo (isset($_POST['campus']) && $_POST['campus'] == 'Granger Bay') ? 'selected' : ''; ?>>Granger Bay</option>
            </select>
        </div>

        <div class="form-group">
            <label for="date">Date</label>
            <input type="text" id="date" name="date" 
                   value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : ''; ?>" 
                   placeholder="Click to select date" required>
        </div>

        <div class="form-group">
            <label for="time">Time Slot</label>
            <select id="time" name="time" required>
                <option value="">Select</option>
                <option value="09:00 - 10:00" <?php echo (isset($_POST['time']) && $_POST['time'] == '09:00 - 10:00') ? 'selected' : ''; ?>>09:00 - 10:00</option>
                <option value="10:00 - 11:00" <?php echo (isset($_POST['time']) && $_POST['time'] == '10:00 - 11:00') ? 'selected' : ''; ?>>10:00 - 11:00</option>
                <option value="11:00 - 12:00" <?php echo (isset($_POST['time']) && $_POST['time'] == '11:00 - 12:00') ? 'selected' : ''; ?>>11:00 - 12:00</option>
                <option value="14:00 - 15:00" <?php echo (isset($_POST['time']) && $_POST['time'] == '14:00 - 15:00') ? 'selected' : ''; ?>>14:00 - 15:00</option>
                <option value="15:00 - 16:00" <?php echo (isset($_POST['time']) && $_POST['time'] == '15:00 - 16:00') ? 'selected' : ''; ?>>15:00 - 16:00</option>
            </select>
        </div>

        <div class="form-group">
            <label>Counseling Type</label>
            <div class="counseling-type">
                <div class="counseling-option">
                    <input type="radio" id="individual" name="counseling_type" value="Individual" 
                           <?php echo (!isset($_POST['counseling_type']) || $_POST['counseling_type'] == 'Individual') ? 'checked' : ''; ?>>
                    <label for="individual">Individual</label>
                </div>
                <div class="counseling-option">
                    <input type="radio" id="group" name="counseling_type" value="Group"
                           <?php echo (isset($_POST['counseling_type']) && $_POST['counseling_type'] == 'Group') ? 'checked' : ''; ?>>
                    <label for="group">Group</label>
                </div>
            </div>
        </div>

        <button type="submit">Submit</button>
    </form>

    <!-- Health Booklets Section -->
    <div class="booklets">
        <h3>Health Booklets</h3>
        <img src="images/health.jpeg" alt="Health Booklet 1">
        <img src="images/health2.jpeg" alt="Health Booklet 2">
    </div>
</div>

</body>
</html>