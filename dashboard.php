<?php
include 'php_action/db_connect.php';
session_start();


if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION["user_id"];


$sql_user = "SELECT user_name, profile_avatar FROM users WHERE user_id = '$user_id'";
$result_user = $connect->query($sql_user);
$user = $result_user->fetch_assoc();
$user_name = $user['user_name'] ?? 'Unknown';
$user_avatar = $user['profile_avatar'] ?? 'photos/user.png';

// Handle fetch_calorie_data request
if (isset($_GET['fetch_calorie_data'])) {
    $todayCaloriesQuery = "SELECT SUM(calories) AS total_calories
                           FROM meals 
                           WHERE user_id = ? AND DATE(date_added) = CURDATE()";
    $stmt = $connect->prepare($todayCaloriesQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $todayResult = $stmt->get_result()->fetch_assoc();
    $todayCalories = $todayResult['total_calories'] ?: 0;
    $stmt->close();

    $lastWeekCaloriesQuery = "SELECT SUM(calories) AS total_calories
                              FROM meals 
                              WHERE user_id = ? 
                              AND DATE(date_added) BETWEEN DATE_SUB(DATE_SUB(CURDATE(), INTERVAL 1 DAY), INTERVAL 6 DAY) 
                              AND DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
    $stmt = $connect->prepare($lastWeekCaloriesQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $lastWeekResult = $stmt->get_result()->fetch_assoc();
    $lastWeekTotalCalories = $lastWeekResult['total_calories'] ?: 0;
    $stmt->close();

    $lastWeekAverageCalories = $lastWeekTotalCalories / 7;
    $percentageChange = 0;
    $comment = "No change from last week.";
    $direction = "neutral";

    if ($lastWeekAverageCalories > 0) {
        $percentageChange = (($todayCalories - $lastWeekAverageCalories) / $lastWeekAverageCalories) * 100;
        $percentageChange = round($percentageChange, 2);

        if ($percentageChange > 0) {
            $comment = "Higher than last week.";
            $direction = "increase";
        } elseif ($percentageChange < 0) {
            $comment = "Lower than last week.";
            $direction = "decrease";
        }
    }

    echo json_encode([
        'todayValue' => $todayCalories,
        'percentageChange' => abs($percentageChange),
        'comment' => $comment,
        'direction' => $direction,
        'type' => 'calories'
    ]);
    exit();
}

// Handle fetch_protein_data request
if (isset($_GET['fetch_protein_data'])) {
    $todayProteinQuery = "SELECT SUM(protein) AS total_protein
                           FROM meals 
                           WHERE user_id = ? AND DATE(date_added) = CURDATE()";
    $stmt = $connect->prepare($todayProteinQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $todayResult = $stmt->get_result()->fetch_assoc();
    $todayProtein = $todayResult['total_protein'] ?: 0;
    $stmt->close();

    $lastWeekProteinQuery = "SELECT SUM(protein) AS total_protein
                              FROM meals 
                              WHERE user_id = ? 
                              AND DATE(date_added) BETWEEN DATE_SUB(DATE_SUB(CURDATE(), INTERVAL 1 DAY), INTERVAL 6 DAY) 
                              AND DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
    $stmt = $connect->prepare($lastWeekProteinQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $lastWeekResult = $stmt->get_result()->fetch_assoc();
    $lastWeekTotalProtein = $lastWeekResult['total_protein'] ?: 0;
    $stmt->close();

    $lastWeekAverageProtein = $lastWeekTotalProtein / 7;
    $percentageChange = 0;
    $comment = "No change from last week.";
    $direction = "neutral";

    if ($lastWeekAverageProtein > 0) {
        $percentageChange = (($todayProtein - $lastWeekAverageProtein) / $lastWeekAverageProtein) * 100;
        $percentageChange = round($percentageChange, 2);

        if ($percentageChange > 0) {
            $comment = "Higher than last week.";
            $direction = "increase";
        } elseif ($percentageChange < 0) {
            $comment = "Lower than last week.";
            $direction = "decrease";
        }
    }

    echo json_encode([
        'todayValue' => $todayProtein,
        'percentageChange' => abs($percentageChange),
        'comment' => $comment,
        'direction' => $direction,
        'type' => 'protein'
    ]);
    exit();
}

// Handle fetch_carbs_data request
if (isset($_GET['fetch_carbs_data'])) {
    $todayCarbsQuery = "SELECT SUM(carbs) AS total_carbs
                         FROM meals 
                         WHERE user_id = ? AND DATE(date_added) = CURDATE()";
    $stmt = $connect->prepare($todayCarbsQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $todayResult = $stmt->get_result()->fetch_assoc();
    $todayCarbs = $todayResult['total_carbs'] ?: 0;
    $stmt->close();

    $lastWeekCarbsQuery = "SELECT SUM(carbs) AS total_carbs
                            FROM meals 
                            WHERE user_id = ? 
                            AND DATE(date_added) BETWEEN DATE_SUB(DATE_SUB(CURDATE(), INTERVAL 1 DAY), INTERVAL 6 DAY) 
                            AND DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
    $stmt = $connect->prepare($lastWeekCarbsQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $lastWeekResult = $stmt->get_result()->fetch_assoc();
    $lastWeekTotalCarbs = $lastWeekResult['total_carbs'] ?: 0;
    $stmt->close();

    $lastWeekAverageCarbs = $lastWeekTotalCarbs / 7;
    $percentageChange = 0;
    $comment = "No change from last week.";
    $direction = "neutral";

    if ($lastWeekAverageCarbs > 0) {
        $percentageChange = (($todayCarbs - $lastWeekAverageCarbs) / $lastWeekAverageCarbs) * 100;
        $percentageChange = round($percentageChange, 2);

        if ($percentageChange > 0) {
            $comment = "Higher than last week.";
            $direction = "increase";
        } elseif ($percentageChange < 0) {
            $comment = "Lower than last week.";
            $direction = "decrease";
        }
    }

    echo json_encode([
        'todayValue' => $todayCarbs,
        'percentageChange' => abs($percentageChange),
        'comment' => $comment,
        'direction' => $direction,
        'type' => 'carbs'
    ]);
    exit();
}

// Existing fetch_totals endpoint
if (isset($_GET['fetch_totals'])) {
    $totalIntakeQuery = "SELECT SUM(calories) AS total_calories, SUM(protein) AS total_protein, SUM(carbs) AS total_carbs
                         FROM meals WHERE user_id = ? AND DATE(date_added) = CURDATE()";
    $stmt = $connect->prepare($totalIntakeQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $totalIntakeResult = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $waterIntakeQuery = "SELECT SUM(amount) AS total_water
                         FROM water_intake WHERE user_id = ? AND DATE(date_added) = CURDATE()";
    $stmt = $connect->prepare($waterIntakeQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $waterIntakeResult = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $userQuery = "SELECT water_goal FROM users WHERE id = ?";
    $stmt = $connect->prepare($userQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $userResult = $stmt->get_result()->fetch_assoc();
    $waterGoal = $userResult['water_goal'] ?: 2.0;
    $stmt->close();

    echo json_encode([
        'totalCalories' => $totalIntakeResult['total_calories'] ?: 0,
        'totalProtein' => $totalIntakeResult['total_protein'] ?: 0,
        'totalCarbs' => $totalIntakeResult['total_carbs'] ?: 0,
        'totalWater' => $waterIntakeResult['total_water'] ?: 0,
        'waterGoal' => $waterGoal
    ]);
    exit();
}

// Handle meal insertion
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['water_amount'])) {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input && isset($input['meal_name'])) {
        $meal_name = $input['meal_name'];
        $calories = $input['calories'];
        $protein = $input['protein'];
        $carbs = $input['carbs'];
    } else {
        $meal_name = $_POST['meal_name'] ?? '';
        $calories = $_POST['calories'] ?? 0;
        $protein = $_POST['protein'] ?? 0;
        $carbs = $_POST['carbs'] ?? 0;
    }

    if (empty($meal_name) || !is_numeric($calories) || !is_numeric($protein) || !is_numeric($carbs)) {
        echo json_encode(["error" => "Invalid input data"]);
        exit();
    }

    $stmt = $connect->prepare("INSERT INTO meals (user_id, meal_name, calories, protein, carbs) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isddd", $user_id, $meal_name, $calories, $protein, $carbs);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Error adding meal: " . $connect->error]);
    }

    $stmt->close();
    exit();
}


// Get current date and the start of the current week (Monday)
$currentDate = date('Y-m-d');
$startOfWeek = date('Y-m-d', strtotime('monday this week'));

// Default period is today
$period = isset($_GET['period']) ? $_GET['period'] : 'today';

// Fetch today's intake (meals)
$totalIntakeQuery = "SELECT SUM(calories) AS total_calories, SUM(protein) AS total_protein, SUM(carbs) AS total_carbs
                     FROM meals WHERE user_id = ? AND DATE(date_added) = CURDATE()";
$stmt = $connect->prepare($totalIntakeQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$totalIntakeResult = $stmt->get_result()->fetch_assoc();
$totalCalories = $totalIntakeResult['total_calories'] ?: 0;
$totalProtein = $totalIntakeResult['total_protein'] ?: 0;
$totalCarbs = $totalIntakeResult['total_carbs'] ?: 0;
$stmt->close();




$sql = "SELECT * FROM posts ORDER BY post_time DESC";
$result = $connect->query($sql);
$posts = [];
while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}


$connect->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script defer src="script.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <title>BiteTrack - Your Nutrient Tracker!</title>
    <script src="js/food_db_api.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="custom/css/custom.css">
    <link rel="stylesheet" href="css/button.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        function toggleCheck() {
            const button = document.getElementById("check-button");
            const icon = document.getElementById("check-icon");
            const message = document.getElementById("saved-message");

            // Toggle checked state
            const isChecked = button.classList.contains("bg-green-500");

            if (!isChecked) {
                // Mark as checked
                button.classList.add("bg-green-500", "border-green-500", "text-white");

                message.classList.remove("opacity-0");
            } else {
                // Uncheck
                button.classList.remove("bg-green-500", "border-green-500", "text-white");
                icon.setAttribute("fill", "none");
                message.classList.add("opacity-0");
            }
        }
        // JavaScript to toggle dropdown
        document.addEventListener("DOMContentLoaded", function() {
            updateCalorieData();
            updateProteinData();
            updateCarbsData();
            updateTotals();


            const toggleBtn = document.getElementById("dashboardToggle");
            const dropdownMenu = document.getElementById("dashboardDropdown");

            toggleBtn.addEventListener("click", function(event) {
                event.preventDefault();
                dropdownMenu.classList.toggle("max-h-0");
                dropdownMenu.classList.toggle("opacity-0");
                dropdownMenu.classList.toggle("max-h-[200px]");
                dropdownMenu.classList.toggle("opacity-100");
            });
        });
        document.addEventListener("DOMContentLoaded", function() {
            var options = {
                series: [75.55],
                chart: {
                    height: 229,
                    type: "radialBar",
                },
                plotOptions: {
                    radialBar: {
                        hollow: {
                            size: "70%",
                        },

                        // Apply the gradient to the radial bar
                        track: {
                            background: '#e6e6e6', // Light background color for track
                        }
                    },
                },
                colors: ['#FCD404', '#FB6F74'], // Define the gradient colors
                fill: {
                    type: 'gradient', // Use gradient fill
                    gradient: {
                        shade: 'light',
                        type: 'linear',
                        shadeIntensity: 0.5,
                        gradientToColors: ['#FB6F74'], // Gradient from #FCD404 to #FB6F74
                        inverseColors: false,
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 100]
                    }
                },
                labels: ["Progress"],
            };

            var chart = new ApexCharts(document.querySelector("#chartTwo"), options);
            chart.render();
        });


        async function fetchCalories() {


            const query = document.getElementById("foodInput").value;
            if (!query) {
                alert("Please enter a food item.");
                return;
            }

            const apiKey = "FmEM2rbCs+c9j0rAbzaJRA==IVZqSzB9NOhvqjAs";
            const url = `https://api.calorieninjas.com/v1/nutrition?query=${encodeURIComponent(query)}`;

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Api-Key': apiKey
                    }
                });

                const data = await response.json();

                if (data.items && data.items.length > 0) {
                    let item = data.items[0];

                    document.getElementById("bmr1").innerHTML = `<strong>${item.calories}</strong> kcal`;
                    document.getElementById("bmr2").innerHTML = `<strong>${item.protein_g}</strong> g`;
                    document.getElementById("bmr3").innerHTML = `<strong>${item.carbohydrates_total_g}</strong> g`;

                    const mealData = {
                        meal_name: query,
                        calories: item.calories,
                        protein: item.protein_g,
                        carbs: item.carbohydrates_total_g
                    };

                    const addResponse = await fetch('', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(mealData)
                    });

                    const addResult = await addResponse.json();

                    if (addResult.success) {
                        alert("Meal added successfully!");
                        updateTotals();
                        updateCalorieData();
                        updateProteinData();
                        updateCarbsData();
                    } else {
                        console.error("Error adding meal:", addResult.error);
                        alert("Error adding meal: " + (addResult.error || "Unknown error"));
                    }
                } else {
                    document.getElementById("bmr1").innerHTML = "No data found";
                    document.getElementById("bmr2").innerHTML = "No data found";
                    document.getElementById("bmr3").innerHTML = "No data found";
                    alert("No nutritional data found for this food item.");
                }
            } catch (error) {
                console.error("Error fetching data:", error);
                document.getElementById("bmr1").innerHTML = "Error fetching data";
                document.getElementById("bmr2").innerHTML = "Error fetching data";
                document.getElementById("bmr3").innerHTML = "Error fetching data";
                alert("Error fetching nutritional data.");
            }
        }

        async function logWaterIntake() {
            const waterAmount = document.getElementById("waterInput").value;
            if (!waterAmount || waterAmount <= 0) {
                alert("Please enter a valid amount of water (greater than 0).");
                return;
            }

            const formData = new FormData();
            formData.append("water_amount", waterAmount);

            try {
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    const message = document.getElementById("water-saved-message");
                    message.classList.remove("opacity-0");
                    setTimeout(() => message.classList.add("opacity-0"), 3000);
                    document.getElementById("waterInput").value = '';
                    updateTotals();
                } else {
                    console.error("Error logging water intake:", result.error);
                    alert("Error logging water intake: " + (result.error || "Unknown error"));
                }
            } catch (error) {
                console.error("Error logging water intake:", error);
                alert("Error logging water intake.");
            }
        }

        async function updateTotals() {
            try {
                const response = await fetch(window.location.href + (window.location.search ? '&' : '?') + 'fetch_totals=1');
                const data = await response.json();
                if (data.totalCalories !== undefined) {
                    document.getElementById("bmr1").innerHTML = `<strong>${data.totalCalories}</strong> kcal`;
                    document.getElementById("bmr2").innerHTML = `<strong>${data.totalProtein}</strong> g`;
                    document.getElementById("bmr3").innerHTML = `<strong>${data.totalCarbs}</strong> g`;
                    document.getElementById("bmr4").innerHTML = `<strong>${data.totalWater}</strong> / <span id="waterGoal">${data.waterGoal}</span> L`;

                    const waterInputContainer = document.getElementById("waterInputContainer");
                    const waterGoalReached = document.getElementById("waterGoalReached");
                    if (data.totalWater >= data.waterGoal) {
                        waterInputContainer.classList.add("hidden");
                        waterGoalReached.classList.remove("hidden");
                    } else {
                        waterInputContainer.classList.remove("hidden");
                        waterGoalReached.classList.add("hidden");
                    }
                } else {
                    console.error("Error fetching totals:", data.error);
                }
            } catch (error) {
                console.error("Error updating totals:", error);
            }
        }

        async function updateCalorieData() {
            try {
                const response = await fetch(window.location.href + (window.location.search ? '&' : '?') + 'fetch_calorie_data=1');
                const data = await response.json();
                if (data.todayValue !== undefined) {
                    document.getElementById("calorieIntake").innerText = `${data.todayValue.toLocaleString()} cal`;
                    updateMetricData("calorie", data);
                } else {
                    console.error("Error fetching calorie data:", data.error);
                }
            } catch (error) {
                console.error("Error updating calorie data:", error);
                document.getElementById("calorieIntake").innerText = "Error";
            }
        }

        async function updateProteinData() {
            try {
                const response = await fetch(window.location.href + (window.location.search ? '&' : '?') + 'fetch_protein_data=1');
                const data = await response.json();
                if (data.todayValue !== undefined) {
                    document.getElementById("proteinIntake").innerText = `${data.todayValue.toLocaleString()} g`;
                    updateMetricData("protein", data);
                } else {
                    console.error("Error fetching protein data:", data.error);
                }
            } catch (error) {
                console.error("Error updating protein data:", error);
                document.getElementById("proteinIntake").innerText = "Error";
            }
        }

        async function updateCarbsData() {
            try {
                const response = await fetch(window.location.href + (window.location.search ? '&' : '?') + 'fetch_carbs_data=1');
                const data = await response.json();
                if (data.todayValue !== undefined) {
                    document.getElementById("carbsIntake").innerText = `${data.todayValue.toLocaleString()} g`;
                    updateMetricData("carbs", data);
                } else {
                    console.error("Error fetching carbs data:", data.error);
                }
            } catch (error) {
                console.error("Error updating carbs data:", error);
                document.getElementById("carbsIntake").innerText = "Error";
            }
        }

        function updateMetricData(type, data) {
            const changeElement = document.getElementById(`${type}Change`);
            const changeIcon = document.getElementById(`${type}ChangeIcon`);
            const changeValue = document.getElementById(`${type}ChangeValue`);
            const commentElement = document.getElementById(`${type}Comment`);

            changeValue.innerText = `${data.percentageChange}%`;
            commentElement.innerText = data.comment;

            if (data.direction === "increase") {
                changeElement.classList.remove("bg-red-100", "text-red-600");
                changeElement.classList.add("bg-green-100", "text-green-600");
                changeIcon.innerHTML = `<path fill-rule="evenodd" clip-rule="evenodd" d="M6.40505 1.59382L9.40514 4.5918C9.69814 4.8846 9.69831 5.35947 9.40552 5.65246C9.11273 5.94546 8.63785 5.94562 8.34486 5.65283L6.62329 3.93247L6.62329 10.125C6.62329 10.5392 6.28751 10.875 5.87329 10.875C5.45908 10.875 5.12329 10.5392 5.12329 10.125L5.12329 3.93578L3.40516 5.65281C3.11218 5.94561 2.6373 5.94546 2.3445 5.65248C2.0517 5.35949 2.05185 4.88462 2.34484 4.59182L5.31462 1.62393C5.45194 1.47072 5.65136 1.37431 5.87329 1.37431C5.8736 1.37431 5.8739 1.37431 5.87421 1.37431C6.0663 1.37414 6.25845 1.4473 6.40505 1.59382Z"></path>`;
            } else if (data.direction === "decrease") {
                changeElement.classList.remove("bg-green-100", "text-green-600");
                changeElement.classList.add("bg-red-100", "text-red-600");
                changeIcon.innerHTML = `<path fill-rule="evenodd" clip-rule="evenodd" d="M5.31462 10.3761C5.45194 10.5293 5.65136 10.6257 5.87329 10.6257C5.8736 10.6257 5.8739 10.6257 5.87421 10.6257C6.0663 10.6259 6.25845 10.5527 6.40505 10.4062L9.40514 7.4082C9.69814 7.11541 9.69831 6.64054 9.40552 6.34754C9.11273 6.05454 8.63785 6.05438 8.34486 6.34717L6.62329 8.06753V1.875C6.62329 1.46079 6.28751 1.125 5.87329 1.125C5.45908 1.125 5.12329 1.46079 5.12329 1.875V8.06422L3.40516 6.34719C3.11218 6.05439 2.6373 6.05454 2.3445 6.34752C2.0517 6.64051 2.05185 7.11538 2.34484 7.40818L5.31462 10.3761Z"></path>`;
            } else {
                changeElement.classList.remove("bg-green-100", "text-green-600", "bg-red-100", "text-red-600");
                changeElement.classList.add("bg-gray-100", "text-gray-600");
                changeIcon.innerHTML = `<path fill-rule="evenodd" clip-rule="evenodd" d="M2.25 6C2.25 5.58579 2.58579 5.25 3 5.25H9C9.41421 5.25 9.75 5.58579 9.75 6C9.75 6.41421 9.41421 6.75 9 6.75H3C2.58579 6.75 2.25 6.41421 2.25 6Z"></path>`;
            }
        }

        // Call all update functions on page load
        document.addEventListener("DOMContentLoaded", function() {
            updateCalorieData();
            updateProteinData();
            updateCarbsData();
            updateTotals();
            // Existing chart and dropdown event listeners remain here
        });
    </script>

