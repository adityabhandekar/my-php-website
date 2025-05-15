<?php
session_start();
include('dbConnection.php');

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    header("Location: my_orders.php");
    exit();
}

$order_id = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);
$user_id = $_SESSION['user_id'] ?? null;

// Get order details
try {
    $stmt = $conn->prepare("
        SELECT o.*, u.username, u.email as user_email 
        FROM orders_tb o
        LEFT JOIN userinfo_tb u ON o.user_id = u.id
        WHERE o.id = ? AND (o.user_id = ? OR ? IS NULL)
    ");
    $stmt->bind_param("iii", $order_id, $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();
    
    if (!$order) {
        header("Location: my_orders.php?error=order_not_found");
        exit();
    }
} catch (Exception $e) {
    header("Location: my_orders.php?error=database_error");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt - Cleanify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .receipt-container, .receipt-container * {
                visibility: visible;
            }
            .receipt-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
        .receipt-header {
            border-bottom: 2px dashed #dee2e6;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .receipt-footer {
            border-top: 2px dashed #dee2e6;
            padding-top: 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card receipt-container">
                    <div class="card-body p-4">
                        <!-- Print Button (hidden when printing) -->
                        <div class="text-end mb-4 no-print">
                            <button onclick="window.print()" class="btn btn-primary">
                                <i class="bi bi-printer"></i> Print Receipt
                            </button>
                            <a href="my_orders.php" class="btn btn-outline-secondary ms-2">
                                Back to My Orders
                            </a>
                        </div>
                        
                        <!-- Receipt Header -->
                        <div class="text-center receipt-header">
                            <h2>Cleanify</h2>
                            <p class="text-muted mb-0">Professional Cleaning Services</p>
                            <p class="text-muted">support@cleanify.com | +91 1111111111</p>
                        </div>
                        
                        <!-- Order Info -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Order Information</h5>
                                <p><strong>Order ID:</strong> #<?= $order['id'] ?></p>
                                <p><strong>Date:</strong> <?= date('F j, Y H:i', strtotime($order['order_date'])) ?></p>
                                <p><strong>Status:</strong> 
                                    <span class="badge bg-<?= 
                                        $order['order_status'] == 'Completed' ? 'success' : 
                                        ($order['order_status'] == 'Cancelled' ? 'danger' : 'warning') ?>">
                                        <?= $order['order_status'] ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6 text-end">
                                <h5>Customer Information</h5>
                                <p><?= htmlspecialchars($order['username']) ?></p>
                                <p><?= htmlspecialchars($order['user_email']) ?></p>
                                <p><?= htmlspecialchars($order['phone']) ?></p>
                            </div>
                        </div>
                        
                        <!-- Service Details -->
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Service</th>
                                    <th>Description</th>
                                    <th class="text-end">Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?= htmlspecialchars($order['service_title']) ?></td>
                                    <td><?= htmlspecialchars($order['service_description']) ?></td>
                                    <td class="text-end">₹<?= number_format($order['service_rate'], 2) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-end"><strong>Total Amount</strong></td>
                                    <td class="text-end"><strong>₹<?= number_format($order['amount'], 2) ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <!-- Delivery Info -->
                        <div class="mb-4">
                            <h5>Delivery Information</h5>
                            <p><?= htmlspecialchars($order['full_name']) ?></p>
                            <p><?= htmlspecialchars($order['address']) ?></p>
                            <p><?= htmlspecialchars($order['pincode']) ?></p>
                        </div>
                        
                        <!-- Payment Info -->
                        <div class="mb-4">
                            <h5>Payment Information</h5>
                            <p><strong>Transaction ID:</strong> <?= htmlspecialchars($order['transaction_id']) ?></p>
                            <p><strong>Payment Method:</strong> UPI Payment</p>
                            <p><strong>Payment Status:</strong> Paid</p>
                        </div>
                        
                        <!-- Receipt Footer -->
                        <div class="text-center receipt-footer">
                            <p class="text-muted mb-1">Thank you for choosing Cleanify!</p>
                            <p class="text-muted">For any queries, please contact our support team</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>