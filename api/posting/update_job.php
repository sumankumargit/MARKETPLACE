
<?php

// update_job.php - Update job details
require_once '../../config/config.php';
require_once '../../config/db.php';


header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'poster') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

$job_id = $_POST['job_id'] ?? '';
$title = trim($_POST['title']);
$description = trim($_POST['description']);
$requirements = trim($_POST['requirements']);
$expiration = date('Y-m-d H:i:s', strtotime($_POST['expiration']));

$db = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("UPDATE jobs SET title = ?, description = ?, requirements = ?, expiration = ? WHERE job_id = ? AND poster_id = ?");

if ($stmt->execute([$title, $description, $requirements, $expiration, $job_id, $_SESSION['user_id']])) {
    echo json_encode(['status' => 'success', 'message' => 'Job updated successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update job.']);
}
?>

