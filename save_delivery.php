<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['user_name'] = $_POST['full_name'];
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['address'] = $_POST['address'] . ', ' . $_POST['city'] . ', ' . $_POST['state'] . ', ' . $_POST['pincode'];

    // Redirect back to the order page
    header("Location: order_service.php?id=1");
    exit();
}
?>
