<?php
require_once 'db_connect.php';

// Only start a session if one isn't already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access!");
}

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

// ==========================================
// 1. FETCH GOALS (FIXED)
// ==========================================
// We use 'AS' to rename the database columns (daily_calories) 
// to match the names your code uses (calories)
$sql_goal = "SELECT 
                daily_calories AS calories, 
                protein_goal AS protein, 
                carbs_goal AS carbs 
             FROM goals 
             WHERE user_id = ? 
             ORDER BY goal_id DESC LIMIT 1";

$stmt = $connect->prepare($sql_goal);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_goal = $stmt->get_result();
$goal = $result_goal->fetch_assoc();

// Default goal values if no goal is set
$dailyGoal = [
    "calories" => $goal['calories'] ?? 2000,
    "protein" => $goal['protein'] ?? 150,
    "carbs" => $goal['carbs'] ?? 250
];

// ==========================================
// 2. FETCH TODAY'S INTAKE
// ==========================================
$sql_intake = "SELECT 
                  SUM(calories) AS total_calories, 
                  SUM(protein) AS total_protein, 
                  SUM(carbs) AS total_carbs 
               FROM meals 
               WHERE user_id = ? AND DATE(date_added) = ?";
$stmt = $connect->prepare($sql_intake);
$stmt->bind_param("is", $user_id, $today);
$stmt->execute();
$result_intake = $stmt->get_result();
$intake = $result_intake->fetch_assoc();

$dailyIntake = [
    "calories" => $intake['total_calories'] ?? 0,
    "protein" => $intake['total_protein'] ?? 0,
    "carbs" => $intake['total_carbs'] ?? 0
];

// ==========================================
// 3. FETCH WEEKLY INTAKE
// ==========================================
$sql_weekly = "SELECT 
                  SUM(calories) AS total_calories, 
                  SUM(protein) AS total_protein, 
                  SUM(carbs) AS total_carbs 
               FROM meals 
               WHERE user_id = ? AND DATE(date_added) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
$stmt = $connect->prepare($sql_weekly);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_weekly = $stmt->get_result();
$weeklyIntakeData = $result_weekly->fetch_assoc();

$weeklyIntake = [
    "calories" => $weeklyIntakeData['total_calories'] ?? 0,
    "protein" => $weeklyIntakeData['total_protein'] ?? 0,
    "carbs" => $weeklyIntakeData['total_carbs'] ?? 0
];

// Calculate weekly goals (Daily Goal * 7)
$weeklyGoal = [
    "calories" => $dailyGoal["calories"] * 7,
    "protein" => $dailyGoal["protein"] * 7,
    "carbs" => $dailyGoal["carbs"] * 7
];

// Helper function for percentage
function calculatePercentage($intake, $goal) {
    if ($goal > 0) {
        return min(round(($intake / $goal) * 100), 100);
    }
    return 0;
}

// Calculate percentages for the Progress Bars
$calories_percentage_daily = calculatePercentage($dailyIntake["calories"], $dailyGoal["calories"]);
$protein_percentage_daily = calculatePercentage($dailyIntake["protein"], $dailyGoal["protein"]);
$carbs_percentage_daily = calculatePercentage($dailyIntake["carbs"], $dailyGoal["carbs"]);

$calories_percentage_weekly = calculatePercentage($weeklyIntake["calories"], $weeklyGoal["calories"]);
$protein_percentage_weekly = calculatePercentage($weeklyIntake["protein"], $weeklyGoal["protein"]);
$carbs_percentage_weekly = calculatePercentage($weeklyIntake["carbs"], $weeklyGoal["carbs"]);

// ==========================================
// 4. FETCH CHART DATA (Consolidated)
// ==========================================
// I combined this into one query to prevent errors if data is missing for some days
$sql_chart = "SELECT DATE(date_added) as log_date, 
                     SUM(calories) as total_calories, 
                     SUM(protein) as total_protein, 
                     SUM(carbs) as total_carbs 
              FROM meals 
              WHERE user_id = ? AND date_added >= DATE(NOW()) - INTERVAL 6 DAY 
              GROUP BY DATE(date_added) 
              ORDER BY DATE(date_added) ASC";

$stmt = $connect->prepare($sql_chart);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_chart = $stmt->get_result();

$dates = [];
$calories = [];
$protein = [];
$carbs = [];

while ($row = $result_chart->fetch_assoc()) {
    $dates[] = date("M d", strtotime($row['log_date']));
    $calories[] = (int)$row['total_calories'];
    $protein[] = (int)$row['total_protein'];
    $carbs[] = (int)$row['total_carbs'];
}

// Convert to JSON for the charts
$dates_json = json_encode($dates);
$calories_json = json_encode($calories);
$protein_json = json_encode($protein);
$carbs_json = json_encode($carbs);
?>