<?php
require_once 'db_connect.php';
session_start();

header('Content-Type: application/json');

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Login required']);
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'] ?? null;

if (!$post_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid post']);
    exit();
}

// 2. Check if the user already liked this post
$checkSql = "SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?";
$stmt = $connect->prepare($checkSql);
$stmt->bind_param("ii", $post_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$exists = $result->num_rows > 0;
$stmt->close();

// 3. Toggle Like (Insert or Delete)
if ($exists) {
    // Already liked -> Unlike it
    $sql = "DELETE FROM post_likes WHERE post_id = ? AND user_id = ?";
} else {
    // Not liked -> Like it
    $sql = "INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)";
}

$stmt = $connect->prepare($sql);
$stmt->bind_param("ii", $post_id, $user_id);

if ($stmt->execute()) {
    // 4. Get the new total like count to update the UI accurately
    $countSql = "SELECT COUNT(*) as count FROM post_likes WHERE post_id = ?";
    $countStmt = $connect->prepare($countSql);
    $countStmt->bind_param("i", $post_id);
    $countStmt->execute();
    $countData = $countStmt->get_result()->fetch_assoc();
    
    echo json_encode(['success' => true, 'new_count' => $countData['count']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$connect->close();
?>