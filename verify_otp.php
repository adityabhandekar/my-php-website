<?php
session_start();
include('dbConnection.php');

// Redirect if no OTP request was made
if (!isset($_SESSION['reset_user_id'])) {
    echo "<script>alert('Unauthorized access!'); window.location.href='forgot_password.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entered_otp = trim($_POST['otp']);
    $user_id = $_SESSION['reset_user_id'];

    // Fetch the correct OTP and expiry time from the database
    $stmt = $conn->prepare("SELECT reset_token, token_expiry FROM userinfo_tb WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $stored_otp = $row['reset_token'];
        $expiry = $row['token_expiry'];

        if ($entered_otp === $stored_otp && strtotime($expiry) > time()) {
            // OTP verified
            $_SESSION['otp_verified'] = true;
            header("Location: reset_password.php");
            exit;
        } else {
            echo "<script>alert('Invalid or expired OTP');</script>";
        }
    } else {
        echo "<script>alert('User not found.'); window.location.href='forgot_password.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP - Cleanify</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card mx-auto" style="max-width: 400px;">
            <div class="card-body">
                <h4 class="card-title text-center">Verify OTP</h4>
                <form method="POST">
                    <div class="mb-3">
                        <label for="otp" class="form-label">Enter OTP</label>
                        <input type="text" class="form-control" id="otp" name="otp" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Verify</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
