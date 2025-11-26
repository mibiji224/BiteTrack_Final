<?php
include 'php_action/db_connect.php';
session_start();

// Check DB Connection
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

// Ensure User is Logged In
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// 1. Fetch User Info
$sql_user = "SELECT user_name, profile_avatar FROM users WHERE user_id = ?";
$stmt = $connect->prepare($sql_user);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_user = $stmt->get_result();
$user = $result_user->fetch_assoc();
$user_name = $user['user_name'] ?? 'Unknown';
$user_avatar = $user['profile_avatar'] ?? 'photos/user.png';
$stmt->close();

// --- API HANDLERS (AJAX) ---

// Fetch Calories
if (isset($_GET['fetch_calorie_data'])) {
    $todayCaloriesQuery = "SELECT SUM(calories) AS total_calories FROM meals WHERE user_id = ? AND DATE(date_added) = CURDATE()";
    $stmt = $connect->prepare($todayCaloriesQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $todayResult = $stmt->get_result()->fetch_assoc();
    $todayCalories = $todayResult['total_calories'] ?: 0;
    $stmt->close();

    $lastWeekCaloriesQuery = "SELECT SUM(calories) AS total_calories FROM meals WHERE user_id = ? AND DATE(date_added) BETWEEN DATE_SUB(DATE_SUB(CURDATE(), INTERVAL 1 DAY), INTERVAL 6 DAY) AND DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
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

// Fetch Protein
if (isset($_GET['fetch_protein_data'])) {
    $todayProteinQuery = "SELECT SUM(protein) AS total_protein FROM meals WHERE user_id = ? AND DATE(date_added) = CURDATE()";
    $stmt = $connect->prepare($todayProteinQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $todayResult = $stmt->get_result()->fetch_assoc();
    $todayProtein = $todayResult['total_protein'] ?: 0;
    $stmt->close();

    $lastWeekProteinQuery = "SELECT SUM(protein) AS total_protein FROM meals WHERE user_id = ? AND DATE(date_added) BETWEEN DATE_SUB(DATE_SUB(CURDATE(), INTERVAL 1 DAY), INTERVAL 6 DAY) AND DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
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

// Fetch Carbs
if (isset($_GET['fetch_carbs_data'])) {
    $todayCarbsQuery = "SELECT SUM(carbs) AS total_carbs FROM meals WHERE user_id = ? AND DATE(date_added) = CURDATE()";
    $stmt = $connect->prepare($todayCarbsQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $todayResult = $stmt->get_result()->fetch_assoc();
    $todayCarbs = $todayResult['total_carbs'] ?: 0;
    $stmt->close();

    $lastWeekCarbsQuery = "SELECT SUM(carbs) AS total_carbs FROM meals WHERE user_id = ? AND DATE(date_added) BETWEEN DATE_SUB(DATE_SUB(CURDATE(), INTERVAL 1 DAY), INTERVAL 6 DAY) AND DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
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

// Fetch Totals (Water + Summary)
if (isset($_GET['fetch_totals'])) {
    $totalIntakeQuery = "SELECT SUM(calories) AS total_calories, SUM(protein) AS total_protein, SUM(carbs) AS total_carbs FROM meals WHERE user_id = ? AND DATE(date_added) = CURDATE()";
    $stmt = $connect->prepare($totalIntakeQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $totalIntakeResult = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $waterIntakeQuery = "SELECT SUM(amount) AS total_water FROM water_intake WHERE user_id = ? AND DATE(date_added) = CURDATE()";
    $stmt = $connect->prepare($waterIntakeQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $waterIntakeResult = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $userQuery = "SELECT water_goal FROM users WHERE user_id = ?";
    $stmt = $connect->prepare($userQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $userResult = $stmt->get_result()->fetch_assoc();
    $waterGoal = $userResult['water_goal'] ?? 2.0;
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

// Handle Meal Insertion
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

// Handle Water Insertion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['water_amount'])) {
    $waterAmount = $_POST['water_amount'];
    $stmt = $connect->prepare("INSERT INTO water_intake (user_id, amount) VALUES (?, ?)");
    $stmt->bind_param("id", $user_id, $waterAmount);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Error adding water: " . $connect->error]);
    }
    $stmt->close();
    exit();
}

// --- INITIAL PAGE LOAD DATA ---

$today = date('Y-m-d');

// Fetch today's totals for initial render (PHP Side)
$totalIntakeQuery = "SELECT SUM(calories) AS total_calories, SUM(protein) AS total_protein, SUM(carbs) AS total_carbs FROM meals WHERE user_id = ? AND DATE(date_added) = CURDATE()";
$stmt = $connect->prepare($totalIntakeQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$totalIntakeResult = $stmt->get_result()->fetch_assoc();
$totalCalories = $totalIntakeResult['total_calories'] ?: 0;
$totalProtein = $totalIntakeResult['total_protein'] ?: 0;
$totalCarbs = $totalIntakeResult['total_carbs'] ?: 0;
$stmt->close();

// 2. FETCH POSTS FOR THE FEED (FIXED & MERGED)
$sql_posts = "SELECT * FROM posts ORDER BY post_time DESC LIMIT 10"; 
$result_posts = $connect->query($sql_posts);

$posts = [];
while ($row = $result_posts->fetch_assoc()) {
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
    <title>BiteTrack - Dashboard</title>
    <script src="js/food_db_api.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="custom/css/custom.css">
    <link rel="stylesheet" href="css/button.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen">

    <div class="flex h-screen overflow-hidden">
        
        <?php require_once 'includes/sidebar.php'; ?>

        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            
            <div :class="sidebarToggle ? 'block lg:hidden' : 'hidden'" class="fixed z-20 h-screen w-full bg-gray-900/50 hidden"></div>

            <main class="w-full bg-gray-50 min-h-screen transition-all duration-200 ease-in-out">
                <div class="p-6 mx-auto max-w-7xl">
                    
                    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Welcome back, <?= htmlspecialchars($user_name) ?>! 👋</h2>
                            <p class="text-sm text-gray-500">Here's what's happening with your nutrition today.</p>
                        </div>
                        <div class="mt-4 sm:mt-0">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <?= date("l, F j") ?>
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6">

                        <div class="col-span-12 lg:col-span-8 space-y-6">
                            
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
                                    <div>
                                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Calories</span>
                                        <h4 id="calorieIntake" class="mt-2 text-2xl font-bold text-gray-800">Loading...</h4>
                                    </div>
                                    <div class="mt-4 flex items-center justify-between">
                                        <span id="calorieChange" class="flex items-center gap-1 rounded-full py-1 px-2 text-xs font-medium bg-gray-100 text-gray-600">
                                            <span id="calorieChangeIcon"></span>
                                            <span id="calorieChangeValue">0%</span>
                                        </span>
                                    </div>
                                    <p id="calorieComment" class="text-xs text-gray-400 mt-2">Loading data...</p>
                                </div>

                                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
                                    <div>
                                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Protein</span>
                                        <h4 id="proteinIntake" class="mt-2 text-2xl font-bold text-gray-800">Loading...</h4>
                                    </div>
                                    <div class="mt-4 flex items-center justify-between">
                                        <span id="proteinChange" class="flex items-center gap-1 rounded-full py-1 px-2 text-xs font-medium bg-gray-100 text-gray-600">
                                            <span id="proteinChangeIcon"></span>
                                            <span id="proteinChangeValue">0%</span>
                                        </span>
                                    </div>
                                    <p id="proteinComment" class="text-xs text-gray-400 mt-2">Loading data...</p>
                                </div>

                                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
                                    <div>
                                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Carbs</span>
                                        <h4 id="carbsIntake" class="mt-2 text-2xl font-bold text-gray-800">Loading...</h4>
                                    </div>
                                    <div class="mt-4 flex items-center justify-between">
                                        <span id="carbsChange" class="flex items-center gap-1 rounded-full py-1 px-2 text-xs font-medium bg-gray-100 text-gray-600">
                                            <span id="carbsChangeIcon"></span>
                                            <span id="carbsChangeValue">0%</span>
                                        </span>
                                    </div>
                                    <p id="carbsComment" class="text-xs text-gray-400 mt-2">Loading data...</p>
                                </div>
                            </div>

                            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-lg font-bold text-gray-800">Daily Actions</h3>
                                    <span class="text-xs font-medium px-2 py-1 bg-green-100 text-green-700 rounded">Today</span>
                                </div>

                                <div class="flex items-center justify-between p-4 bg-blue-50 rounded-xl border border-blue-100 mb-4">
                                    <div class="flex items-center gap-4">
                                        <div class="p-3 bg-white rounded-full text-blue-500 shadow-sm">
                                            <i class="fas fa-tint"></i>
                                        </div>
                                        <div>
                                            <h5 class="font-semibold text-gray-900">Hydration Check</h5>
                                            <p class="text-sm text-gray-500">Track your water intake (Target: <span id="waterGoal">2</span> L)</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div id="waterInputContainer" class="flex items-center gap-2">
                                            <input type="number" id="waterInput" placeholder="ml" class="w-20 px-2 py-1 text-sm border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                            <button onclick="logWaterIntake()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-sm transition">Log</button>
                                        </div>
                                        <div id="waterGoalReached" class="hidden text-green-600 font-bold text-sm">Goal Reached! 🎉</div>
                                        <button id="check-button" onclick="toggleCheck()" class="w-10 h-10 flex items-center justify-center rounded-full border-2 border-gray-300 text-gray-300 hover:border-green-500 hover:text-green-500 transition-all">
                                            <svg id="check-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div id="water-saved-message" class="text-center text-xs text-green-600 font-semibold opacity-0 transition-opacity duration-500 mb-4">
                                    ✅ Hydration logged!
                                </div>
                                <div id="saved-message" class="text-center text-xs text-green-600 font-semibold opacity-0 transition-opacity duration-500 mb-4">
                                    ✅ Great Job!
                                </div>

                                <div class="flex flex-col sm:flex-row items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
                                    <div class="flex items-center gap-4 w-full sm:w-auto mb-3 sm:mb-0">
                                        <div class="p-3 bg-white rounded-full text-orange-500 shadow-sm">
                                            <i class="fas fa-utensils"></i>
                                        </div>
                                        <div>
                                            <h5 class="font-semibold text-gray-900">Log Meal</h5>
                                            <p class="text-sm text-gray-500">What did you eat?</p>
                                        </div>
                                    </div>
                                    <div class="flex w-full sm:w-auto gap-2">
                                        <input type="text" id="foodInput" class="w-full sm:w-48 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" placeholder="e.g. 1 apple">
                                        <button onclick="fetchCalories()" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-md shadow-orange-200">
                                            Add
                                        </button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mt-6">
                                    <div class="text-center p-3 bg-yellow-50 rounded-lg">
                                        <span class="block text-xs text-yellow-600 font-bold uppercase">Cal</span>
                                        <span id="bmr1" class="text-lg font-bold text-gray-800"><?= htmlspecialchars($totalCalories) ?></span>
                                    </div>
                                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                                        <span class="block text-xs text-blue-600 font-bold uppercase">Prot</span>
                                        <span id="bmr2" class="text-lg font-bold text-gray-800"><?= htmlspecialchars($totalProtein) ?>g</span>
                                    </div>
                                    <div class="text-center p-3 bg-green-50 rounded-lg">
                                        <span class="block text-xs text-green-600 font-bold uppercase">Carb</span>
                                        <span id="bmr3" class="text-lg font-bold text-gray-800"><?= htmlspecialchars($totalCarbs) ?>g</span>
                                    </div>
                                    <div class="text-center p-3 bg-cyan-50 rounded-lg">
                                        <span class="block text-xs text-cyan-600 font-bold uppercase">Water</span>
                                        <span id="bmr4" class="text-lg font-bold text-gray-800">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-12 lg:col-span-4">
                            <div class="bg-white rounded-2xl border border-gray-200 shadow-lg h-[600px] flex flex-col">
                                <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-t-2xl">
                                    <h3 class="text-white font-bold flex items-center gap-2">
                                        <i class="fas fa-users"></i> Community Feed
                                    </h3>
                                </div>
                                
                                <div id="news-feed" class="flex-1 overflow-y-auto p-4 space-y-4 scrollbar-thin scrollbar-thumb-gray-200">
                                    <?php if (!empty($posts)): ?>
                                        <?php foreach ($posts as $post): ?>
                                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 hover:bg-white hover:shadow-md transition duration-200">
                                                <div class="flex items-start space-x-3">
                                                    <img src="<?php echo htmlspecialchars($post['user_avatar'] ?? 'photos/user.png'); ?>" 
                                                         alt="User" 
                                                         class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm">
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <p class="font-bold text-gray-900 text-sm"><?php echo htmlspecialchars($post['user_name']); ?></p>
                                                            <span class="text-xs text-gray-400">• <?php echo date("M d", strtotime($post['post_time'])); ?></span>
                                                        </div>
                                                        <p class="mt-1 text-sm text-gray-600 leading-relaxed">
                                                            <?php echo htmlspecialchars($post['post_content']); ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-center text-gray-400 py-10">
                                            <i class="fas fa-comment-slash text-4xl mb-2 opacity-30"></i>
                                            <p>No posts yet. Be the first!</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                    </div> </div>
            </main>
        </div>
    </div>

    <script>
        function toggleCheck() {
            const button = document.getElementById("check-button");
            const icon = document.getElementById("check-icon");
            const message = document.getElementById("saved-message");
            const isChecked = button.classList.contains("bg-green-500");

            if (!isChecked) {
                button.classList.add("bg-green-500", "border-green-500", "text-white");
                button.classList.remove("border-gray-300", "text-gray-300");
                message.classList.remove("opacity-0");
            } else {
                button.classList.remove("bg-green-500", "border-green-500", "text-white");
                button.classList.add("border-gray-300", "text-gray-300");
                message.classList.add("opacity-0");
            }
        }

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
                    headers: { 'X-Api-Key': apiKey }
                });
                const data = await response.json();

                if (data.items && data.items.length > 0) {
                    let item = data.items[0];
                    const mealData = {
                        meal_name: query,
                        calories: item.calories,
                        protein: item.protein_g,
                        carbs: item.carbohydrates_total_g
                    };

                    const addResponse = await fetch('', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(mealData)
                    });

                    const addResult = await addResponse.json();

                    if (addResult.success) {
                        alert("Meal added successfully!");
                        document.getElementById("foodInput").value = ""; // Clear input
                        updateTotals();
                        updateCalorieData();
                        updateProteinData();
                        updateCarbsData();
                    } else {
                        alert("Error adding meal: " + (addResult.error || "Unknown error"));
                    }
                } else {
                    alert("No nutritional data found for this food item.");
                }
            } catch (error) {
                console.error("Error fetching data:", error);
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
                    alert("Error logging water intake: " + (result.error || "Unknown error"));
                }
            } catch (error) {
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
                    document.getElementById("bmr4").innerHTML = `<strong>${data.totalWater}</strong> / ${data.waterGoal} L`;

                    const waterInputContainer = document.getElementById("waterInputContainer");
                    const waterGoalReached = document.getElementById("waterGoalReached");
                    
                    if (parseFloat(data.totalWater) >= parseFloat(data.waterGoal)) {
                        waterInputContainer.classList.add("hidden");
                        waterGoalReached.classList.remove("hidden");
                    } else {
                        waterInputContainer.classList.remove("hidden");
                        waterGoalReached.classList.add("hidden");
                    }
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
                }
            } catch (error) { console.error(error); }
        }

        async function updateProteinData() {
            try {
                const response = await fetch(window.location.href + (window.location.search ? '&' : '?') + 'fetch_protein_data=1');
                const data = await response.json();
                if (data.todayValue !== undefined) {
                    document.getElementById("proteinIntake").innerText = `${data.todayValue.toLocaleString()} g`;
                    updateMetricData("protein", data);
                }
            } catch (error) { console.error(error); }
        }

        async function updateCarbsData() {
            try {
                const response = await fetch(window.location.href + (window.location.search ? '&' : '?') + 'fetch_carbs_data=1');
                const data = await response.json();
                if (data.todayValue !== undefined) {
                    document.getElementById("carbsIntake").innerText = `${data.todayValue.toLocaleString()} g`;
                    updateMetricData("carbs", data);
                }
            } catch (error) { console.error(error); }
        }

        function updateMetricData(type, data) {
            const changeElement = document.getElementById(`${type}Change`);
            const changeIcon = document.getElementById(`${type}ChangeIcon`);
            const changeValue = document.getElementById(`${type}ChangeValue`);
            const commentElement = document.getElementById(`${type}Comment`);

            changeValue.innerText = `${data.percentageChange}%`;
            if(commentElement) commentElement.innerText = data.comment;

            let iconUp = `<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>`;
            let iconDown = `<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>`;
            let iconFlat = `<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>`;

            if (data.direction === "increase") {
                changeElement.className = "flex items-center gap-1 rounded-full py-1 px-2 text-xs font-medium bg-green-100 text-green-600";
                changeIcon.innerHTML = iconUp;
            } else if (data.direction === "decrease") {
                changeElement.className = "flex items-center gap-1 rounded-full py-1 px-2 text-xs font-medium bg-red-100 text-red-600";
                changeIcon.innerHTML = iconDown;
            } else {
                changeElement.className = "flex items-center gap-1 rounded-full py-1 px-2 text-xs font-medium bg-gray-100 text-gray-600";
                changeIcon.innerHTML = iconFlat;
            }
        }

        // Init
        document.addEventListener("DOMContentLoaded", function() {
            updateCalorieData();
            updateProteinData();
            updateCarbsData();
            updateTotals();
        });
    </script>
</body>
</html>