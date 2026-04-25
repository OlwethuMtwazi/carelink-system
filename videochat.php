<?php
// videochat.php
session_start();

// 1. Basic Authentication (won't interfere with your existing login)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// 2. Get appointment ID (safe validation)
$appointment_id = isset($_GET['appointment_id']) ? (int)$_GET['appointment_id'] : 0;

// 3. Set user details (with fallbacks)
$user_name = $_SESSION['full_name'] ?? 'Participant';
$user_role = $_SESSION['user_type'] ?? 'guest'; // counselor/student
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Video Session | CareLink</title>
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
        }
        
        .video-header {
            background: linear-gradient(135deg, var(--primary), #0056b3);
            color: white;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .video-header h1 {
            margin: 0;
            font-size: 1.8rem;
        }
        
        .video-header p {
            margin: 0.5rem 0 0;
            opacity: 0.9;
        }
        
        #video-container {
            width: 90%;
            max-width: 1200px;
            height: 70vh;
            margin: 2rem auto;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            background: var(--dark);
            position: relative;
        }
        
        .video-controls {
            text-align: center;
            margin: 1.5rem auto;
            max-width: 1200px;
        }
        
        .control-btn {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            margin: 0 10px;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.3s;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .control-btn:hover {
            transform: scale(1.1);
            background: var(--secondary);
            color: var(--primary);
        }
        
        .control-btn.end-call {
            background: #e74c3c;
        }
        
        .user-badge {
            position: absolute;
            bottom: 20px;
            left: 20px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            z-index: 100;
        }
        
        .user-badge i {
            margin-right: 8px;
            color: var(--secondary);
        }
        
        @media (max-width: 768px) {
            #video-container {
                height: 50vh;
                width: 95%;
            }
            
            .control-btn {
                width: 45px;
                height: 45px;
                margin: 0 5px;
            }
        }
    </style>
</head>
<body>
    <div class="video-header">
        <h1><i class="fas fa-video"></i> Counseling Session</h1>
        <p>Appointment #<?php echo $appointment_id; ?></p>
    </div>
    
    <div id="video-container">
        <div class="user-badge">
            <i class="fas fa-user"></i>
            <?php echo htmlspecialchars($user_name); ?> (<?php echo $user_role; ?>)
        </div>
    </div>
    
    <div class="video-controls">
        <button class="control-btn" onclick="toggleMic()">
            <i class="fas fa-microphone"></i>
        </button>
        <button class="control-btn" onclick="toggleCamera()">
            <i class="fas fa-video"></i>
        </button>
        <button class="control-btn end-call" onclick="endCall()">
            <i class="fas fa-phone-slash"></i>
        </button>
    </div>

    <!-- Jitsi API (No account needed) -->
    <script src='https://meet.jit.si/external_api.js'></script>
    <script>
        // Generate room name with random component for privacy
        const roomName = `CareLink-<?php echo $appointment_id; ?>-${Math.random().toString(36).substr(2, 5)}`;
        
        const options = {
            roomName: roomName,
            width: '100%',
            height: '100%',
            parentNode: document.getElementById('video-container'),
            userInfo: {
                displayName: '<?php echo $user_name . " (" . $user_role . ")"; ?>',
            },
            interfaceConfigOverwrite: {
                SHOW_JITSI_WATERMARK: false,
                SHOW_WATERMARK_FOR_GUESTS: false,
                MOBILE_APP_PROMO: false,
                DISABLE_JOIN_LEAVE_NOTIFICATIONS: true,
                TOOLBAR_BUTTONS: [
                    'microphone', 'camera', 'closedcaptions', 'desktop', 
                    'fullscreen', 'hangup', 'chat', 'settings'
                ],
            },
            configOverwrite: {
                disableSimulcast: true,
                startWithAudioMuted: false,
                startWithVideoMuted: false,
                enableWelcomePage: false,
                enableNoisyMicDetection: false,
                prejoinPageEnabled: false
            }
        };

        const api = new JitsiMeetExternalAPI('meet.jit.si', options);
        
        // Custom control functions
        function toggleMic() {
            api.executeCommand('toggleAudio');
            document.querySelector('.fa-microphone').classList.toggle('fa-microphone-slash');
        }
        
        function toggleCamera() {
            api.executeCommand('toggleVideo');
            document.querySelector('.fa-video').classList.toggle('fa-video-slash');
        }
        
        function endCall() {
            api.executeCommand('hangup');
            setTimeout(() => {
                window.location.href = 'dashboard.php?call_ended=1';
            }, 500);
        }
        
        // Handle call termination
        api.addEventListener('readyToClose', () => {
            window.location.href = 'dashboard.php?call_ended=1';
        });
    </script>
</body>
</html>