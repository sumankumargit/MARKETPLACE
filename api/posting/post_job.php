<?php
require_once '../../config/config.php';
require_once '../../config/db.php';

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
    // Prepare SQL query using PDO
    $stmt = $conn->prepare("INSERT INTO jobs (poster_id, title, description, requirements, expiration) 
                            VALUES (:poster_id, :title, :description, :requirements, :expiration)");

    // Bind parameters
    $stmt->bindParam(':poster_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
    $stmt->bindParam(':requirements', $requirements, PDO::PARAM_STR);
    $stmt->bindParam(':expiration', $expiration_datetime, PDO::PARAM_STR);

    // Execute query
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Job posted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to post the job.']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}

// Close connection
$conn = null;
?>
