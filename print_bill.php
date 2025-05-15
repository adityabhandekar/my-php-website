<?php
include('dbConnection.php');

if (!isset($_GET['order_id'])) {
    echo "Invalid order ID";
    exit;
}

$order_id = intval($_GET['order_id']);

$stmt = $conn->prepare("SELECT * FROM orders_tb WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Order not found.";
    exit;
}

$order = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Service Bill - Order #<?= $order['id'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .bill-box {
            max-width: 750px;
            margin: 50px auto;
            border: 1px solid #333;
            padding: 30px;
            background: #fff;
        }
        .signature {
            height: 80px;
            border-bottom: 1px solid #000;
            margin-top: 40px;
            margin-bottom: 10px;
        }
        .text-right {
            text-align: right;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="bill-box">
    <h3 class="text-center mb-4">Cleanify - Service Bill</h3>
    <p><strong>Billing Date:</strong> <?= date("Y-m-d") ?></p>
    <p><strong>Order ID:</strong> <?= $order['id'] ?></p>
    <p><strong>Customer Name:</strong> <?= htmlspecialchars($order['full_name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
    <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($order['address'])) ?></p>
    <p><strong>Service:</strong> <?= htmlspecialchars($order['service_title']) ?></p>
    <p><strong>Appointment Date:</strong> <?= $order['appointment_date'] ?></p>
    <p><strong>Price:</strong> ₹<?= $order['service_rate'] ?></p>
    <p><strong>Technician:</strong> <?= htmlspecialchars($order['technician_name']) ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars($order['order_status']) ?></p>

    <div class="row mt-5">
        <div class="col-6">
            <p>Customer Signature:</p>
            <div class="signature"></div>
        </div>
        <div class="col-6 text-right">
            <p>Technician Signature:</p>
            <div class="signature"></div>
        </div>
    </div>

    <p class="text-center mt-4">Thank you for choosing Cleanify!</p>
</div>

<div class="text-center mt-3 no-print">
    <button onclick="window.print()" class="btn btn-success">Print</button>
    <a href="assigned_work.php" class="btn btn-secondary">Back</a>
</div>

</body>
</html>
