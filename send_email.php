<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }
    
    // Get admin email from database
    $stmt = $pdo->prepare("SELECT email FROM users LIMIT 1");
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $to = $user['email'] ?? 'admin@example.com';
    
    // Prepare email headers
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    // Send email
    $emailSubject = "New Contact Form Submission: $subject";
    $emailBody = "You have received a new message from your portfolio contact form.\n\n";
    $emailBody .= "Name: $name\n";
    $emailBody .= "Email: $email\n\n";
    $emailBody .= "Message:\n$message\n";
    
    if (mail($to, $emailSubject, $emailBody, $headers)) {
        echo json_encode(['success' => true, 'message' => 'Message sent successfully!']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to send message']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>