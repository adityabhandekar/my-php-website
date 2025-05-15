<?php
include('dbConnection.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, email, name, password FROM userinfo_tb WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $name, $email, $hashed_password);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        echo "<script>alert('Login successful'); window.location.href='services.php';</script>";
    } else {
        echo "<script>alert('Invalid credentials'); window.location.href='login.html';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
