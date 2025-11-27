<?php
require_once 'db_connect.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 1. Get data from form
    $calories = $_POST['calories'] ?? 2000;
    $protein = $_POST['protein'] ?? 150;
    $carbs = $_POST['carbs'] ?? 250;

    // 2. Check if a goal row already exists for this user
    $check_sql = "SELECT goal_id FROM goals WHERE user_id = ?";
    $stmt = $connect->prepare($check_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();

    // 3. Update or Insert
    if ($exists) {
        // UPDATE existing record
        $sql = "UPDATE goals SET daily_calories = ?, protein_goal = ?, carbs_goal = ? WHERE user_id = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("iiii", $calories, $protein, $carbs, $user_id);
    } else {
        // INSERT new record
        $sql = "INSERT INTO goals (user_id, daily_calories, protein_goal, carbs_goal) VALUES (?, ?, ?, ?)";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("iiii", $user_id, $calories, $protein, $carbs);
    }

    if ($stmt->execute()) {
        // Success: Redirect back to goals page
        header('Location: ../goals.php?success=goals_updated');
    } else {
        // Error handling
        echo "Error updating goals: " . $connect->error;
    }

    $stmt->close();
} else {
    // Redirect if accessed directly without POST
    header('Location: ../goals.php');
}

$connect->close();
?>