<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Prevent any stray errors from displaying as HTML
error_reporting(0); 
ob_start();
require "connection.php";

// Set header for AJAX JSON response
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Determine which form is being submitted via a hidden 'type' field
    $formType = $_POST['form_type'] ?? '';

    switch ($formType) {
        case 'usage':
            // Logic for Water Usage Form
            $building_id = (int)$_POST['building_id'];
            $usage_date  = $_POST['usage_date'];
            $liters      = (int)$_POST['water_used'];

            $stmt = $conn->prepare("INSERT INTO water_usage (BUILDING_ID, USAGE_DATE, WATER_USED_LITERS) VALUES (?, ?, ?)");
            $stmt->bind_param("isi", $building_id, $usage_date, $liters);
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid form type']);
            exit;
    }

    // Execute the prepared statement
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'id' => $conn->insert_id]);
    } else {
       // echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }

    $stmt->close();
}
$conn->close();
?>