</head>

<body class="flex flex-col min-h-screen">

    <main class="flex-grow">
        <!-- MAIN CONTENT -->

        <!-- ===== Page Wrapper Start ===== -->
        <div class="flex h-screen overflow-hidden">
            <!-- ===== SIDEBAR INCLUSION ===== -->
            <?php require_once 'includes/sidebar.php'; ?>


            <!-- ===== Content Area Start ===== -->
            <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
                <!-- Small Device Overlay Start -->
                <div :class="sidebarToggle ? 'block lg:hidden' : 'hidden'"
                    class="fixed z-9 h-screen w-full bg-gray-900/50 hidden"></div>
                <!-- Small Device Overlay End -->

                <!-- ===== Main Content Start ===== -->
                <main>
                    <!-- ===== Header Start ===== -->
                    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
                        <div class="grid grid-cols-30 gap-4 md:gap-6">
                            <div class="col-span-12 space-y-6 xl:col-span-7 ">
                                <!-- Metric Group One -->
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-1 md:gap-6">
                                    <!-- Metric Item Start -->
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 md:gap-6">
                                        <!-- Calorie Intake -->
                                        <div class="w-full max-w-full sm:max-w-[24rem] md:max-w-[28rem] lg:max-w-[32rem] rounded-2xl border border-gray-200 bg-white p-4 md:p-6 shadow-md mx-auto flex flex-col space-y-3">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <span class="text-sm text-gray-500">Calorie Intake</span>
                                                    <h4 id="calorieIntake" class="mt-1 text-lg font-bold text-gray-800">Loading...</h4>
                                                </div>
                                                <span id="calorieChange" class="flex items-center gap-1 rounded-full py-0.5 px-2 text-sm font-medium">
                                                    <svg id="calorieChangeIcon" class="fill-current" width="12" height="12" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d=""></path>
                                                    </svg>
                                                    <span id="calorieChangeValue">0%</span>
                                                </span>
                                            </div>
                                            <div id="calorieComment" class="text-sm text-gray-500 text-center">
                                                Loading...
                                            </div>
                                        </div>

                                        <!-- Protein Intake -->
                                        <div class="w-full max-w-full sm:max-w-[24rem] md:max-w-[28rem] lg:max-w-[32rem] rounded-2xl border border-gray-200 bg-white p-4 md:p-6 shadow-md mx-auto flex flex-col space-y-3">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <span class="text-sm text-gray-500">Protein Intake</span>
                                                    <h4 id="proteinIntake" class="mt-1 text-lg font-bold text-gray-800">Loading...</h4>
                                                </div>
                                                <span id="proteinChange" class="flex items-center gap-1 rounded-full py-0.5 px-2 text-sm font-medium">
                                                    <svg id="proteinChangeIcon" class="fill-current" width="12" height="12" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d=""></path>
                                                    </svg>
                                                    <span id="proteinChangeValue">0%</span>
                                                </span>
                                            </div>
                                            <div id="proteinComment" class="text-sm text-gray-500 text-center">
                                                Loading...
                                            </div>
                                        </div>

                                        <!-- Carbs Intake -->
                                        <div class="w-full max-w-full sm:max-w-[24rem] md:max-w-[28rem] lg:max-w-[32rem] rounded-2xl border border-gray-200 bg-white p-4 md:p-6 shadow-md mx-auto flex flex-col space-y-3">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <span class="text-sm text-gray-500">Carbs Intake</span>
                                                    <h4 id="carbsIntake" class="mt-1 text-lg font-bold text-gray-800">Loading...</h4>
                                                </div>
                                                <span id="carbsChange" class="flex items-center gap-1 rounded-full py-0.5 px-2 text-sm font-medium">
                                                    <svg id="carbsChangeIcon" class="fill-current" width="12" height="12" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d=""></path>
                                                    </svg>
                                                    <span id="carbsChangeValue">0%</span>
                                                </span>
                                            </div>
                                            <div id="carbsComment" class="text-sm text-gray-500 text-center">
                                                Loading...
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Metric Item Start -->


                                    <!-- Metric Item End -->
                                </div>
                                <!-- Metric Group One -->

                                <!-- ====== Chart One Start -->
                                <div
                                    class="overflow-hidden rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-md max-h-90 overflow-y-auto">
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="text-lg font-semibold text-gray-800">Daily Log Checker</h3>
                                    </div>

                                    <div
                                        class="w-full rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition p-4 flex items-center justify-between">
                                        <div class="flex-1">
                                            <h5 class="text-md font-semibold text-gray-900">Drank water?</h5>
                                            <p class="text-sm text-gray-700">Have you had enough glasses of water today?
                                            </p>
                                        </div>

                                        <!-- Check Button -->
                                        <button id="check-button" onclick="toggleCheck()"
                                            class="w-12 h-12 flex items-center justify-center rounded-full border-2 border-gray-400 text-gray-400 transition-all duration-300 ease-in-out hover:bg-gray-100 focus:outline-none">
                                            <svg id="check-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>

                                        </button>
                                    </div>

                                    <!-- Saved Message -->
                                    <div id="saved-message"
                                        class="mt-1 text-sm text-green-600 font-semibold opacity-0 transition-opacity duration-500">
                                        ✅ Great Job!
                                    </div>

                                    <!-- Food Intake -->
                                    <div
                                        class="w-full rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition p-3 flex items-center justify-between mt-2">
                                        <div class="flex-1">
                                            <h5 class="text-md font-semibold text-gray-900">Food Eaten</h5>
                                            <p class="text-sm text-gray-700">What did you eat today?</p>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <input type="text" id="foodInput"
                                                class="border border-gray-300 p-2 rounded-lg text-sm"
                                                placeholder="Enter food name" />
                                            <button type="submit" onclick="fetchCalories()"
                                                class="bg-blue-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-blue-600 transition">Add</button>
                                        </div>
                                    </div>
                                    <!-- Calories, Protein, Carbs -->
                                    <div class="grid grid-cols-3 gap-2 mt-3">
                                        <!-- Calories -->
                                        <!-- Calories -->
                                        <div class="w-full rounded-lg border border-gray-200 bg-white shadow-sm p-3 text-center">
                                            <h5 class="text-md font-semibold text-gray-900">Calories</h5>
                                            <div id="bmr1" class="text-lg text-gray-900 font-bold mt-1"><strong><?= htmlspecialchars($totalCalories) ?></strong> kcal</div>
                                        </div>

                                        <!-- Protein -->
                                        <div class="w-full rounded-lg border border-gray-200 bg-white shadow-sm p-3 text-center">
                                            <h5 class="text-md font-semibold text-gray-900">Protein</h5>
                                            <div id="bmr2" class="text-lg text-gray-900 font-bold mt-1"><strong><?= htmlspecialchars($totalProtein) ?></strong> g</div>
                                        </div>

                                        <!-- Carbohydrates -->
                                        <div class="w-full rounded-lg border border-gray-200 bg-white shadow-sm p-3 text-center">
                                            <h5 class="text-md font-semibold text-gray-900">Carbs</h5>
                                            <div id="bmr3" class="text-lg text-gray-900 font-bold mt-1"><strong><?= htmlspecialchars($totalCarbs) ?></strong> g</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ====== Chart One End -->
                            </div>
                            <!--DASHBOARD: GOALS -->







                            <!-- ====== Community Page Start ====== -->
                            <div class="col-span-12">
                                <div
                                    class="overflow-auto max-h-90 rounded-2xl border border-gray-200 bg-white p-5 shadow-lg ">
                                    <!-- Header -->
                                    <div
                                        class="bg-gradient-to-r from-blue-500 to-indigo-600 text-black text-lg font-semibold py-3 px-4 rounded-t-xl">
                                        News Feed ✨
                                    </div>

                                    <!-- Scrollable Section -->
                                    <div id="news-feed" class="relative h-screen overflow-y-auto p-2 space-y-6 scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-200 flex-1">
                                    <?php foreach ($posts as $post): ?>
                                        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                                            <div class="flex items-center space-x-3">
                                                <img src="<?php echo htmlspecialchars($post['user_avatar']); ?>" alt="User Icon" class="w-10 h-10 rounded-full">
                                                <div>
                                                    <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($post['user_name']); ?></p>
                                                    <p class="text-sm text-gray-500"><?php echo $post['post_time']; ?></p>
                                                </div>
                                            </div>
                                            <p class="mt-2 text-gray-700"><?php echo htmlspecialchars($post['post_content']); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                                </div>
                            </div>
                            <!-- ====== Community Page End ====== -->

                </main>
                <!-- ===== Main Content End ===== -->
            </div>
            <!-- ===== Content Area End ===== -->
        </div>
        <!-- ===== Page Wrapper End ===== -->
    </main>
