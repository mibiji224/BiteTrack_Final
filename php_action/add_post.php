<?php
include 'db_connect.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access!']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the logged-in user's username and avatar
$sql_user = "SELECT user_name, profile_avatar FROM users WHERE id = '$user_id'";
$result_user = $connect->query($sql_user);
$user = $result_user->fetch_assoc();
$user_name = $user['user_name'] ?? 'Unknown';
$user_avatar = $user['profile_avatar'] ?? 'photos/default.png';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    $content = $connect->real_escape_string(trim($_POST['content']));

    if (!empty($content)) {
        $sql = "INSERT INTO posts (user_name, post_content, post_time) VALUES ('$user_name', '$content', NOW())";
        if ($connect->query($sql) === TRUE) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $connect->error]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Content cannot be empty']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}

$connect->close();
?>