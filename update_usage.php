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
    
    $id = (int)$_POST['id'];
    $new_amount = (int)$_POST['new_amount'];

    $stmt = $conn->prepare("UPDATE water_usage SET WATER_USED_LITERS = ? WHERE USAGE_ID = ?");
    
    if ($stmt === false) {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("ii", $new_amount, $id);

    
    ob_clean();
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
    }

    $stmt->close();
}
$conn->close();
?>