<?php
include 'db_connect.php'; // Ensure you have your database connection

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$userid = $_SESSION['user_id'];

$sql = "SELECT first_name, age, height, weight FROM users WHERE user_id = ?";
$stmt = $connect->prepare($sql);
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    $user = [
        'name' => 'N/A',
        'age' => 'N/A',
        'height' => 'N/A',
        'weight' => 'N/A'
    ];
}

$stmt->close();
$connect->close();
?>
