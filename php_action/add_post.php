<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch User Info
$sql_user = "SELECT user_name, profile_avatar FROM users WHERE user_id = ?";
$stmt = $connect->prepare($sql_user);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_res = $stmt->get_result()->fetch_assoc();
$user_name = $user_res['user_name'];
$user_avatar = $user_res['profile_avatar'];
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content']);
    $post_image = null;

    // Handle Image Upload
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] == 0) {
        $upload_dir = '../photos/posts/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $ext = pathinfo($_FILES['post_image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('post_') . '.' . $ext;
        $target = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['post_image']['tmp_name'], $target)) {
            $post_image = 'photos/posts/' . $filename; // Store relative path
        }
    }

    if (!empty($content) || $post_image) {
        $sql = "INSERT INTO posts (user_id, user_name, user_avatar, post_content, post_image, post_time) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("issss", $user_id, $user_name, $user_avatar, $content, $post_image);
        
        if ($stmt->execute()) {
            header('Location: ../sns.php');
        } else {
            echo "Error: " . $connect->error;
        }
        $stmt->close();
    } else {
        header('Location: ../sns.php?error=empty');
    }
}
$connect->close();
?>