<?php
require_once 'db_connect.php';
session_start();

header('Content-Type: application/json');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'messages' => 'User not authenticated',
        'updatedData' => []
    ]);
    exit;
}

$valid = [
    'success' => false,
    'messages' => [],
    'updatedData' => []
];

$user_id = (int)$_SESSION['user_id'];

// Fetch current profile data (including user_name for the posts table update)
$sql_current = "SELECT user_name, profile_avatar FROM users WHERE user_id = ?";
$stmt_current = $connect->prepare($sql_current);
if (!$stmt_current) {
    error_log("Prepare failed: " . $connect->error);
    $valid['messages'] = "Database prepare failed";
    echo json_encode($valid);
    exit;
}
$stmt_current->bind_param("i", $user_id);
$stmt_current->execute();
$result_current = $stmt_current->get_result();
$current_data = $result_current->fetch_assoc();
$stmt_current->close();

if (!$current_data) {
    $valid['messages'] = "User not found";
    echo json_encode($valid);
    exit;
}

$user_name = $current_data['user_name'];
$avatar = $current_data['profile_avatar'] ?: 'photos/user.png';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['profile_avatar'])) {
        if ($_FILES['profile_avatar']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = realpath('../photos/') . '/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            if (!is_writable($upload_dir)) {
                $valid['messages'] = "Upload directory is not writable";
                echo json_encode($valid);
                exit;
            }

            $type = explode('.', $_FILES['profile_avatar']['name']);
            $type = strtolower(end($type));
            $allowed_types = ['gif', 'jpg', 'jpeg', 'png'];
            if (!in_array($type, $allowed_types)) {
                $valid['messages'] = "Invalid file type. Only GIF, JPG, JPEG, PNG are allowed.";
                echo json_encode($valid);
                exit;
            }

            $avatar_name = uniqid(rand()) . '.' . $type;
            $target_file = $upload_dir . $avatar_name;
            $relative_path = 'photos/' . $avatar_name;

            if (move_uploaded_file($_FILES['profile_avatar']['tmp_name'], $target_file)) {
                $avatar = $relative_path;
                if ($current_data['profile_avatar'] && $current_data['profile_avatar'] != 'photos/user.png') {
                    $old_file = realpath('../' . $current_data['profile_avatar']);
                    if (file_exists($old_file)) {
                        unlink($old_file);
                    } else {
                        error_log("Old avatar file not found: $old_file");
                    }
                }
            } else {
                error_log("File Upload Error: Failed to move uploaded file to $target_file");
                $valid['messages'] = "Error uploading avatar.";
                echo json_encode($valid);
                exit;
            }

            // Update the users table
            $sql = "UPDATE users SET profile_avatar = ? WHERE user_id = ?";
            $stmt = $connect->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("si", $avatar, $user_id);
                if ($stmt->execute()) {
                    error_log("Avatar updated successfully in users table. New avatar: $avatar");

                    // Update the posts table
                    $sql_posts = "UPDATE posts SET user_avatar = ? WHERE user_name = ?";
                    $stmt_posts = $connect->prepare($sql_posts);
                    if ($stmt_posts) {
                        $stmt_posts->bind_param("ss", $avatar, $user_name);
                        if ($stmt_posts->execute()) {
                            error_log("Posts table updated successfully. Affected rows: " . $stmt_posts->affected_rows);
                            $valid['success'] = true;
                            $valid['messages'] = "Successfully Updated";
                            $valid['updatedData'] = ['profile_avatar' => $avatar];
                        } else {
                            error_log("Posts table update failed: " . $stmt_posts->error);
                            $valid['messages'] = "Avatar updated in users table, but failed to update posts: " . $stmt_posts->error;
                        }
                        $stmt_posts->close();
                    } else {
                        error_log("Prepare statement failed for posts table: " . $connect->error);
                        $valid['messages'] = "Avatar updated in users table, but failed to prepare posts update: " . $connect->error;
                    }
                } else {
                    error_log("Database execution failed: " . $connect->error);
                    $valid['messages'] = "Error while updating avatar in users table: " . $connect->error;
                }
                $stmt->close();
            } else {
                error_log("Prepare statement failed: " . $connect->error);
                $valid['messages'] = "Prepare statement failed: " . $connect->error;
            }
        } else {
            $upload_errors = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds the upload_max_filesize directive in php.ini.',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds the MAX_FILE_SIZE directive in the HTML form.',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.'
            ];
            $error_message = $upload_errors[$_FILES['profile_avatar']['error']] ?? 'Unknown upload error.';
            error_log("Upload Error Code: " . $_FILES['profile_avatar']['error'] . " - " . $error_message);
            $valid['messages'] = $error_message;
            echo json_encode($valid);
            exit;
        }
    } else {
        $valid['messages'] = "No avatar file uploaded";
    }
} else {
    $valid['messages'] = "Invalid request method";
}

$connect->close();
echo json_encode($valid);
?>