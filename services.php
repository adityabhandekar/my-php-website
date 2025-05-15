<?php
session_start();
include('dbConnection.php');  // Your database connection file

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];  // Get user ID

// Query to fetch all available services
$sql = "SELECT id, title, description, rate, image FROM services_tb";  
$result = $conn->query($sql);  // Execute the query

// Check if query was successful
if (!$result) {
    echo "Error: " . $conn->error;
    exit();
}
try {
    $stmt = $conn->prepare("SELECT username, email FROM userinfo_tb WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_result = $stmt->get_result();
    $user = $user_result->fetch_assoc();
    $stmt->close();
} catch (Exception $e) {
    $user = ['username' => 'User', 'email' => ''];
}

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
    footer{
      padding: 50px 0 20px;
    }
  </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand logo" href="#">Clinefy</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="loginindex.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="services.php">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="loginindex.php/#feedback">Feedback</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_orders.php">My Orders</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="profile.php" class="btn btn-outline-light me-2">Profile</a>
                </div>
            </div>
        </div>
    </nav>

<!-- Services Section -->
<section class="container my-5">
  <h2 class="text-center mb-4">Our Cleaning Services</h2>
  <div class="row g-4">
    <?php
    // Check if there are services available
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Check if an image is provided, otherwise fallback to the default image
            $imagePath = !empty($row['image']) ? 'images/' . $row['image'] : 'images/default.jpg';  // Fallback image
            echo '
                <div class="col-md-4">
                  <a href="service_detail.php?id=' . $row['id'] . '" class="text-decoration-none text-dark">
                    <div class="card service-card h-100">
                      <img src="' . htmlspecialchars($imagePath) . '" class="card-img-top service-img" alt="' . htmlspecialchars($row["title"]) . '">
                      <div class="card-body d-flex flex-column">
                        <h5 class="card-title">' . htmlspecialchars($row["title"]) . '</h5>
                        <p class="card-text flex-grow-1">' . htmlspecialchars($row["description"]) . '</p>
                        <p class="fw-bold text-primary">Rate: ₹' . htmlspecialchars($row["rate"]) . '</p>
                      </div>
                    </div>
                  </a>
                </div>';
        }
    } else {
        echo "<p class='text-center'>No services available at the moment.</p>";
    }
    ?>
  </div>
</section>

<!-- Footer -->
<footer class="text-center bg-dark text-light">
  <div class="container">
    <div class="row text-start">
      <div class="col-md-6">
        <h5>About Us</h5>
        <p>Cleanify is dedicated to providing top-notch cleaning services with trained professionals at affordable rates.</p>
      </div>
      <div class="col-md-6">
          <h5>Quick Links</h5>
          <ul class="list-unstyled">
              <li><a href="services.php" class="text-white">Services</a></li>
               <li><a href="my_orders.php" class="text-white">My Orders</a></li>
                <li><a href="contact.html" class="text-white">Contact Us</a></li>
          </ul>
      </div>
      <div class="col-md-6">
          <h5>Contact Us</h5>
        <p><i class="fas fa-phone-alt me-2"></i>+91 0000000000</p>
        <p><i class="fas fa-envelope me-2"></i>support@cleanify.com</p>
        <p><i class="fas fa-map-marker-alt me-2"></i>000, Gajanan Nagar, Wardha, India</p>
      </div>
    </div>
    <hr>
    <p class="text-center p-3">&copy; 2025 Cleanify. All rights reserved.</p>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
