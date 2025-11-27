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
$content = trim($_POST['content']);

if (empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Content cannot be empty']);
    exit();
}

// Update DB (Ensure user owns post)
$sql = "UPDATE posts SET post_content = ? WHERE post_id = ? AND user_id = ?";
$stmt = $connect->prepare($sql);
$stmt->bind_param("sii", $content, $post_id, $user_id);

if ($stmt->execute() && $stmt->affected_rows >= 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed or no changes made']);
}

$stmt->close();
$connect->close();
?>