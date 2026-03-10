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
    $motor_id   = trim($_POST['motor_id']);
    $motor_name = trim($_POST['motor_name']);
    $power      = trim($_POST['power_rating']);
    $tank_id    = trim($_POST['tank_id']); 

    if (empty($motor_id) || empty($motor_name) || empty($power) || empty($tank_id)) {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO motor_pumps (MOTOR_ID, MOTOR_NAME, POWER_RATING, TANK_ID) VALUES (?, ?, ?, ?)");
    
    if ($stmt === false) {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("ssss", $motor_id, $motor_name, $power, $tank_id);

    ob_clean(); 
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save: ' . $stmt->error]);
    }

    $stmt->close();
}
$conn->close();
?>