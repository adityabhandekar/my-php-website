<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Delivery Details - Cleanify</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .back-arrow {
      display: inline-block;
      margin: 20px 0;
    }
    .form-box {
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 25px;
      background-color: #fff;
    }
  </style>
</head>
<body>

<div class="container my-5">
    <!-- Back Arrow -->
    <a href="order_service.php?id=1" class="back-arrow text-dark">
        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
    </a>

    <div class="form-box shadow-sm">
        <h4 class="mb-4 text-center">Change Delivery Details</h4>

        <form action="save_delivery.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Full Name*</label>
                <input type="text" name="full_name" class="form-control" value="<?php echo $_SESSION['user_name'] ?? ''; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email Address*</label>
                <input type="email" name="email" class="form-control" value="<?php echo $_SESSION['email'] ?? ''; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone Number*</label>
                <input type="tel" name="phone" class="form-control" pattern="[0-9]{10}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Address*</label>
                <textarea name="address" class="form-control" rows="3" required><?php echo $_SESSION['address'] ?? ''; ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">City*</label>
                <input type="text" name="city" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">State*</label>
                <input type="text" name="state" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Pin Code*</label>
                <input type="text" name="pincode" class="form-control" pattern="[0-9]{6}" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Save Changes</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
