<?php

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    die("Unauthorized access");
}
require "connection.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $table = $_POST['table'] ?? '';
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $allowed_tables = [
        'buildings'   => 'BUILDING_ID',
        'water_tanks' => 'TANK_ID',
        'motor_pumps' => 'MOTOR_ID',
        'maintenance' => 'MAINTENANCE_ID',
        'water_usage' => 'USAGE_ID'
    ];
    if (array_key_exists($table, $allowed_tables)) {
        $column_name = $allowed_tables[$table];
        $sql = "DELETE FROM $table WHERE $column_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "success";
        } else {
            if ($conn->errno == 1451) {
                echo "Cannot delete: This record is linked to other data (e.g., a building with active tanks).";
            } else {
                echo "Database Error: " . $conn->error;
            }
        }
        $stmt->close();
    } else {
        echo "Invalid table specified.";
    }
}
$conn->close();
?>