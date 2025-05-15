<?php
session_start();
include('dbConnection.php');

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

// Validate POST data
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
$user_id = $_SESSION['user_id'];

try {
    // Verify the order belongs to the user
    $stmt = $conn->prepare("SELECT id FROM orders_tb WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Order not found or not authorized']);
        exit();
    }
    $stmt->close();

    // Update order status
    $stmt = $conn->prepare("UPDATE orders_tb SET order_status = 'Cancelled' WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
    
    $stmt->close();
} catch (Exception $e) {
    error_log("Cancel order error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
?>