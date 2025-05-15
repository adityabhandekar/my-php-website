<?php
include('dbConnection.php');
session_start();

// Ensure the user is logged in, else redirect to login page
if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Please login first'); window.location.href='admin_login.php';</script>";
    exit;
}

// Fetch the logged-in admin's details
$admin_id = $_SESSION['admin_id'];

$query = "SELECT * FROM adminlogin_tb WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

// Update admin information
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // If password is provided, hash it
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    } else {
        $hashed_password = $admin['password']; // keep current password if empty
    }

    // Update query
    $update_query = "UPDATE adminlogin_tb SET username = ?, email = ?, password = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sssi", $username, $email, $hashed_password, $admin_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Profile updated successfully'); window.location.href='admin_profile.php';</script>";
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    echo "<script>window.location.href='admin_login.php';</script>"; // Redirect to login page
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Profile - HomeCleaning</title>
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
                <li class="nav-item"><a class="nav-link fw-bold" href="admin_profile.php">Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_services.php">Manage Services</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_feedbacks.php">Manage Feedback</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h2 class="text-center my-4">Admin Profile</h2>

    <!-- Display admin info -->
    <div class="row">
        <div class="col-md-6">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($admin['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($admin['email']); ?></p>
        </div>
    </div>

    <!-- Profile Update Form -->
    <h3>Update Profile</h3>
    <form action="admin_profile.php" method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">New Password (Leave empty if you don't want to change)</label>
            <input type="password" class="form-control" name="password">
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
        <!-- Logout Button -->
    <a href="admin_profile.php?logout=true" class="btn btn-danger ms-2">Logout</a>
    </form>

    <!-- Footer -->
    <footer class="text-center mt-4">
        <p>&copy; 2025 HomeCleaning</p>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
