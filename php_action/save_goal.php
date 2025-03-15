<?php

include 'db_connect.php';
session_start();


// Check for connection errors
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $calories = $_POST['calories'];
    $protein = $_POST['protein'];
    $carbs = $_POST['carbs'];

    // Check if the logged-in user already has a goal set
    $check_sql = "SELECT id FROM daily_goals WHERE user_id = ? LIMIT 1";
    $stmt_check = $connect->prepare($check_sql);
    $stmt_check->bind_param("i", $user_id);
    $stmt_check->execute();
    $check_result = $stmt_check->get_result();

    if ($check_result->num_rows > 0) {
        // If a goal exists, update it
        $sql = "UPDATE daily_goals SET calories = ?, protein = ?, carbs = ?, date_set = NOW() WHERE user_id = ?";
    } else {
        // If no goal exists, insert a new one
        $sql = "INSERT INTO daily_goals (user_id, calories, protein, carbs, date_set) VALUES (?, ?, ?, ?, NOW())";
    }

    $stmt = $connect->prepare($sql);

    if ($check_result->num_rows > 0) {
        // Update query
        $stmt->bind_param("iiii", $calories, $protein, $carbs, $user_id);
    } else {
        // Insert query
        $stmt->bind_param("iiii", $user_id, $calories, $protein, $carbs);
    }

    // Execute and redirect
    if ($stmt->execute()) {
        header('Location: /goals.php');
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement
    $stmt->close();
}

// Close connection
$connect->close();
?>
