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
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access. Please log in.']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $motor_id         = (int)$_POST['motor_id']; 
    $maintenance_date = trim($_POST['maintenance_date']);
    $issue_desc       = trim($_POST['issue_description']);
    $status           = trim($_POST['status']);

    if (empty($motor_id) || empty($maintenance_date) || empty($issue_desc) || empty($status)) {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO maintenance (MOTOR_ID, ISSUE_DESCRIPTION, MAINTENANCE_DATE, STATUS) VALUES (?, ?, ?, ?)");
    
    if ($stmt === false) {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("isss", $motor_id, $issue_desc, $maintenance_date, $status);
    ob_clean(); 
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save record: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

$conn->close();
?>