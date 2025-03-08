<?php
// delete_bid.php
require_once '../../config/config.php';
require_once '../../config/db.php';

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

$stmt = $conn->prepare("DELETE FROM bids WHERE bid_id = :bid_id AND bidder_id = :bidder_id");
$stmt->execute(['bid_id' => $bid_id, 'bidder_id' => $bidder_id]);

if ($stmt->rowCount() > 0) {
    echo json_encode(['status' => 'success', 'message' => 'Bid deleted successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete bid or bid not found']);
}

exit();
?>
