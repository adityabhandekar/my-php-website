<?php
session_start();
include('dbConnection.php');

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Get user's orders from database
try {
    $stmt = $conn->prepare("
        SELECT o.*, s.title as service_name, s.description as service_desc 
        FROM orders_tb o
        LEFT JOIN services_tb s ON o.service_id = s.id
        WHERE o.user_id = ?
        ORDER BY o.order_date DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    $orders = [];
}

// 3. Get user details for the header
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Cleanify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .order-card {
            border-left: 4px solid #0d6efd;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        .order-card:hover {
            box-shadow: 0 5px 15px rgba(255, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 20px;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .order-details {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        .order-details.show {
            max-height: 500px;
        }
        .empty-state {
            text-align: center;
            padding: 40px 0;
        }
        .empty-state i {
            font-size: 5rem;
            color: #6c757d;
            opacity: 0.5;
            margin-bottom: 20px;
        }
        footer {
            padding: 50px 0 20px;
        }
    </style>
</head>
<body class="bg-light">
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
    
    <!-- Main Content -->
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col">
                <h1 class="display-6">My Orders</h1>
                <p class="text-muted">View and manage your service orders</p>
            </div>
            <div class="col-auto">
                <a href="services.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Book New Service
                </a>
            </div>
        </div>

        <?php if (empty($orders)): ?>
            <!-- Empty State -->
            <div class="card empty-state">
                <div class="card-body">
                    <i class="bi bi-box-seam"></i>
                    <h3>No Orders Yet</h3>
                    <p class="text-muted">You haven't placed any orders yet. Book a service to get started!</p>
                    <a href="services.php" class="btn btn-primary mt-3">
                        Browse Services
                    </a>
                </div>
            </div>
        <?php else: ?>
            
            <!-- Orders List -->
            <div class="row">
                <div class="col-12">
                    <?php foreach ($orders as $order): ?>
                        <div class="card order-card mb-3">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <h5 class="card-title mb-1"><?= htmlspecialchars($order['service_name'] ?? $order['service_title']) ?></h5>
                                        <small class="text-muted">Order #<?= $order['id'] ?></small>
                                    </div>
                                    <div class="col-md-2">
                                        <p class="mb-1"><strong>Date:</strong></p>
                                        <p class="mb-0"><?= date('M j, Y', strtotime($order['order_date'])) ?></p>
                                    </div>
                                    <div class="col-md-2">
                                        <p class="mb-1"><strong>Amount:</strong></p>
                                        <p class="mb-0">₹<?= number_format($order['service_rate'], 2) ?></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Status:</strong></p>
                                        <span class="status-badge status-<?= strtolower($order['order_status']) ?>">
                                            <?= $order['order_status'] ?>
                                        </span>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <button class="btn btn-sm btn-outline-primary toggle-details" 
                                                data-target="order-<?= $order['id'] ?>">
                                            <i class="bi bi-chevron-down"></i> Details
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Order Details (Hidden by default) -->
                                <div class="order-details mt-3" id="order-<?= $order['id'] ?>">
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Service Information</h6>
                                            <p><?= htmlspecialchars($order['service_desc'] ?? $order['service_description']) ?></p>
                                            <p><strong>Rate:</strong> ₹<?= number_format($order['service_rate'], 2) ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Delivery Information</h6>
                                            <p>
                                                <strong>To:</strong> <?= htmlspecialchars($order['full_name']) ?><br>
                                                <?= htmlspecialchars($order['address']) ?><br>
                                                <?= htmlspecialchars($order['pincode']) ?>
                                            </p>
                                            <p>
                                                <strong>Contact:</strong> <?= htmlspecialchars($order['phone']) ?><br>
                                                <?= htmlspecialchars($order['email']) ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <h6>Payment Information</h6>
                                            <p>
                                                <strong>Transaction ID:</strong> <?= htmlspecialchars($order['transaction_id']) ?><br>
                                                <strong>Payment Date:</strong> <?= date('M j, Y H:i', strtotime($order['order_date'])) ?>
                                            </p>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <?php if ($order['order_status'] == 'Pending'): ?>
                                                <button class="btn btn-sm btn-outline-danger cancel-order" 
                                                        data-order-id="<?= $order['id'] ?>">
                                                    <i class="bi bi-x-circle"></i> Cancel Order
                                                </button>
                                            <?php endif; ?>
                                            <a href="order_confirmation.php?order_id=<?= $order['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-printer"></i> Print Receipt
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

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

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Toggle order details
        document.querySelectorAll('.toggle-details').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const detailsDiv = document.getElementById(targetId);
                detailsDiv.classList.toggle('show');
                
                // Toggle icon
                const icon = this.querySelector('i');
                if (detailsDiv.classList.contains('show')) {
                    icon.classList.remove('bi-chevron-down');
                    icon.classList.add('bi-chevron-up');
                    this.textContent = ' Hide Details';
                    this.prepend(icon);
                } else {
                    icon.classList.remove('bi-chevron-up');
                    icon.classList.add('bi-chevron-down');
                    this.textContent = ' Details';
                    this.prepend(icon);
                }
            });
        });

        // Cancel order functionality
        document.querySelectorAll('.cancel-order').forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-order-id');
                
                Swal.fire({
                    title: 'Cancel Order?',
                    text: "Are you sure you want to cancel this order?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, cancel it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // AJAX request to cancel order
                        fetch('cancel_order.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `order_id=${orderId}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire(
                                    'Cancelled!',
                                    'Your order has been cancelled.',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Error',
                                    data.message || 'Failed to cancel order',
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            Swal.fire(
                                'Error',
                                'An error occurred while processing your request',
                                'error'
                            );
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
