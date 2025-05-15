<?php
session_start();
include('dbConnection.php');

// Fetch total orders
$order_query = "SELECT COUNT(*) AS total_orders FROM orders_tb";
$order_result = $conn->query($order_query);
if (!$order_result) die("Error fetching total orders: " . $conn->error);
$total_orders = $order_result->fetch_assoc()['total_orders'];

// Fetch assigned work
$assigned_query = "SELECT COUNT(*) AS assigned_work FROM orders_tb WHERE technician_name IS NOT NULL";
$assigned_result = $conn->query($assigned_query);
if (!$assigned_result) die("Error fetching assigned work: " . $conn->error);
$assigned_work = $assigned_result->fetch_assoc()['assigned_work'];  

// Fetch total technicians
$tech_query = "SELECT COUNT(*) AS total_techs FROM technicians_tb";
$tech_result = $conn->query($tech_query);
if (!$tech_result) die("Error fetching technicians: " . $conn->error);
$total_techs = $tech_result->fetch_assoc()['total_techs'];

// Fetch total users
$user_query = "SELECT COUNT(*) AS total_users FROM userinfo_tb";
$user_result = $conn->query($user_query);
if (!$user_result) die("Error fetching users: " . $conn->error);
$total_users = $user_result->fetch_assoc()['total_users'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Cleanify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 80px; /* Adjust this value based on your navbar height */
        }
        .dashboard-card {
            border-radius: 15px;
            padding: 30px;
            color: white;
        }
        footer {
            background-color:rgb(49, 49, 49);
            padding: 20px 0 20px;
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            color: white;
            text-align: center;
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
                <li class="nav-item"><a class="nav-link active" href="admin_dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="technicians.php">Technicians</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_profile.php">Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_services.php">Manage Servicess</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_feedbacks.php">Manage Feedback</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Dashboard -->
<div class="container mt-4">
    <h2 class="text-center mb-4">Admin Dashboard</h2>
    <div class="row row-cols-1 row-cols-md-4 g-4">
        <div class="col">
            <a href="orders.php" class="text-decoration-none">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body text-center">
                        <h5>Total Orders</h5>
                        <h3><?php echo $total_orders; ?></h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col">
            <a href="assigned_work.php" class="text-decoration-none">
                <div class="card bg-success text-white h-100">
                    <div class="card-body text-center">
                        <h4>Assigned Work</h4>
                        <p class="fs-3"><?php echo $assigned_work; ?></p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col">
            <a href="technicians.php" class="text-decoration-none">
                <div class="card bg-warning text-dark h-100">
                    <div class="card-body text-center">
                        <h4>Technicians</h4>
                        <p class="fs-3"><?php echo $total_techs; ?></p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>


<!-- Footer -->
<footer class="text-center">
  <div class="container">
    <div class="row text-start">
      <div class="col-md-6">
        <h5>About Us</h5>
        <p>Cleanify is dedicated to providing top-notch cleaning services with trained professionals at affordable rates.</p>
      </div>
      <div class="col-md-6">
        <h5>Contact Us</h5>
        <p><i class="fas fa-phone-alt me-2"></i>+91 0000000000</p>
        <p><i class="fas fa-envelope me-2"></i>support@cleanify.com</p>
        <p><i class="fas fa-map-marker-alt me-2"></i>000, Gajanan Nagar, Wardha, India</p>
      </div>
    </div>
    <hr>
    <p><i class="fas fa-map-marker-alt me-2"></i>&copy; 2025 Cleanify. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
