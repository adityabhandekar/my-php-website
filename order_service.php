<?php
include('dbConnection.php');
session_start();

// Check user login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Validate service ID
if (!isset($_GET['id'])) {
    echo "<script>alert('Invalid request'); window.location.href='services.php';</script>";
    exit;
}

$service_id = $_GET['id'];

// Fetch service details
$stmt = $conn->prepare("SELECT * FROM services_tb WHERE id = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    die("Execution failed: " . $conn->error);
}
$service = $result->fetch_assoc();
$stmt->close();

if (!$service) {
    echo "<script>alert('Service not found'); window.location.href='services.php';</script>";
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Service - Cleanify</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .logo {
        font-size: 1.5rem;
        font-weight: bold;
        text-align: center;
    }
    .box {
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 20px;
        background-color: #fff;
        margin-bottom: 20px;
    }
    .payment-box img {
        width: 200px;
        display: block;
        margin: auto;
    }
  </style>
</head>
<body class="bg-light">
<div class="container my-4">
    <div class="logo mb-4">🧹 Cleanify</div>

    <!-- Service Summary -->
    <div class="box">
        <h5>Service Details</h5>
        <p><strong>Title:</strong> <?php echo htmlspecialchars($service['title']); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($service['description']); ?></p>
        <p class="fw-bold text-primary"><strong>Rate:</strong> ₹<?php echo htmlspecialchars($service['rate']); ?></p>
    </div>

    <!-- Delivery Details -->
    <div class="box">
        <h5>Edit Delivery Details</h5>
        <form action="payment.php" method="POST">
            <input type="hidden" name="service_id" value="<?php echo $service_id; ?>">
            <input type="hidden" name="service_title" value="<?php echo htmlspecialchars($service['title']); ?>">
            <input type="hidden" name="service_description" value="<?php echo htmlspecialchars($service['description']); ?>">
            <input type="hidden" name="service_rate" value="<?php echo htmlspecialchars($service['rate']); ?>">

            <div class="mb-2">
                <label class="form-label">Full Name*</label>
                <input type="text" name="full_name" class="form-control" value="<?php echo $_SESSION['user_name'] ?? ''; ?>" required>
            </div>

            <div class="mb-2">
                <label class="form-label">Email Address*</label>
                <input type="email" name="email" class="form-control" value="<?php echo $_SESSION['email'] ?? ''; ?>" required>
            </div>

            <div class="mb-2">
                <label class="form-label">Phone Number*</label>
                <input type="tel" name="phone" class="form-control" value="<?php echo $_SESSION['phone'] ?? ''; ?>" pattern="[0-9]{10}" required>
            </div>

            <div class="mb-2">
                <label class="form-label">Address*</label>
                <textarea name="address" class="form-control" rows="3" required><?php echo $_SESSION['address'] ?? ''; ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Pin Code*</label>
                <input type="text" name="pincode" class="form-control" value="<?php echo $_SESSION['pincode'] ?? ''; ?>" pattern="[0-9]{6}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Appointment Date*</label>
                <input type="date" name="appointment_date" class="form-control" required>
            </div>

            <!-- Place Order Button -->
            <button type="submit" class="btn btn-success w-100">Place Order</button>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
