<?php
include('dbConnection.php');

$serviceId = $_GET['id'] ?? null;
if (!$serviceId) {
  die("Invalid service ID.");
}

$sql = "SELECT * FROM services_tb WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $serviceId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  die("Service not found.");
}
$service = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Services - Cleanify</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .service-card {
      transition: all 0.3s ease;
      border-radius: 15px;
      overflow: hidden;
    }
    .service-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    .service-img {
      height: 220px;
      object-fit: cover;
    }
    footer {
      background-color: #f8f9fa;
      padding: 40px 0 20px;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">Cleanify</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
        <li class="nav-item"><a class="nav-link " href="services.php">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.html">Contact Us</a></li>
        <li class="nav-item"><a class="nav-link" href="my_orders.php">Track Order</a></li>
        <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container my-5">
  <div class="card">
    <img src="<?= htmlspecialchars($service['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($service['title']) ?>" style="height:300px;object-fit:cover;">
    <div class="card-body">
      <h3><?= htmlspecialchars($service['title']) ?></h3>
      <p><?= htmlspecialchars($service['description']) ?></p>
      <p><strong>Rate:</strong> ₹<?= htmlspecialchars($service['rate']) ?></p>
      <a href="order_service.php?id=<?= $service['id'] ?>" class="btn btn-primary">Order Now</a>
      <a href="services.php?id=<?= $service['id'] ?>" class="btn btn-dark">Back</a>
    </div>
  </div>
</div>
</body>
</html>
