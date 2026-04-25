<?php
// Get user message from POST request
$userMessage = strtolower(trim($_POST['message'] ?? ''));

// Define bot responses with keywords for various site pages
$responses = [
    "hi" => "Hello! How can I help you today?",
    "hello" => "Hi there! How can I assist you?",
    "hey" => "Hey! What can I help you with?",
    "how are you" => "I'm just a bot, but I'm functioning well!",

    "appointment" => "You can book appointments in <a href='appointments.php'>appointments.php</a>.",
    "library" => "Visit the <a href='library.php'>library section</a> for resources.",
    "books" => "Check out available books in the <a href='library.php'>books section</a>.",
    "login" => "Go to <a href='login.php'>login.php</a> to access your account.",
    "register" => "Create an account at <a href='register.php'>register.php</a>.",
    "schedule" => "View your schedule at <a href='scheduled.php'>scheduled.php</a>.",
    "my schedule" => "Your schedule is available at <a href='scheduled.php'>scheduled.php</a>.",
    "support" => "Join our support groups via the <a href='groups.php'>support groups page</a>.",
    "groups" => "Find a support group on the <a href='support-groups.php'>support groups page</a>.",
    "video" => "Watch video sessions in the <a href='videochat.php'>videos section</a>.",
    "sessions" => "Video sessions are available at <a href='videochat.php'>videos section</a>.",
    "contact" => "Check out our contact page at <a href='contact.php'>contact.php</a>.",
    "about" => "Learn more about us on the <a href='about.php'>about page</a>.",
    "services" => "See what we offer on the <a href='appointments.php'>services page</a>.",
    "home" => "Return to the homepage at <a href='index.php'>index.php</a>.",
    
    "bye" => "Goodbye! Come back soon!",
    "default" => "I didn't understand. Try keywords like 'appointment', 'support', 'video', 'books', or 'register'."
];

// Default reply
$botReply = $responses['default'];

// Match keywords
foreach ($responses as $keyword => $response) {
    if ($keyword !== 'default' && strpos($userMessage, $keyword) !== false) {
        $botReply = $response;
        break;
    }
}

// Return response
header('Content-Type: application/json');
echo json_encode(["reply" => $botReply]);
?>

