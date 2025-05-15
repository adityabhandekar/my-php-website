<?php
$db_host = "sql208.infinityfree.com";
$db_user = "if0_38984079";
$db_password = "6yVzCOxLsl8mCih";
$db_name = "if0_38984079_homecleaning";

// Create Connection
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check Connection
if($conn->connect_error) {
 die("connection failed");
}
?>