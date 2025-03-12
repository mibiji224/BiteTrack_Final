<?php
include '../db_connect.php';
session_start();

header('Content-Type: application/json');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle form data (including file upload)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $connect->real_escape_string(trim($_POST['first_name'] ?? ''));
    $last_name = $connect->real_escape_string(trim($_POST['last_name'] ?? ''));
    $age = (int)($_POST['age'] ?? 0);
    $height = (float)($_POST['height'] ?? 0.0);
    $weight = (float)($_POST['weight'] ?? 0.0);
    $current_avatar = null;

    // Fetch current profile data to keep avatar if not updated
    $sql_current = "SELECT profile_avatar FROM users WHERE id = ?";
    $stmt_current = $connect->prepare($sql_current);
    $stmt_current->bind_param("i", $user_id);
    $stmt_current->execute();
    $result_current = $stmt_current->get_result();
    if ($result_current && $row = $result_current->fetch_assoc()) {
        $current_avatar = $row['profile_avatar'];
    }
    $stmt_current->close();

    $avatar = $current_avatar ?: 'photos/user.png'; // Default full path

    // Handle avatar upload if a new file is provided
    if (isset($_FILES['profile_avatar']) && $_FILES['profile_avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
        error_log("File Upload Details: " . print_r($_FILES['profile_avatar'], true));

        if ($_FILES['profile_avatar']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../photos/'; // Adjust this to your actual upload directory
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            if (!is_writable($upload_dir)) {
                echo json_encode(['success' => false, 'error' => 'Upload directory is not writable']);
                exit;
            }

            $avatar_name = uniqid() . '_' . basename($_FILES['profile_avatar']['name']);
            $target_file = $upload_dir . $avatar_name;
            $full_path = $target_file; // Store the full path

            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            $max_file_size = 2 * 1024 * 1024; // 2MB

            if (!in_array($imageFileType, $allowed_types)) {
                echo json_encode(['success' => false, 'error' => 'Invalid file type. Only JPG, JPEG, PNG, GIF are allowed.']);
                exit;
            }

            if ($_FILES['profile_avatar']['size'] > $max_file_size) {
                echo json_encode(['success' => false, 'error' => 'File size exceeds 2MB limit.']);
                exit;
            }

            $check = getimagesize($_FILES['profile_avatar']['tmp_name']);
            if ($check === false) {
                echo json_encode(['success' => false, 'error' => 'File is not a valid image.']);
                exit;
            }

            if (move_uploaded_file($_FILES['profile_avatar']['tmp_name'], $target_file)) {
                $avatar = $full_path; // Store the full path
                if ($current_avatar && $current_avatar != 'photos/user.png') {
                    $old_file = $current_avatar; // Full path to old file
                    if (file_exists($old_file)) {
                        unlink($old_file);
                    }
                }
            } else {
                error_log("File Upload Error: Failed to move uploaded file to $target_file");
                echo json_encode(['success' => false, 'error' => 'Error uploading avatar.']);
                exit;
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
            echo json_encode(['success' => false, 'error' => $error_message]);
            exit;
        }
    }

    // Update the user's profile based on their user_id
    $sql = "UPDATE users SET first_name = ?, last_name = ?, age = ?, height = ?, weight = ?, profile_avatar = ? WHERE id = ?";
    $stmt = $connect->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sssddsd", $first_name, $last_name, $age, $height, $weight, $avatar, $user_id);
        if ($stmt->execute()) {
            error_log("Profile updated successfully. New avatar: $avatar");
            echo json_encode([
                'success' => true,
                'updatedData' => [
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'age' => $age,
                    'height' => $height,
                    'weight' => $weight,
                    'profile_avatar' => $avatar // Return the full path
                ]
            ]);
        } else {
            error_log("Database execution failed: " . $connect->error);
            echo json_encode(['success' => false, 'error' => 'Database execution failed: ' . $connect->error]);
        }
        $stmt->close();
    } else {
        error_log("Prepare statement failed: " . $connect->error);
        echo json_encode(['success' => false, 'error' => 'Prepare statement failed: ' . $connect->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

$connect->close();
?>