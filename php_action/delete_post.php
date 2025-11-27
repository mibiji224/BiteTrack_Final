<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'];

// Check if post belongs to user
$check_sql = "SELECT post_image FROM posts WHERE post_id = ? AND user_id = ?";
$stmt = $connect->prepare($check_sql);
$stmt->bind_param("ii", $post_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Post not found or permission denied']);
    exit();
}

$post = $result->fetch_assoc();

// Delete post from DB
$del_sql = "DELETE FROM posts WHERE post_id = ?";
$del_stmt = $connect->prepare($del_sql);
$del_stmt->bind_param("i", $post_id);

if ($del_stmt->execute()) {
    // Optional: Delete image file if it exists
    if (!empty($post['post_image']) && file_exists('../' . $post['post_image'])) {
        unlink('../' . $post['post_image']);
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$stmt->close();
$del_stmt->close();
$connect->close();
?>