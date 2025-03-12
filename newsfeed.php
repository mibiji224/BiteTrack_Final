<?php
header("Content-Type: application/json");
include 'php_action/db_connect.php'; // Adjust path if needed

$query = "SELECT user_name, user_avatar, post_content, post_time FROM posts ORDER BY post_time DESC";
$result = mysqli_query($connect, $query);

$posts = [];

while ($row = mysqli_fetch_assoc($result)) {
    $posts[] = $row;
}

echo json_encode($posts);
