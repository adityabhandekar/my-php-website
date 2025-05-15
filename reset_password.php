<?php
session_start();
include('dbConnection.php');

// Ensure the user has verified OTP
if (!isset($_SESSION['reset_user_id']) || !isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
    echo "<script>alert('Unauthorized access!'); window.location.href='forgot_password.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($new_password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $user_id = $_SESSION['reset_user_id'];

        $stmt = $conn->prepare("UPDATE userinfo_tb SET password = ?, reset_token = NULL, token_expiry = NULL WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);

        if ($stmt->execute()) {
            // Clear session
            unset($_SESSION['reset_user_id']);
            unset($_SESSION['otp_verified']);
            echo "<script>alert('Password reset successfully. Please log in.'); window.location.href='login.html';</script>";
            exit;
        } else {
            echo "<script>alert('Failed to reset password. Try again later.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password - Cleanify</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card mx-auto" style="max-width: 400px;">
            <div class="card-body">
                <h4 class="card-title text-center">Reset Password</h4>
                    <form method="POST" onsubmit="return validatePassword();">
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <div class="form-text" id="strengthMsg" style="color: red;"></div>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Reset Password</button>
                    </form>

            </div>
        </div>
    </div>
            <script>
            function validatePassword() {
                const password = document.getElementById('new_password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                const msg = document.getElementById('strengthMsg');

                const strengthRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/;

                if (!strengthRegex.test(password)) {
                    msg.textContent = "Password must be at least 8 characters long and include 1 uppercase letter, 1 number, and 1 special character.";
                    return false;
                } else {
                    msg.textContent = "";
                }

                if (password !== confirmPassword) {
                    alert("Passwords do not match.");
                    return false;
                }

                return true;
            }
        </script>

</body>
</html>
