<?php
session_start();
include('dbConnection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['reset_user_id'])) {
    $entered_otp = $_POST['otp'];
    $user_id = $_SESSION['reset_user_id'];

    // Retrieve the stored OTP and expiry time
    $stmt = $conn->prepare("SELECT reset_token, token_expiry FROM userinfo_tb WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($db_otp, $expiry);
    $stmt->fetch();
    $stmt->close();

    // Check if OTP is valid and not expired
    if ($entered_otp == $db_otp && strtotime($expiry) > time()) {
        $_SESSION['otp_verified'] = true;
        header("Location: reset_password.php");
    } else {
        echo "<script>alert('Invalid or expired OTP'); window.location.href='verify_otp.php';</script>";
    }
}
