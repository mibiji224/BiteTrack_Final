<?php
require_once 'db_connect.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access!']);
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. FETCH USER DETAILS
// Fix: Changed 'WHERE id' to 'WHERE user_id'
$sql_user = "SELECT user_name, profile_avatar FROM users WHERE user_id = ?";
$stmt = $connect->prepare($sql_user);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_user = $stmt->get_result();

if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
    $user_name = $user['user_name'];
    // Use default avatar if user doesn't have one
    $user_avatar = !empty($user['profile_avatar']) ? $user['profile_avatar'] : 'photos/user.png';
} else {
    echo json_encode(['success' => false, 'error' => 'User not found']);
    exit();
}
$stmt->close();

// 2. INSERT POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    $content = trim($_POST['content']);

    if (!empty($content)) {
        // Fix: Added 'user_id' and 'user_avatar' to the insert statement
        $sql = "INSERT INTO posts (user_id, user_name, user_avatar, post_content, post_time) 
                VALUES (?, ?, ?, ?, NOW())";
        
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("isss", $user_id, $user_name, $user_avatar, $content);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $connect->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Content cannot be empty']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}

$connect->close();
?>