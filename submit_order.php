<?php
include('dbConnection.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $service_id = $_POST['service_id'];
    $service_title = $_POST['service_title'];
    $service_description = $_POST['service_description'];
    $service_rate = $_POST['service_rate'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $pincode = $_POST['pincode'];
    $appointment_date = $_POST['appointment_date'];
    $order_status = $_POST['order_status'];

    // Validate file upload
    if (isset($_FILES['payment_screenshot']) && $_FILES['payment_screenshot']['error'] === 0) {
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_tmp = $_FILES['payment_screenshot']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['payment_screenshot']['name']);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file_tmp, $file_path)) {
            // Check if the database connection is successful
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Prepare SQL statement
            $stmt = $conn->prepare("INSERT INTO orders_tb (
                user_id, service_id, service_title, service_description, service_rate,
                full_name, email, phone, address, pincode, appointment_date, payment_screenshot, order_status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            if (!$stmt) {
                echo "Error in query preparation: " . $conn->error;
                exit();
            }

            // Bind parameters
            $stmt->bind_param("iissdssssssiss", 
                $user_id, $service_id, $service_title, $service_description, $service_rate,
                $full_name, $email, $phone, $address, $pincode, $appointment_date, $file_path, $order_status
            );

            // Execute the query
            if ($stmt->execute()) {
                echo "<script>alert('Order placed successfully!'); window.location.href='my_orders.php';</script>";
            } else {
                echo "<script>alert('Error placing order. Please try again.'); window.history.back();</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Failed to upload screenshot'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Please upload a valid payment screenshot'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Invalid access'); window.location.href='services.php';</script>";
}
