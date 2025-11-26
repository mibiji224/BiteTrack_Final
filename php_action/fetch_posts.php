<?php
require_once 'db_connect.php';
session_start();

// Check if user is logged in (Optional for public feeds, but good for security)
if (!isset($_SESSION['user_id'])) {
    // You can uncomment this if you want to force login to see posts
    // die(json_encode(['error' => 'Unauthorized access!']));
}

// Fetch posts ordered by newest first
// We select columns explicitly to be safe
$sql = "SELECT post_id, user_name, user_avatar, post_content, post_time 
        FROM posts 
        ORDER BY post_time DESC";

$result = $connect->query($sql);

$posts = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Ensure avatar path is valid
        if (empty($row['user_avatar'])) {
            $row['user_avatar'] = 'photos/user.png';
        }
        $posts[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($posts);

$connect->close();
?>