<?php
require_once 'db_connect.php';
session_start();

// Return JSON response
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'] ?? null;
    $comment = trim($_POST['comment'] ?? '');
    $user_id = $_SESSION['user_id'];

    if ($post_id && !empty($comment)) {
        // Insert Comment
        $sql = "INSERT INTO post_comments (post_id, user_id, comment_text) VALUES (?, ?, ?)";
        $stmt = $connect->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("iis", $post_id, $user_id, $comment);
            if ($stmt->execute()) {
                // Fetch user details to return to the frontend immediately
                $u_sql = "SELECT user_name, profile_avatar FROM users WHERE user_id = ?";
                $u_stmt = $connect->prepare($u_sql);
                $u_stmt->bind_param("i", $user_id);
                $u_stmt->execute();
                $user_data = $u_stmt->get_result()->fetch_assoc();
                
                echo json_encode([
                    'success' => true,
                    'user_name' => $user_data['user_name'],
                    'user_avatar' => $user_data['profile_avatar'] ?? 'photos/user.png',
                    'comment' => htmlspecialchars($comment),
                    'date' => 'Just now'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error']);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Query preparation failed']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Comment cannot be empty']);
    }
}
$connect->close();
?>