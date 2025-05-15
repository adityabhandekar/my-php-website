<?php
include('dbConnection.php');

$order_id = $_GET['order_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $technician_id = $_POST['technician_id'];
    $appointment_date = $_POST['order_date'];
    $order_status = $_POST['order_status'];

    // Get technician name from ID
    $tech_stmt = $conn->prepare("SELECT technician_name FROM technicians_tb WHERE id = ?");
    if ($tech_stmt === false) {
        die('Error preparing technician query: ' . $conn->error);
    }
    $tech_stmt->bind_param("i", $technician_id);
    $tech_stmt->execute();
    $tech_result = $tech_stmt->get_result();
    $tech_data = $tech_result->fetch_assoc();
    $technician_name = $tech_data['technician_name'];
    $tech_stmt->close();

    // Update order
    $stmt = $conn->prepare("UPDATE orders_tb SET technician_name=?, order_date=?, order_status=? WHERE id=?");
    if ($stmt === false) {
        die('Error preparing update query: ' . $conn->error);
    }
    $stmt->bind_param("sssi", $technician_name, $appointment_date, $order_status, $order_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Order Assigned'); window.location.href='orders.php';</script>";
    exit;
}

// Fetch order info
$order_result = $conn->query("SELECT * FROM orders_tb WHERE id = $order_id");
if ($order_result === false) {
    die('Error fetching order: ' . $conn->error);
}
$order = $order_result->fetch_assoc();

// Fetch all technicians
$techs_result = $conn->query("SELECT id, technician_name FROM technicians_tb");
if ($techs_result === false) {
    die('Error fetching technicians: ' . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Order</title>
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
                <li class="nav-item"><a class="nav-link" href="manage_services.php">Manage Services</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h3>Assign Order #<?= $order_id ?></h3>
    <form method="post">
        <div class="mb-3">
            <label for="technician_id">Technician</label>
            <select name="technician_id" id="technician_id" class="form-select" required>
                <option value="" disabled selected>-- Select Technician --</option>
                <?php while ($row = $techs_result->fetch_assoc()) { ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['technician_name']) ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="appointment_date">Appointment Date</label>
            <input type="date" name="appointment_date" id="appointment_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="order_status" class="form-select" required>
                <option value="Pending">Pending</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
                <option value="Cancle">Cancle</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Assign</button>
        <a href="orders.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
