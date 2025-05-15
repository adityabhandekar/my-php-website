<?php
session_start();
include('dbConnection.php');
require_once('phpqrcode/qrlib.php');

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Validate POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['service_id'])) {
    echo "<script>alert('Invalid request.'); window.location.href='services.php';</script>";
    exit;
}

// Fetch service
$service_id = $_POST['service_id'];

$stmt = $conn->prepare("SELECT * FROM services_tb WHERE id = ?");
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();
$service = $result->fetch_assoc();
$stmt->close();

if (!$service) {
    echo "<script>alert('Service not found.'); window.location.href='services.php';</script>";
    exit;
}

// Delivery details from POST
$full_name = htmlspecialchars($_POST['full_name']);
$email = htmlspecialchars($_POST['email']);
$phone = htmlspecialchars($_POST['phone']);
$address = htmlspecialchars($_POST['address']);
$pincode = htmlspecialchars($_POST['pincode']);

$service_title = htmlspecialchars($service['title']);
$service_description = htmlspecialchars($service['description']);
$service_rate = htmlspecialchars($service['rate']);

// UPI QR generation
$upi_id = '9579356195@ybl';
$qr_data = "upi://pay?pa={$upi_id}&pn=Cleanify&am={$service_rate}&cu=INR&tn=Payment%20for%20{$service_title}";
$tempDir = 'temp_qr/';
if (!file_exists($tempDir)) mkdir($tempDir);
$qr_filename = $tempDir . 'qr_' . time() . '.png';
QRcode::png($qr_data, $qr_filename, QR_ECLEVEL_H, 5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment - Cleanify</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .box {
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 20px;
        background-color: #fff;
        margin-bottom: 20px;
    }
    .hidden {
        display: none;
    }
  </style>
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="text-center mb-4">
        <h2>Complete Payment</h2>
        <p class="text-muted">Scan the QR code and enter your transaction ID</p>
    </div>

    <!-- Service Info -->
    <div class="box">
        <h5>🧹 Service Details</h5>
        <p><strong>Title:</strong> <?= $service_title ?></p>
        <p><strong>Description:</strong> <?= $service_description ?></p>
        <p><strong>Rate:</strong> ₹<?= $service_rate ?></p>
    </div>

    <!-- User Info -->
    <div class="box">
        <h5>📦 Delivery Details</h5>
        <p><strong>Name:</strong> <?= $full_name ?></p>
        <p><strong>Email:</strong> <?= $email ?></p>
        <p><strong>Phone:</strong> <?= $phone ?></p>
        <p><strong>Address:</strong> <?= $address ?> - <?= $pincode ?></p>
    </div>

    <!-- QR Code -->
    <div class="text-center mb-4">
        <img src="<?= $qr_filename ?>" alt="Scan to Pay" class="img-fluid" width="200">
        <p class="mt-2 text-muted">Scan using any UPI app</p>
        <button class="btn btn-success mt-3" onclick="document.getElementById('confirmBox').classList.remove('hidden')">
            I’ve Scanned the QR
        </button>
    </div>

    <!-- Form for transaction ID and booking -->
    <div id="confirmBox" class="box hidden mt-4 mx-auto" style="max-width: 500px;">
        <h5 class="text-success">✅ Payment Initiated</h5>
        <form action="order_success.php" method="POST">
            <div class="mb-3">
                <label for="transaction_id" class="form-label">Transaction ID</label>
                <input type="text" name="transaction_id" id="transaction_id" class="form-control" placeholder="Enter Transaction ID" required>
            </div>

            <!-- Hidden data -->
            <input type="hidden" name="service_id" value="<?= $service_id ?>">
            <input type="hidden" name="service_title" value="<?= $service_title ?>">
            <input type="hidden" name="service_description" value="<?= $service_description ?>">
            <input type="hidden" name="service_rate" value="<?= $service_rate ?>">
            <input type="hidden" name="amount" value="<?= $service_rate ?>">
            <input type="hidden" name="full_name" value="<?= $full_name ?>">
            <input type="hidden" name="email" value="<?= $email ?>">
            <input type="hidden" name="phone" value="<?= $phone ?>">
            <input type="hidden" name="address" value="<?= $address ?>">
            <input type="hidden" name="pincode" value="<?= $pincode ?>">

            <button type="submit" class="btn btn-primary w-100 mt-3">Confirm Booking</button>
        </form>
    </div>
</div>
</body>
</html>
