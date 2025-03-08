<?php
session_start();
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => ''];

try {
    if (!isset($_SESSION['otp'], $_SESSION['otp_expiry'], $_SESSION['otp_email'])) {
        throw new Exception('OTP not generated');
    }

    if (time() > $_SESSION['otp_expiry']) {
        session_unset();
        throw new Exception('OTP has expired');
    }

    $userOtp = $_POST['otp'] ?? '';
    if (!preg_match('/^\d{6}$/', $userOtp)) {
        throw new Exception('Invalid OTP format');
    }

    if ($userOtp !== $_SESSION['otp']) {
        throw new Exception('Invalid OTP');
    }

    $_SESSION['email_verified'] = true;
    $response = ['status' => 'success', 'message' => 'Email verified'];
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>