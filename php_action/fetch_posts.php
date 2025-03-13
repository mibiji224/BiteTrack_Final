<?php
include 'db_connect.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'Unauthorized access!']));
}

$user_id = $_SESSION['user_id'];

// Fetch posts and join with users table to get the latest profile_avatar
$sql = "SELECT p.*, u.profile_avatar AS user_avatar 
        FROM posts p 
        JOIN users u ON p.user_name = u.user_name 
        ORDER BY p.post_time DESC";
$result = $connect->query($sql);

$posts = [];
while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}

header('Content-Type: application/json');
echo json_encode($posts);
$connect->close();
?>