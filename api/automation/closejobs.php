<?php
require_once '../../config/config.php';
require_once '../../config/db.php';

$db = new Database();
$conn = $db->connect();

try {
    // Get the current timestamp
    $current_time = date('Y-m-d H:i:s');

    // Update jobs that have expired
    $sql = "UPDATE jobs SET is_closed = TRUE WHERE expiration <= ? AND is_closed = FALSE";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$current_time]);

    // Check how many rows were updated
    $affected_rows = $stmt->rowCount();
    
    echo json_encode(["status" => "success", "message" => "$affected_rows jobs updated successfully."]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
