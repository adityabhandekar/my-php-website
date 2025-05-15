<?php
include('dbConnection.php');
session_start();

// Fetch all orders
$sql = "SELECT * FROM orders_tb";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Orders - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 80px; /* Adjust this value based on the height of your navbar */
        }
    </style>
</head>
<body>
<!-- Navbar/Menu -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Cleanify Admin</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link fw-bold" href="orders.php">Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="technicians.php">Technicians</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_profile.php">Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_services.php">Manage Services</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_feedbacks.php">Manage Feedback</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="mb-4">All Orders</h2>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Mobile Number</th>
                <th>Amount</th>
                <th>Transaction ID</th>
                <th>Service</th>
                <th>Status</th>
                <th>Technician</th>
                <th>Date</th>
                <th>Assign</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['phone'])?></td>
                <td><?= htmlspecialchars($row['amount'])?></td>
                <td><?= htmlspecialchars($row['transaction_id'])?></td>
                <td><?= htmlspecialchars($row['service_title']) ?></td>
                <td><?= htmlspecialchars($row['order_status']) ?></td>
                <td><?= htmlspecialchars($row['technician_name'] ?: 'Not Assigned') ?></td>
                <td><?= htmlspecialchars($row['order_date'] ?: 'N/A') ?></td>
                <td>
                    <a href="assign_order.php?order_id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Assign</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