</body>
<script>
        // Function to fetch posts dynamically
        async function fetchPosts() {
            try {
                const response = await fetch('fetch_posts.php');
                const posts = await response.json();

                const newsFeed = document.getElementById('news-feed');
                newsFeed.innerHTML = '';

                posts.forEach(post => {
                    const postElement = document.createElement('div');
                    postElement.className = 'bg-white p-4 rounded-xl border border-gray-200 shadow-sm';
                    postElement.innerHTML = `
                        <div class="flex items-center space-x-3">
                            <img src="${post.user_avatar}" alt="User Icon" class="w-10 h-10 rounded-full">
                            <div>
                                <p class="font-semibold text-gray-800">${post.user_name}</p>
                                <p class="text-sm text-gray-500">${post.post_time}</p>
                            </div>
                        </div>
                        <p class="mt-2 text-gray-700">${post.post_content}</p>
                    `;
                    newsFeed.appendChild(postElement);
                });
            } catch (error) {
                console.error('Error fetching posts:', error);
            }

            fetchPosts();
        }
        </script>

<script defer="" src="bundle.js"></script>
<script defer=""
    src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015"
    integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ=="
    data-cf-beacon="{&quot;rayId&quot;:&quot;91b7c147fdd902a9&quot;,&quot;version&quot;:&quot;2025.1.0&quot;,&quot;r&quot;:1,&quot;token&quot;:&quot;67f7a278e3374824ae6dd92295d38f77&quot;,&quot;serverTiming&quot;:{&quot;name&quot;:{&quot;cfExtPri&quot;:true,&quot;cfL4&quot;:true,&quot;cfSpeedBrain&quot;:true,&quot;cfCacheStatus&quot;:true}}}"
    crossorigin="anonymous"></script>


<svg id="SvgjsSvg1001" width="2" height="0" xmlns="http://www.w3.org/2000/svg" version="1.1"
    xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev"
    style="overflow: hidden; top: -100%; left: -100%; position: absolute; opacity: 0;">
    <defs id="SvgjsDefs1002"></defs>
    <polyline id="SvgjsPolyline1003" points="0,0"></polyline>
    <path id="SvgjsPath1004" d="M0 0 ">

    </path>
</svg>

<div class="jvm-tooltip"></div>

</body>

</html>