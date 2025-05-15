<?php
include('dbConnection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $name     = $_POST['name'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Prepare the SQL query
    $stmt = $conn->prepare("INSERT INTO userinfo_tb (username, email, name, password) VALUES (?, ?, ?, ?)");

    // Check if preparation failed
    if ($stmt === false) {
        // Output error details
        die('MySQL prepare error: ' . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("ssss", $username, $email, $name, $password);

    // Execute and check for success
    if ($stmt->execute()) {
        echo "<script>alert('Registered successfully'); window.location.href='login.html';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
