<?php
include 'db_connect.php';
session_start(); // Start the session to get logged-in user info
header("Content-Type: application/json");

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "User not authenticated"]);
    exit;
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Get the input data from the request body
$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    // Sanitize input data to prevent SQL injection
    $name = $connect->real_escape_string($data["name"]);
    $age = (int)$data["age"];
    $height = (float)$data["height"];
    $weight = (float)$data["weight"];

    // Check DB connection
    if ($connect->connect_error) {
        echo json_encode(["success" => false, "error" => "DB connection failed"]);
        exit;
    }

    // Update the user's profile based on their user_id
    $sql = "UPDATE users SET name='$name', age='$age', height='$height', weight='$weight' WHERE id='$user_id'";

    if ($connect->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Profile updated successfully"]);
    } else {
        echo json_encode(["success" => false, "error" => $connect->error]);
    }

    $connect->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid request"]);
}
?>
