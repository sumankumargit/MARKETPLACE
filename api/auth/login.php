<?php
session_start();
require_once '../../config/db.php';

// Establish database connection
$db = new Database();
$conn = $db->connect();

// Helper function to send a JSON response
function sendResponse($status, $message) {
    echo json_encode(["status" => $status, "message" => $message]);
    exit;
}

// Check if request is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    sendResponse("error", "Invalid request method.");
}

// Get input data
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validate input fields
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendResponse("error", "Invalid email format.");
}

if (empty($password)) {
    sendResponse("error", "Password is required.");
}

// Check if user exists
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password_hash'])) {
    sendResponse("error", "Invalid email or password.");
}

// Set session and send success response
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_type'] = $user['user_type'];
$_SESSION['user_enail'] = $user['email'];



sendResponse("success", "Login successful.");
?>
