<?php
include 'db_connect.php'; // Ensure you have your database connection



$user_id = $_SESSION['user_id'];

// Fetch user data
$sql_user = "SELECT first_name, last_name, age, height, weight, profile_avatar FROM users WHERE user_id = ?";
$stmt_user = $connect->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
} else {
    $user = [
        'first_name' => 'Unknown',
        'last_name' => '',
        'age' => 0,
        'height' => 0.0,
        'weight' => 0.0,
        'profile_avatar' => 'photos/user.png'
    ];
}
$stmt_user->close();
$connect->close();
?>
