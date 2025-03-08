
<?php

// get_job.php - Fetch a single job for editing
require_once '../../config/config.php';
require_once '../../config/db.php';


header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'poster') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

$job_id = $_GET['job_id'] ?? '';

$db = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("SELECT job_id, title, description, requirements, expiration FROM jobs WHERE job_id = ? AND poster_id = ?");
$stmt->execute([$job_id, $_SESSION['user_id']]);

$job = $stmt->fetch(PDO::FETCH_ASSOC);

if ($job) {
    echo json_encode(['status' => 'success', 'data' => $job]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Job not found.']);
}
?>

