<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - CareLink</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #003C7E;
            --secondary: #FFD700;
            --accent: #4CAF50;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            color: #333;
            line-height: 1.6;
        }
        .navbar {
            background-color: var(--primary);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar .logo {
            height: 50px;
            transition: transform 0.3s;
        }
        .navbar .logo:hover {
            transform: scale(1.05);
        }
        .navbar .nav-links {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        .navbar .nav-links a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .navbar .nav-links a:hover {
            color: var(--secondary);
            transform: translateY(-2px);
        }
        .navbar .nav-links a i {
            font-size: 1.1rem;
        }
        .logout-btn {
            background-color: var(--secondary);
            color: var(--primary);
            padding: 0.5rem 1.2rem;
            border: none;
            border-radius: 30px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .logout-btn:hover {
            background-color: #e6c200;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .welcome-card {
            background: linear-gradient(135deg, var(--primary), #0056b3);
            color: #ffffff;
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        .welcome-card::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        .welcome-card h2 {
            margin-top: 0;
            font-size: 2.2rem;
            position: relative;
        }
        .welcome-card h2 i {
            margin-right: 0.8rem;
            color: var(--secondary);
        }
        .welcome-card p {
            font-size: 1.1rem;
            line-height: 1.7;
            max-width: 80%;
            position: relative;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        .feature-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        .feature-card i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #e6f0ff, #d0e2ff);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .feature-card h3 {
            margin: 0.5rem 0;
            color: var(--primary);
        }
        .feature-card p {
            color: #666;
            margin-bottom: 1rem;
        }
        .feature-link {
            margin-top: auto;
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: color 0.3s;
        }
        .feature-link:hover {
            color: #0056b3;
        }
        .feature-link i {
            font-size: 1rem;
            background: none;
            width: auto;
            height: auto;
            margin: 0;
        }
        .quote-section {
            background: url('images/mental-health-bg.jpg') center/cover no-repeat;
            padding: 3rem 2rem;
            border-radius: 16px;
            margin: 2rem 0;
            position: relative;
            color: white;
            text-align: center;
        }
        .quote-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,60,126,0.8);
            border-radius: 16px;
        }
        .quote-content {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
        }
        .quote {
            font-size: 1.5rem;
            font-style: italic;
            margin-bottom: 1rem;
            line-height: 1.6;
        }
        .author {
            font-weight: 600;
            font-size: 1.1rem;
        }
        .emergency-banner {
            background-color: #ff6b6b;
            color: white;
            padding: 1rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
            box-shadow: 0 4px 10px rgba(255,107,107,0.3);
        }
        .emergency-banner i {
            font-size: 1.5rem;
        }
        .emergency-banner a {
            color: white;
            font-weight: 600;
            text-decoration: underline;
            margin-left: 0.5rem;
        }
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                padding: 1rem;
            }
            .nav-links {
                margin-top: 1rem;
                flex-wrap: wrap;
                justify-content: center;
            }
            .welcome-card p {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <div class="navbar">
        <img src="images/cputlogo.jpeg" alt="CPUT Logo" class="logo">
        <div class="nav-links">
            <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
            <a href="library.php"><i class="fas fa-book"></i> Library</a>
            <a href="appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a>
            <a href="scheduled.php"><i class="fas fa-list"></i> Scheduled</a>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container">
        <div class="welcome-card">
            <h2><i class="fas fa-heart"></i> Welcome to CareLink</h2>
            <p>
                At CareLink, we are dedicated to nurturing a compassionate and resilient school community.
                Our platform serves as a safe haven for both students and staff, offering resources and support
                to navigate the challenges of academic and personal life. By promoting mental well-being and
                open communication, we aim to empower every individual to thrive and contribute positively to
                our school's culture.
            </p>
        </div>

        <!-- Features Grid -->
        <div class="features-grid">
            <div class="feature-card">
                <i class="fas fa-book-open"></i>
                <h3>Resource Library</h3>
                <p>Access our collection of mental health resources, self-help guides, and wellness materials.</p>
                <a href="library.php" class="feature-link">Explore Resources <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <div class="feature-card">
                <i class="fas fa-calendar-alt"></i>
                <h3>Book Appointment</h3>
                <p>Schedule a confidential counseling session with our professional team.</p>
                <a href="appointments.php" class="feature-link">Book Now <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <div class="feature-card">
                <i class="fas fa-users"></i>
                <h3>Support Groups</h3>
                <p>Join our community support groups and connect with others who understand.</p>
                <a href="groups.php" class="feature-link">Learn More <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <!-- NEW VIDEO SESSION FEATURE CARD -->
            <div class="feature-card">
                <i class="fas fa-video"></i>
                <h3>Video Sessions</h3>
                <p>Join secure video counseling sessions with your therapist or student.</p>
                <?php if (isset($_SESSION['upcoming_appointment_id'])): ?>
                    <a href="videochat.php?appointment_id=<?php echo $_SESSION['upcoming_appointment_id']; ?>" class="feature-link">
                        Join Session <i class="fas fa-arrow-right"></i>
                    </a>
                <?php else: ?>
                    <span class="feature-link" style="color:#666; cursor:default;">
                        No upcoming sessions
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Inspirational Quote Section -->
        <div class="quote-section">
            <div class="quote-content">
                <div class="quote">
                    "Mental health is not a destination, but a process. It's about how you drive, not where you're going."
                </div>
                <div class="author">- Noam Shpancer, PhD</div>
            </div>
        </div>

        <!-- Emergency Banner -->
        <div class="emergency-banner">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>Need immediate help?</strong> Contact our 24/7 crisis line at <a href="tel:+27211234567">021 123 4567</a>
            </div>
        </div>
    </div>

    <!-- Chatbot Widget -->
    <div id="carelink-chatbot" style="position: fixed; bottom: 20px; right: 20px; width: 350px; background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); z-index: 1000; display: none; border: 1px solid #e0e0e0;">
        <div style="background: #003C7E; color: white; padding: 12px 15px; border-radius: 10px 10px 0 0; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px;"><i class="fas fa-robot" style="margin-right: 8px;"></i> CareLink Assistant</h3>
            <button onclick="toggleCarelinkChatbot()" style="background: none; border: none; color: white; cursor: pointer; font-size: 16px;">×</button>
        </div>
        <div id="carelink-chatbox" style="height: 300px; overflow-y: auto; padding: 15px; background: #f9f9f9;"></div>
        <div style="padding: 10px; background: white; border-top: 1px solid #eee; display: flex;">
            <input type="text" id="carelink-userInput" placeholder="Type your message..." style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 20px; outline: none;">
            <button onclick="sendCarelinkMessage()" style="background: #003C7E; color: white; border: none; border-radius: 50%; width: 40px; height: 40px; margin-left: 10px; cursor: pointer;"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <!-- Chatbot Toggle Button -->
    <button onclick="toggleCarelinkChatbot()" style="position: fixed; bottom: 20px; right: 20px; background: #003C7E; color: white; width: 60px; height: 60px; border-radius: 50%; border: none; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.2); z-index: 999;">
        <i class="fas fa-robot"></i>
    </button>

    <!-- Chatbot Script -->
    <script>
        // Toggle chatbot visibility
        function toggleCarelinkChatbot() {
            const chatbot = document.getElementById('carelink-chatbot');
            chatbot.style.display = chatbot.style.display === 'block' ? 'none' : 'block';
        }

        // Send message to chatbot
        function sendCarelinkMessage() {
            const userInput = document.getElementById('carelink-userInput');
            const chatbox = document.getElementById('carelink-chatbox');
            const message = userInput.value.trim();

            if (!message) return;

            // Display user message
            chatbox.innerHTML += `
                <div style="margin-bottom: 10px; text-align: right;">
                    <div style="background: #003C7E; color: white; display: inline-block; padding: 8px 12px; border-radius: 18px 18px 0 18px; max-width: 80%;">
                        ${message}
                    </div>
                </div>
            `;
            userInput.value = '';

            // Scroll to bottom
            chatbox.scrollTop = chatbox.scrollHeight;

            // Fetch bot reply (replace with your chatbot.php endpoint)
            fetch('chatbot.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `message=${encodeURIComponent(message)}`
            })
            .then(response => response.json())
            .then(data => {
                // Display bot reply
                chatbox.innerHTML += `
                    <div style="margin-bottom: 10px;">
                        <div style="background: #f1f1f1; color: #333; display: inline-block; padding: 8px 12px; border-radius: 18px 18px 18px 0; max-width: 80%;">
                            ${data.reply}
                        </div>
                    </div>
                `;
                chatbox.scrollTop = chatbox.scrollHeight;
            });
        }

        // Send message on Enter key
        document.getElementById('carelink-userInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') sendCarelinkMessage();
        });

        // Optional: Auto-open after 5 seconds
        setTimeout(() => {
            document.getElementById('carelink-chatbot').style.display = 'block';
        }, 5000);
    </script>
</body>
</html>