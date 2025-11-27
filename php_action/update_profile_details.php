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

$user_id = (int) $_SESSION['user_id'];

// Fetch current profile data (optional, for fallback)
$sql_current = "SELECT first_name, last_name, age, height, weight FROM users WHERE user_id = ?";
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

$first_name = $current_data['first_name'];
$last_name = $current_data['last_name'];
$age = $current_data['age'];
$height = $current_data['height'];
$weight = $current_data['weight'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Profile details update
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $age = (int) ($_POST['age'] ?? 0);
    $height = (float) ($_POST['height'] ?? 0.0);
    $weight = (float) ($_POST['weight'] ?? 0.0);

    // Validate inputs
    if (empty($first_name) || empty($last_name) || $age <= 0 || $height < 0 || $weight < 0) {
        $valid['messages'] = "All fields (First Name, Last Name, Age, Height, Weight) are required and must be valid";
        echo json_encode($valid);
        exit;
    }

    // Update profile details
    $sql = "UPDATE users SET first_name = ?, last_name = ?, age = ?, height = ?, weight = ? WHERE user_id = ?";
    $stmt = $connect->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssddi", $first_name, $last_name, $age, $height, $weight, $user_id);

        if ($stmt->execute()) {
            // 2. LOG THE WEIGHT HISTORY (New Code)
            // Check if the weight actually changed or if it's a new entry for today
            $logSql = "INSERT INTO weight_logs (user_id, weight) VALUES (?, ?)";
            $logStmt = $connect->prepare($logSql);
            $logStmt->bind_param("id", $user_id, $weight);
            $logStmt->execute();
            $logStmt->close();

            $valid['success'] = true;
            $valid['messages'] = "Successfully Updated";
            $valid['updatedData'] = [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'age' => $age,
                'height' => $height,
                'weight' => $weight
            ];
        } else {
            error_log("Database execution failed: " . $connect->error);
            $valid['messages'] = "Error while updating profile: " . $connect->error;
        }
        $stmt->close();
    } else {
        error_log("Prepare statement failed: " . $connect->error);
        $valid['messages'] = "Prepare statement failed: " . $connect->error;
    }
} else {
    $valid['messages'] = "Invalid request method";
}

$connect->close();
echo json_encode($valid);
?>