<?php
require_once '../../config/config.php';
require_once '../../config/db.php';
require '../../vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'bidder') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

$db = new Database();
$conn = $db->connect();

$job_id = $_POST['job_id'] ?? null;
$bid_amount = $_POST['bid_amount'] ?? null;
$bidder_id = $_SESSION['user_id'];

if (!$job_id || !$bid_amount || !is_numeric($bid_amount) || $bid_amount <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
    exit();
}

try {
    // Check if the job is still open
    $stmt = $conn->prepare("SELECT * FROM jobs WHERE job_id = :job_id AND expiration > NOW() AND is_closed = FALSE");
    $stmt->execute(['job_id' => $job_id]);
    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$job) {
        echo json_encode(['status' => 'error', 'message' => 'Job not found or expired.']);
        exit();
    }

    // Check if the bidder already placed a bid
    $stmt = $conn->prepare("SELECT * FROM bids WHERE job_id = :job_id AND bidder_id = :bidder_id");
    $stmt->execute(['job_id' => $job_id, 'bidder_id' => $bidder_id]);
    $existingBid = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingBid) {
        echo json_encode(['status' => 'error', 'message' => 'You have already placed a bid for this job.']);
        exit();
    }

    // Insert the bid
    $stmt = $conn->prepare("INSERT INTO bids (job_id, bidder_id, bid_amount) VALUES (:job_id, :bidder_id, :bid_amount)");
    $stmt->execute([
        'job_id' => $job_id,
        'bidder_id' => $bidder_id,
        'bid_amount' => $bid_amount
    ]);

    // Fetch bidder details
    $stmt = $conn->prepare("SELECT name, email FROM users WHERE user_id = :bidder_id");
    $stmt->execute(['bidder_id' => $bidder_id]);
    $bidder = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch job poster details
    $stmt = $conn->prepare("SELECT name, email FROM users WHERE user_id = :poster_id");
    $stmt->execute(['poster_id' => $job['poster_id']]);
    $poster = $stmt->fetch(PDO::FETCH_ASSOC);

    // Send emails to bidder and poster
    sendEmail(
        $bidder['email'], 
        "Bid Confirmation - {$job['title']}", 
        "Hello {$bidder['name']},<br><br>
        You have successfully placed a bid of <strong>₹{$bid_amount}</strong> on the job: <strong>{$job['title']}</strong>.<br><br>
        <strong>Job Details:</strong><br>
        Title: {$job['title']}<br>
        Description: {$job['description']}<br>
        Requirements: {$job['requirements']}<br>
        Expiration Date: {$job['expiration']}<br><br>
        Thank you for bidding on MarketPlace!"
    );

    sendEmail(
        $poster['email'], 
        "New Bid on Your Job - {$job['title']}", 
        "Hello {$poster['name']},<br><br>
        A user has placed a bid on your job: <strong>{$job['title']}</strong>.<br><br>
        <strong>Bidder Details:</strong><br>
        Name: {$bidder['name']}<br>
        Email: {$bidder['email']}<br>
        Bid Amount: <strong>₹{$bid_amount}</strong><br><br>
        <strong>Job Details:</strong><br>
        Title: {$job['title']}<br>
        Description: {$job['description']}<br>
        Requirements: {$job['requirements']}<br>
        Expiration Date: {$job['expiration']}<br><br>
        Please review the bid and take necessary action.<br><br>
        Regards,<br>
        MarketPlace Team"
    );

    echo json_encode(['status' => 'success', 'message' => 'Bid placed successfully!']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to place bid: ' . $e->getMessage()]);
}

// Function to send email using Gmail SMTP
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
        $mail->setFrom('sumankumarchoudhary733@gmail.com', 'MARKET PLACE');
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
