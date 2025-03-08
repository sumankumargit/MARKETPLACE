<?php
require_once '../../config/config.php';
require_once '../../config/db.php';


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

    echo json_encode(['status' => 'success', 'message' => 'Bid placed successfully!']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to place bid: ' . $e->getMessage()]);
}
?>
