<?php
require_once 'db_connect.php';
session_start(); // Start session

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access!");
}

$user_id = $_SESSION['user_id']; // Get logged-in user ID
$today = date('Y-m-d');

// Fetch the most recent **daily goal** for the user
$sql_goal = "SELECT calories, protein, carbs, date_set FROM daily_goals WHERE user_id = ? ORDER BY date_set DESC LIMIT 1";
$stmt = $connect->prepare($sql_goal);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_goal = $stmt->get_result();
$goal = $result_goal->fetch_assoc();

// Default goal values if no goal is set
$dailyGoal = [
    "calories" => $goal['calories'] ?? 0,
    "protein" => $goal['protein'] ?? 0,
    "carbs" => $goal['carbs'] ?? 0
    "date_set" => $goal['date_set'] ?? 0

];

// Fetch **today's intake** for the user
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

// Default intake values if no meals are logged
$dailyIntake = [
    "calories" => $intake['total_calories'] ?? 0,
    "protein" => $intake['total_protein'] ?? 0,
    "carbs" => $intake['total_carbs'] ?? 0
];

// Fetch **weekly intake (last 7 days)**
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
$weeklyIntake = $result_weekly->fetch_assoc();

// Default weekly intake values if no meals are logged
$weeklyIntake = [
    "calories" => $weeklyIntake['total_calories'] ?? 0,
    "protein" => $weeklyIntake['total_protein'] ?? 0,
    "carbs" => $weeklyIntake['total_carbs'] ?? 0
];

// Calculate **weekly goals** (Daily Goal × 7)
$weeklyGoal = [
    "calories" => $dailyGoal["calories"] * 7,
    "protein" => $dailyGoal["protein"] * 7,
    "carbs" => $dailyGoal["carbs"] * 7
];

//  Function to calculate percentage progress (capped at 100%)
function calculatePercentage($intake, $goal)
{
    if ($goal > 0) {
        return min(round(($intake / $goal) * 100, 1), 100);
    }
    return 0;
}

//  Calculate daily & weekly progress percentages
$calories_percentage_daily = calculatePercentage($dailyIntake["calories"], $dailyGoal["calories"]);
$protein_percentage_daily = calculatePercentage($dailyIntake["protein"], $dailyGoal["protein"]);
$carbs_percentage_daily = calculatePercentage($dailyIntake["carbs"], $dailyGoal["carbs"]);

$calories_percentage_weekly = calculatePercentage($weeklyIntake["calories"], $weeklyGoal["calories"]);
$protein_percentage_weekly = calculatePercentage($weeklyIntake["protein"], $weeklyGoal["protein"]);
$carbs_percentage_weekly = calculatePercentage($weeklyIntake["carbs"], $weeklyGoal["carbs"]);

// Fetch daily calorie intake for the past 7 days
$sql_weeklycalorie = "SELECT DATE(date_added) AS log_date, SUM(calories) AS total_calories 
               FROM meals 
               WHERE user_id = ? AND DATE(date_added) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
               GROUP BY DATE(date_added) 
               ORDER BY DATE(date_added) ASC";
$stmt = $connect->prepare($sql_weeklycalorie);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_weeklycalorie = $stmt->get_result();

$sql_weeklyprotein = "SELECT DATE(date_added) AS log_date, SUM(protein) AS total_protein 
               FROM meals 
               WHERE user_id = ? AND DATE(date_added) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
               GROUP BY DATE(date_added) 
               ORDER BY DATE(date_added) ASC";
$stmt = $connect->prepare($sql_weeklyprotein);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_weeklyprotein = $stmt->get_result();

$sql_weeklycarbs = "SELECT DATE(date_added) AS log_date, SUM(carbs) AS total_carbs 
               FROM meals 
               WHERE user_id = ? AND DATE(date_added) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
               GROUP BY DATE(date_added) 
               ORDER BY DATE(date_added) ASC";
$stmt = $connect->prepare($sql_weeklycarbs);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_weeklycarbs = $stmt->get_result();

// Prepare data for ApexCharts
$dates = [];
$calories = [];
$protein = [];
$carbs = [];

while ($row = $result_weeklycalorie->fetch_assoc()) {
    $dates[] = date("M d", strtotime($row['log_date']));
    $calories[] = $row['total_calories'];
}

while ($row = $result_weeklyprotein->fetch_assoc()) {
    $protein[] = $row['total_protein'];
}

while ($row = $result_weeklycarbs->fetch_assoc()) {
    $carbs[] = $row['total_carbs'];
}

// Convert PHP arrays to JavaScript JSON format
$dates_json = json_encode($dates);
$calories_json = json_encode($calories);
$protein_json = json_encode($protein);
$carbs_json = json_encode($carbs);
?>
