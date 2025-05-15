<?php
// Include database connection
include('dbConnection.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['fName'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Prepare and bind the SQL statement to insert the data
    $stmt = $conn->prepare("INSERT INTO contact_tb (fName, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message); // 'sss' means three strings

    // Execute the query
    if ($stmt->execute()) {
        echo "<script>alert('Thank you for contacting us!'); window.location.href='contact.html';</script>";
    } else {
        echo "<script>alert('Something went wrong. Please try again.'); window.location.href='contact.html';</script>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
