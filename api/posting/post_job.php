<?php
require_once '../../config/config.php';
require_once '../../config/db.php';
require '../../vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Enable error logging for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Check if the user is logged in and has the 'poster' role
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'poster') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

// Validate and sanitize input data
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$requirements = trim($_POST['requirements'] ?? '');
$expiration = trim($_POST['expiration'] ?? '');

// Check for empty fields
if (empty($title) || empty($description) || empty($requirements) || empty($expiration)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit();
}

// Convert expiration to valid datetime
$expiration_datetime = date('Y-m-d H:i:s', strtotime($expiration));

// Database connection
$db = new Database();
$conn = $db->connect();

if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit();
}

try {
    // Insert job into the database
    $stmt = $conn->prepare("INSERT INTO jobs (poster_id, title, description, requirements, expiration) 
                            VALUES (:poster_id, :title, :description, :requirements, :expiration)");
    
    $stmt->execute([
        'poster_id' => $_SESSION['user_id'],
        'title' => $title,
        'description' => $description,
        'requirements' => $requirements,
        'expiration' => $expiration_datetime
    ]);
    
    // Send email notifications
    $poster_email = $_SESSION['user_email'];
    $poster_name = $_SESSION['user_name'] ?? 'Job Poster';
    
    // Email to poster
    sendEmail($poster_email, "Job Posted Successfully - $title", 
        "Hello $poster_name,<br><br>
        Your job <strong>$title</strong> has been posted successfully.<br><br>
        Regards,<br>MarketPlace Team");

    // Fetch all bidders
    $stmt = $conn->prepare("SELECT name, email FROM users WHERE user_type = 'bidder'");
    $stmt->execute();
    $bidders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Email to all bidders
    foreach ($bidders as $bidder) {
        sendEmail($bidder['email'], "New Job Posted - $title",
            "Hello {$bidder['name']},<br><br>
            A new job <strong>$title</strong> has been posted by <strong>$poster_name</strong>.<br><br>
            <strong>Job Details:</strong><br>
            Description: $description<br>
            Requirements: $requirements<br>
            Expiration Date: $expiration<br><br>
            Log in to apply.<br><br>
            Regards,<br>MarketPlace Team");
    }

    echo json_encode(['status' => 'success', 'message' => 'Job posted successfully. Emails sent.']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn = null;

// Function to send emails
function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'sumankumarchoudhary733@gmail.com';
        $mail->Password = 'gxdd wmcl kzbh pegv';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        $mail->setFrom('sumankumarchoudhary733@gmail.com', 'MarketPlace');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->send();
    } catch (Exception $e) {
        error_log("Email could not be sent to $to. Error: {$mail->ErrorInfo}");
    }
}
?>
