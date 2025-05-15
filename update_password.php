<?php
include('dbConnection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE userinfo_tb SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $new_password, $user_id);
    
    if ($stmt->execute()) {
        // Delete token after use
        $conn->query("DELETE FROM password_resets WHERE user_id = $user_id");
        echo "<script>alert('Password updated successfully.'); window.location='login.html';</script>";
    } else {
        echo "<script>alert('Failed to update password.');</script>";
    }
}
?>
