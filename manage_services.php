<?php
session_start();
include('dbConnection.php');

// ADD or UPDATE service
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_service'])) {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $rate = $_POST['rate'];
    $id = $_POST['service_id'];

    $image_name = '';
    if (!empty($_FILES['image']['name'])) {
        $image_name = basename($_FILES['image']['name']);
        $target_path = '../images/' . $image_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $target_path);
    }

    if ($id == '') {
        // Add new
        $sql = "INSERT INTO services_tb (title, description, rate, image)
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssis", $title, $desc, $rate, $image_name);
    } else {
        // Update existing
        if ($image_name) {
            $sql = "UPDATE services_tb SET title=?, description=?, rate=?, image=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssisi", $title, $desc, $rate, $image_name, $id);
        } else {
            $sql = "UPDATE services_tb SET title=?, description=?, rate=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssii", $title, $desc, $rate, $id);
        }
    }

    if ($stmt->execute()) {
        echo "<script>alert('Service saved successfully'); window.location='manage_services.php';</script>";
    } else {
        echo "<script>alert('Error saving service');</script>";
    }
}

// DELETE service
if (isset($_POST['delete_service'])) {
    $delete_id = $_POST['delete_id'];

    // Delete image
    $get_img = $conn->query("SELECT image FROM services_tb WHERE id = $delete_id");
    if ($get_img && $img_row = $get_img->fetch_assoc()) {
        $img_path = '../images/' . $img_row['image'];
        if (file_exists($img_path)) {
            unlink($img_path);
        }
    }

    $delete_sql = "DELETE FROM services_tb WHERE id = $delete_id";
    if ($conn->query($delete_sql)) {
        echo "<script>alert('Service deleted successfully'); window.location='manage_services.php';</script>";
    } else {
        echo "<script>alert('Error deleting service');</script>";
    }
}

// Fetch all services
$services = $conn->query("SELECT * FROM services_tb");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 80px; /* Adjust this value based on the height of your navbar */
        }
    </style>

    </style>
</head>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Cleanify Admin</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="technicians.php">Technicians</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_profile.php">Profile</a></li>
                <li class="nav-item"><a class="nav-link fw-bold" href="manage_services.php">Manage Services</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_feedbacks.php">Manage Feedback</a></li>
            </ul>
        </div>
    </div>
</nav>

<body class="bg-light">

<div class="container my-4">
    <h2 class="text-center mb-4">Manage Services</h2>

    <!-- Add/Edit Form -->
    <div class="card mb-4">
        <div class="card-header">Add / Edit Service</div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="service_id" id="service_id">
                <div class="mb-3">
                    <label>Title</label>
                    <input type="text" name="title" id="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" id="description" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label>Rate</label>
                    <input type="number" name="rate" id="rate" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Image</label>
                    <input type="file" name="image" class="form-control">
                </div>
                <button type="submit" name="save_service" class="btn btn-success">Save</button>
            </form>
        </div>
    </div>

    <!-- Service List -->
    <div class="row">
        <?php while ($row = $services->fetch_assoc()) { ?>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <img src="../images/<?php echo $row['image']; ?>" class="card-img-top" style="height:200px; object-fit:cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['title']; ?></h5>
                        <p class="card-text"><?php echo $row['description']; ?></p>
                        <p><strong>Rate:</strong> ₹<?php echo $row['rate']; ?></p>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <button class="btn btn-primary btn-sm" onclick="editService('<?php echo $row['id']; ?>','<?php echo $row['title']; ?>','<?php echo $row['description']; ?>','<?php echo $row['rate']; ?>')">Edit</button>

                        <form method="POST" onsubmit="return confirm('Delete this service?');">
                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete_service" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script>
function editService(id, title, description, rate) {
    document.getElementById('service_id').value = id;
    document.getElementById('title').value = title;
    document.getElementById('description').value = description;
    document.getElementById('rate').value = rate;
    window.scrollTo(0, 0);
}
</script>

</body>
</html>
