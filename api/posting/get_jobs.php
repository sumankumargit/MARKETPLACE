<?php

// get_jobs.php - Fetch all jobs for the logged-in poster
require_once '../../config/config.php';
require_once '../../config/db.php';


header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'poster') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

$db = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("SELECT job_id, title, description, requirements, expiration FROM jobs WHERE poster_id = ?");
$stmt->execute([$_SESSION['user_id']]);

$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['data' => $jobs]);
?>

