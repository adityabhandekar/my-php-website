<?php
include('dbConnection.php');
session_start();

// Ensure the user is logged in, else redirect to login page
if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Please login first'); window.location.href='admin_login.php';</script>";
    exit;
}

// Handle form submission for adding technician
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_technician'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $aadhaar = $_POST['aadhaar'];
    $skills = $_POST['skills'];

    // Prepare and bind statement with error handling
    $stmt = $conn->prepare("INSERT INTO technicians_tb (technician_name, email, phone, aadhaar, skills) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Error in preparing statement: " . $conn->error);
    }

    $stmt->bind_param("sssss", $name, $email, $phone, $aadhaar, $skills);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Technician added successfully'); window.location.href='technicians.php';</script>";
}

// Handle form submission for editing technician
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_technician'])) {
    $edit_id = $_POST['edit_id'];
    $name = $_POST['edit_name'];
    $email = $_POST['edit_email'];
    $phone = $_POST['edit_phone'];
    $aadhaar = $_POST['edit_aadhaar'];
    $skills = $_POST['edit_skills'];

    // Prepare and bind statement with error handling for update
    $stmt = $conn->prepare("UPDATE technicians_tb SET technician_name = ?, email = ?, phone = ?, aadhaar = ?, skills = ? WHERE id = ?");
    if (!$stmt) {
        die("Error in preparing statement: " . $conn->error);
    }

    $stmt->bind_param("sssssi", $name, $email, $phone, $aadhaar, $skills, $edit_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Technician updated successfully'); window.location.href='technicians.php';</script>";
}
// Handle technician deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    // Prepare and bind statement for deletion
    $stmt = $conn->prepare("DELETE FROM technicians_tb WHERE id = ?");
    if (!$stmt) {
        die("Error in preparing statement: " . $conn->error);
    }

    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Technician deleted successfully'); window.location.href='technicians.php';</script>";
}

// Fetch technicians
$result = $conn->query("SELECT * FROM technicians_tb");
$total_technicians = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technicians</title>
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
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
                <li class="nav-item"><a class="nav-link fw-bold" href="technicians.php">Technicians</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_profile.php">Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_services.php">Manage Services</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_feedbacks.php">Manage Feedback</a></li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="admin_profile.php?logout=true">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Technicians <span class="badge bg-primary"><?= $total_technicians ?></span></h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTechnicianModal">+ Add Technician</button>
    </div>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Aadhaar</th>
                <th>Skills</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['technician_name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['aadhaar']) ?></td>
                <td><?= htmlspecialchars($row['skills']) ?></td>
                <td>
                    <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editTechnicianModal" data-id="<?= $row['id'] ?>" data-name="<?= $row['technician_name'] ?>" data-email="<?= $row['email'] ?>" data-phone="<?= $row['phone'] ?>" data-aadhaar="<?= $row['aadhaar'] ?>" data-skills="<?= $row['skills'] ?>">Edit</a>
                    <a href="technicians.php?delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this technician?')">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal for Adding Technician -->
<div class="modal fade" id="addTechnicianModal" tabindex="-1" aria-labelledby="addTechnicianModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTechnicianModalLabel">Add Technician</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="add_technician" value="1">
                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" name="name" required class="form-control">
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" required class="form-control">
                </div>
                <div class="mb-3">
                    <label>Phone</label>
                    <input type="text" name="phone" required class="form-control">
                </div>
                <div class="mb-3">
                    <label>Aadhaar</label>
                    <input type="text" name="aadhaar" required class="form-control">
                </div>
                <div class="mb-3">
                    <label>Skills</label>
                    <input type="text" name="skills" required class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" type="submit">Add Technician</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal for Editing Technician -->
<div class="modal fade" id="editTechnicianModal" tabindex="-1" aria-labelledby="editTechnicianModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTechnicianModalLabel">Edit Technician</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="edit_technician" value="1">
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" name="edit_name" id="edit_name" required class="form-control">
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="edit_email" id="edit_email" required class="form-control">
                </div>
                <div class="mb-3">
                    <label>Phone</label>
                    <input type="text" name="edit_phone" id="edit_phone" required class="form-control">
                </div>
                <div class="mb-3">
                    <label>Aadhaar</label>
                    <input type="text" name="edit_aadhaar" id="edit_aadhaar" required class="form-control">
                </div>
                <div class="mb-3">
                    <label>Skills</label>
                    <input type="text" name="edit_skills" id="edit_skills" required class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" type="submit">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Populate the Edit Modal with technician data
    const editButtons = document.querySelectorAll('[data-bs-target="#editTechnicianModal"]');
    editButtons.forEach(button => {
        button.addEventListener('click', () => {
            const technicianData = button.dataset;
            document.getElementById('edit_id').value = technicianData.id;
            document.getElementById('edit_name').value = technicianData.name;
            document.getElementById('edit_email').value = technicianData.email;
            document.getElementById('edit_phone').value = technicianData.phone;
            document.getElementById('edit_aadhaar').value = technicianData.aadhaar;
            document.getElementById('edit_skills').value = technicianData.skills;
        });
    });
</script>
</body>
</html>
