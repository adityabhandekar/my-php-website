<?php
include('dbConnection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $rating = intval($_POST['rating']);
    $feedback = trim($_POST['feedback_text']);

    $stmt = $conn->prepare("INSERT INTO feedback (name, email, rating, feedback_text) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $name, $email, $rating, $feedback);

    if ($stmt->execute()) {
        header("Location: index.php?feedback=success");
        exit;
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
