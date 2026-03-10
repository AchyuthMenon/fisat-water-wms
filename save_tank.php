<?php

ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(0); 
require "connection.php";
header('Content-Type: application/json');

if (!isset($_SESSION['loggedin'])) {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Grab the custom ID from the user
    $tank_id     = trim($_POST['tank_id']); 
    $tank_name   = trim($_POST['tank_name']);
    $capacity    = (int)$_POST['capacity'];
    $building_id = (int)$_POST['building_id'];

    if (empty($tank_id) || empty($tank_name) || empty($capacity) || empty($building_id)) {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    // Include TANK_ID in the INSERT statement
    $stmt = $conn->prepare("INSERT INTO water_tanks (TANK_ID, TANK_NAME, CAPACITY_LITERS, BUILDING_ID) VALUES (?, ?, ?, ?)");
    
    if ($stmt === false) {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
        exit;
    }

    // 's' for tank_id (in case you use letters like T-01), 's' for name, 'i' for capacity, 'i' for building
    $stmt->bind_param("ssii", $tank_id, $tank_name, $capacity, $building_id);

    ob_clean(); 
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        // If the user types an ID that already exists, this will catch the duplicate error
        echo json_encode(['status' => 'error', 'message' => 'Failed to save: ' . $stmt->error]);
    }

    $stmt->close();
}
$conn->close();
?>