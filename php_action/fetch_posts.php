<?php
include 'db_connect.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'Unauthorized access!']));
}

$user_id = $_SESSION['user_id'];

// Fetch posts
$sql = "SELECT * FROM posts ORDER BY post_time DESC";
$result = $connect->query($sql);

$posts = [];
while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}

header('Content-Type: application/json');
echo json_encode($posts);
$connect->close();
?>