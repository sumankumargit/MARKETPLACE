<?php

require_once '../../config/config.php';
require_once '../../vendor/autoload.php';

header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$response = ['status' => 'error', 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if (!$email) {
        throw new Exception('Invalid email address');
    }

    // Generate 6-digit OTP
    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_email'] = $email;
    $_SESSION['otp_expiry'] = time() + 300; // 5 minutes expiry

    // Send email using PHPMailer
    $mail = new PHPMailer(true);
    
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'sumankumarchoudhary733@gmail.com'; // Use your Gmail
    $mail->Password = 'gxdd wmcl kzbh pegv'; // Use your app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    $mail->setFrom('sumankumarchoudhary733@gmail.com', 'MARKET PLACE');
    $mail->addAddress($email);
    $mail->Subject = 'Your Verification Code';
    $mail->Body = "Your verification code is: $otp\nThis code expires in 5 minutes.";

    $mail->send();
    $response = ['status' => 'success', 'message' => 'OTP sent successfully'];
} catch (Exception $e) {
    $response['message'] = 'Error sending OTP: ' . $e->getMessage();
}

echo json_encode($response);
?>