<?php
include('dbConnection.php');
session_start();

// Ensure the user is logged in, else redirect to login page
if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Please login first'); window.location.href='admin_login.php';</script>";
    exit;
}


if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'] === 'approve' ? 1 : 0;

    $stmt = $conn->prepare("UPDATE feedback SET status=? WHERE id=?");
    $stmt->bind_param("ii", $action, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_feedbacks.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Feedbacks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
            body {
            padding-top: 80px; /* Adjust this value based on the height of your navbar */
        }
    </style>
</head>
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
<body>
<div class="container mt-5">
    <h2 class="mb-4">Customer Feedbacks</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Rating</th>
                <th>Feedback</th>
                <th>Status</th>
                <th>Submitted At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $result = $conn->query("SELECT * FROM feedback ORDER BY created_at DESC");
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $statusText = $row['status'] ? 'Approved' : 'Pending';
                $btnText = $row['status'] ? 'Reject' : 'Approve';
                $action = $row['status'] ? 'reject' : 'approve';
                echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['rating']}</td>
                    <td>{$row['feedback_text']}</td>
                    <td>{$statusText}</td>
                    <td>{$row['created_at']}</td>
                    <td>
                        <a href='admin_feedbacks.php?action={$action}&id={$row['id']}' class='btn btn-sm ".($action == 'approve' ? "btn-success" : "btn-danger")."'>$btnText</a>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='7' class='text-center'>No feedback available.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
