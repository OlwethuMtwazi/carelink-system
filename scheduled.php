<?php
session_start();

// Protect the page
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Database connection
$db = new mysqli('localhost', 'root', '', 'cput_carelink');
if ($db->connect_error) {
    die("Database connection failed: " . $db->connect_error);
}

// Get all appointments
$appointments = array();
$result = $db->query("SELECT * FROM appointments WHERE counseling_type = 'Individual' ORDER BY date, time_slot");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scheduled Appointments - CareLink</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <style>
        :root {
            --primary: #003C7E;
            --secondary: #FFD700;
            --accent: #4CAF50;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            color: #333;
            line-height: 1.6;
            margin: 0;
        }
        .navbar {
            background-color: var(--primary);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .navbar a:hover {
            color: var(--secondary);
            transform: translateY(-2px);
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .calendar-container {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .calendar-title {
            color: var(--primary);
            margin: 0;
        }
        .calendar-nav button {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 0.5rem 1rem;
            cursor: pointer;
            margin: 0 0.5rem;
        }
        .calendar {
            width: 100%;
            border-collapse: collapse;
        }
        .calendar th {
            background: var(--primary);
            color: white;
            padding: 1rem;
            text-align: center;
        }
        .calendar td {
            border: 1px solid #ddd;
            padding: 0;
            height: 100px;
            vertical-align: top;
        }
        .calendar-day {
            padding: 0.5rem;
            height: 100%;
        }
        .day-number {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .appointment {
            background: #e6f2ff;
            border-left: 3px solid var(--primary);
            padding: 0.5rem;
            margin: 0.25rem 0;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        .appointment-time {
            font-weight: bold;
            color: var(--primary);
        }
        .past-appointment {
            text-decoration: line-through;
            opacity: 0.6;
        }
        .no-appointments {
            color: #666;
            font-style: italic;
            padding: 1rem;
            text-align: center;
        }
        @media (max-width: 768px) {
            .calendar th, .calendar td {
                padding: 0.25rem;
                font-size: 0.8rem;
            }
            .appointment {
                font-size: 0.7rem;
            }
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
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Calendar Container -->
<div class="container">
    <div class="calendar-container">
        <div class="calendar-header">
            <h2 class="calendar-title">Individual Counseling Sessions</h2>
            <div class="calendar-nav">
                <button id="prev-month"><i class="fas fa-chevron-left"></i> Previous</button>
                <span id="current-month">Month Year</span>
                <button id="next-month">Next <i class="fas fa-chevron-right"></i></button>
            </div>
        </div>

        <?php if (empty($appointments)): ?>
            <div class="no-appointments">
                <p>No individual counseling sessions have been scheduled yet.</p>
            </div>
        <?php else: ?>
            <table class="calendar" id="appointment-calendar">
                <thead>
                    <tr>
                        <th>Sunday</th>
                        <th>Monday</th>
                        <th>Tuesday</th>
                        <th>Wednesday</th>
                        <th>Thursday</th>
                        <th>Friday</th>
                        <th>Saturday</th>
                    </tr>
                </thead>
                <tbody id="calendar-body">
                    <!-- Calendar will be generated by JavaScript -->
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    const appointments = <?php echo json_encode($appointments); ?>;
    let currentDate = new Date();
    renderCalendar(currentDate);

    $('#prev-month').click(function() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
    });

    $('#next-month').click(function() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
    });

    function renderCalendar(date) {
        const monthNames = ["January", "February", "March", "April", "May", "June",
                            "July", "August", "September", "October", "November", "December"];
        $('#current-month').text(monthNames[date.getMonth()] + ' ' + date.getFullYear());

        const firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
        const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
        const daysInMonth = lastDay.getDate();

        let calendarBody = '';
        let dateCounter = 1;
        let dayOfWeek = firstDay.getDay();
        const now = new Date();

        for (let i = 0; i < 6; i++) {
            if (dateCounter > daysInMonth) break;

            calendarBody += '<tr>';
            for (let j = 0; j < 7; j++) {
                if ((i === 0 && j < dayOfWeek) || dateCounter > daysInMonth) {
                    calendarBody += '<td><div class="calendar-day"></div></td>';
                } else {
                    const dateStr = date.getFullYear() + '-' + 
                                    String(date.getMonth() + 1).padStart(2, '0') + '-' + 
                                    String(dateCounter).padStart(2, '0');

                    const dayAppointments = appointments.filter(apt => apt.date === dateStr);
                    calendarBody += '<td><div class="calendar-day">';
                    calendarBody += '<div class="day-number">' + dateCounter + '</div>';

                    if (dayAppointments.length > 0) {
                        dayAppointments.forEach(apt => {
                            const aptDateTime = new Date(apt.date + 'T' + apt.time_slot);
                            const pastClass = aptDateTime < now ? 'past-appointment' : '';
                            calendarBody += `<div class="appointment ${pastClass}">
                                <div class="appointment-time">${apt.time_slot}</div>
                                <div>${apt.name} (${apt.student_id})</div>
                                <div>${apt.campus} Campus</div>
                            </div>`;
                        });
                    }

                    calendarBody += '</div></td>';
                    dateCounter++;
                }
            }
            calendarBody += '</tr>';
        }

        $('#calendar-body').html(calendarBody);
    }
});
</script>

</body>
</html>
