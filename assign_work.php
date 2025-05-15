<?php
include('dbConnection.php');
session_start();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['order_status'];

    $stmt = $conn->prepare("UPDATE orders_tb SET order_status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Order status updated successfully');</script>";
}

// Fetch all assigned orders
$query = "SELECT * FROM orders_tb WHERE technician_name IS NOT NULL";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assigned Work</title>
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
                <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="technicians.php">Technicians</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_profile.php">Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_services.php">manage Services</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="mb-4">Assigned Work</h2>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Technician</th>
                <th>Service</th>
                <th>Appointment</th>
                <th>Status</th>
                <th>Update Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['technician_name']) ?></td>
                <td><?= htmlspecialchars($row['service_title']) ?></td>
                <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['order_status']) ?></span></td>
                <td>
                    <form method="POST" class="d-flex gap-2">
                        <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                        <select name="order_status" class="form-select">
                            <option value="Pending" <?= $row['order_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="In Progress" <?= $row['order_status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="Completed" <?= $row['order_status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="Canceled" <?= $row['order_status'] == 'Canceled' ? 'selected' : '' ?>>Canceled</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
