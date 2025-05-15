<?php
session_start();
include('dbConnection.php');

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Validate POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['transaction_id'])) {
    echo "<script>alert('Invalid request.'); window.location.href='services.php';</script>";
    exit;
}

// Get all form data
$user_id = $_SESSION['user_id'];
$service_id = $_POST['service_id'];
$full_name = htmlspecialchars($_POST['full_name']);
$email = htmlspecialchars($_POST['email']);
$phone = htmlspecialchars($_POST['phone']);
$address = htmlspecialchars($_POST['address']);
$pincode = htmlspecialchars($_POST['pincode']);
$service_title = htmlspecialchars($_POST['service_title']);
$service_description = htmlspecialchars($_POST['service_description']);
$service_rate = htmlspecialchars($_POST['service_rate']);
$amount = htmlspecialchars($_POST['amount']);
$transaction_id = htmlspecialchars($_POST['transaction_id']);

// Default status is 'Pending' until verified
$order_status = 'Pending';

// Insert order into database
try {
    $stmt = $conn->prepare("INSERT INTO orders_tb (
        user_id, 
        service_id, 
        full_name, 
        email, 
        phone, 
        address, 
        pincode, 
        service_title, 
        service_description, 
        service_rate, 
        amount, 
        order_status, 
        transaction_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $bind_result = $stmt->bind_param(
        "iissssssssdss", 
        $user_id, 
        $service_id, 
        $full_name, 
        $email, 
        $phone, 
        $address, 
        $pincode, 
        $service_title, 
        $service_description, 
        $service_rate, 
        $amount, 
        $order_status, 
        $transaction_id
    );

    if (!$bind_result) {
        throw new Exception("Bind failed: " . $stmt->error);
    }

    $execute_result = $stmt->execute();
    
    if (!$execute_result) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $order_id = $conn->insert_id;
    $stmt->close();
    
    // Success - show confirmation page
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Order Confirmed - Cleanify</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            .confirmation-box {
                border: 1px solid #28a745;
                border-radius: 10px;
                padding: 30px;
                background-color: #f8f9fa;
                margin-top: 50px;
            }
            .checkmark {
                font-size: 72px;
                color: #28a745;
            }
            .error-details {
                display: none;
                background: #f8d7da;
                padding: 10px;
                border-radius: 5px;
                margin-top: 20px;
            }
        </style>
    </head>
    <body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="confirmation-box text-center">
                    <div class="checkmark mb-4">✓</div>
                    <h2 class="text-success">Order Confirmed!</h2>
                    <p class="lead">Thank you for your booking with Cleanify</p>
                    
                    <div class="card mt-4 mb-4">
                        <div class="card-body text-start">
                            <h5 class="card-title">Order Details</h5>
                            <p><strong>Order ID:</strong> #<?= $order_id ?></p>
                            <p><strong>Service:</strong> <?= $service_title ?></p>
                            <p><strong>Amount Paid:</strong> ₹<?= $amount ?></p>
                            <p><strong>Transaction ID:</strong> <?= $transaction_id ?></p>
                            <p><strong>Status:</strong> <span class="badge bg-warning"><?= $order_status ?></span></p>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <p>We'll contact you shortly to confirm your appointment details.</p>
                        <p>A confirmation has been sent to <?= $email ?></p>
                    </div>
                    
                    <a href="my_orders.php" class="btn btn-primary">View My Orders</a>
                    <a href="services.php" class="btn btn-outline-secondary">Back to Services</a>
                </div>
            </div>
        </div>
    </div>
    </body>
    </html>
    <?php
} catch (Exception $e) {
    // Detailed error handling
    error_log("Order Error: " . $e->getMessage());
    
    // Show error message to user with optional debug info
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Order Error - Cleanify</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
    <div class="container mt-5">
        <div class="alert alert-danger">
            <h4 class="alert-heading">Error Saving Your Order</h4>
            <p>We encountered an issue while processing your order. Please try again.</p>
            <hr>
            <p class="mb-0">If the problem persists, please contact support with the following details:</p>
            <button class="btn btn-sm btn-outline-danger mt-3" onclick="document.getElementById('errorDetails').style.display='block'">
                Show Technical Details
            </button>
            <div id="errorDetails" class="error-details mt-2">
                <?= htmlspecialchars($e->getMessage()) ?>
            </div>
        </div>
        <a href="services.php" class="btn btn-primary">Back to Services</a>
    </div>
    </body>
    </html>
    <?php
}
?>