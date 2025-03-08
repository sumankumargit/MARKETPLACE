
<?php

// delete_job.php - Delete a job
require_once '../../config/config.php';
require_once '../../config/db.php';


header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'poster') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

$job_id = $_POST['job_id'] ?? '';

$db = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("DELETE FROM jobs WHERE job_id = ? AND poster_id = ?");

if ($stmt->execute([$job_id, $_SESSION['user_id']])) {
    echo json_encode(['status' => 'success', 'message' => 'Job deleted successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete job.']);
}
?>
