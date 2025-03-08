<?php
session_start();
require_once '../../config/db.php';

// Establish database connection
$db = new Database();
$conn = $db->connect();

// Ensure the request is a POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    sendResponse("error", "Invalid request method.");
}

// Helper function to send JSON responses
function sendResponse($status, $message) {
    echo json_encode(["status" => $status, "message" => $message]);
    exit;
}

// Get and sanitize input data
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$user_type = $_POST['user_type'] ?? '';

// Validate input fields
if (strlen($name) < 3) {
    sendResponse("error", "Name must be at least 3 characters long.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendResponse("error", "Invalid email format.");
}

if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
    sendResponse("error", "Invalid phone number format.");
}

if (strlen($password) < 6) {
    sendResponse("error", "Password must be at least 6 characters long.");
}

if (!in_array($user_type, ['poster', 'bidder'])) {
    sendResponse("error", "Invalid user type.");
}

// Hash the password
$password_hash = password_hash($password, PASSWORD_BCRYPT);

// Check if email or phone already exists
$stmt = $conn->prepare("SELECT email, phone FROM users WHERE email = ? OR phone = ?");
$stmt->execute([$email, $phone]);

if ($stmt->fetch()) {
    sendResponse("error", "Email or phone already registered.");
}

// Insert user into the database
$stmt = $conn->prepare("INSERT INTO users (name, email, phone, password_hash, user_type) VALUES (?, ?, ?, ?, ?)");
$result = $stmt->execute([$name, $email, $phone, $password_hash, $user_type]);

if ($result) {
    sendResponse("success", "Registration successful.");
} else {
    sendResponse("error", "Registration failed. Please try again.");
}
?>
