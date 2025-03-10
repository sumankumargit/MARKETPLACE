<?php
// delete_bid.php
require_once '../../config/config.php';
require_once '../../config/db.php';
require '../../vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json'); // Set proper JSON response header

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'bidder') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$bidder_id = $_SESSION['user_id'];
$bid_id = $_POST['bid_id'] ?? null;

if (!$bid_id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid bid ID']);
    exit();
}

$db = new Database();
$conn = $db->connect();

// Fetch bid details
$stmt = $conn->prepare("SELECT b.job_id, j.title, j.description, j.requirements, j.expiration, j.poster_id 
                        FROM bids b
                        JOIN jobs j ON b.job_id = j.job_id
                        WHERE b.bid_id = :bid_id AND b.bidder_id = :bidder_id");
$stmt->execute(['bid_id' => $bid_id, 'bidder_id' => $bidder_id]);
$bid = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bid) {
    echo json_encode(['status' => 'error', 'message' => 'Bid not found or unauthorized']);
    exit();
}

// Delete the bid
$stmt = $conn->prepare("DELETE FROM bids WHERE bid_id = :bid_id AND bidder_id = :bidder_id");
$stmt->execute(['bid_id' => $bid_id, 'bidder_id' => $bidder_id]);

if ($stmt->rowCount() > 0) {
    // Fetch bidder details
    $stmt = $conn->prepare("SELECT name, email FROM users WHERE user_id = :bidder_id");
    $stmt->execute(['bidder_id' => $bidder_id]);
    $bidder = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch job poster details
    $stmt = $conn->prepare("SELECT name, email FROM users WHERE user_id = :poster_id");
    $stmt->execute(['poster_id' => $bid['poster_id']]);
    $poster = $stmt->fetch(PDO::FETCH_ASSOC);

    // Send email to bidder
    sendEmail(
        $bidder['email'],
        "Bid Removed - {$bid['title']}",
        "Hello {$bidder['name']},<br><br>
        Your bid on the job <strong>{$bid['title']}</strong> has been successfully removed.<br><br>
        <strong>Job Details:</strong><br>
        Title: {$bid['title']}<br>
        Description: {$bid['description']}<br>
        Requirements: {$bid['requirements']}<br>
        Expiration Date: {$bid['expiration']}<br><br>
        If you removed this bid by mistake, you may place a new bid before the job expires.<br><br>
        Regards,<br>
        MarketPlace Team"
    );

    // Send email to job poster
    sendEmail(
        $poster['email'],
        "Bid Withdrawn from Your Job - {$bid['title']}",
        "Hello {$poster['name']},<br><br>
        A bidder has withdrawn their bid from your job: <strong>{$bid['title']}</strong>.<br><br>
        <strong>Bidder Details:</strong><br>
        Name: {$bidder['name']}<br>
        Email: {$bidder['email']}<br><br>
        <strong>Job Details:</strong><br>
        Title: {$bid['title']}<br>
        Description: {$bid['description']}<br>
        Requirements: {$bid['requirements']}<br>
        Expiration Date: {$bid['expiration']}<br><br>
        If you're looking for new bids, consider extending the job's deadline or reaching out to more bidders.<br><br>
        Regards,<br>
        MarketPlace Team"
    );

    echo json_encode(['status' => 'success', 'message' => 'Bid deleted successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete bid']);
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
