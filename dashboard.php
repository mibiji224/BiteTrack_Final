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
// (Existing API Handlers for Calories, Protein, Carbs, Totals, Meal/Water Insert... keep these as they were)
// ... [Preserve all your existing AJAX logic here] ...

// Fetch Calories
if (isset($_GET['fetch_calorie_data'])) {
    // ... (Keep existing code)
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
    // ... (Keep existing code)
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
    // ... (Keep existing code)
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
    // ... (Keep existing code)
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

    echo json_encode([
        'totalCalories' => $totalIntakeResult['total_calories'] ?: 0,
        'totalProtein' => $totalIntakeResult['total_protein'] ?: 0,
        'totalCarbs' => $totalIntakeResult['total_carbs'] ?: 0,
        'totalWater' => $waterIntakeResult['total_water'] ?: 0,
        'waterGoalGlasses' => 8
    ]);
    exit();
}

// Handle Meal Insertion
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['water_amount'])) {
    // ... (Keep existing code)
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

    if (empty($meal_name) || !is_numeric($calories)) {
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
    // ... (Keep existing code)
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

// ==================================================
// 2. FETCH POSTS WITH LIKES & COMMENTS (UPDATED)
// ==================================================
$sql_posts = "SELECT p.*,
    (SELECT COUNT(*) FROM post_likes WHERE post_id = p.post_id) as like_count,
    (SELECT COUNT(*) FROM post_comments WHERE post_id = p.post_id) as comment_count
    FROM posts p
    ORDER BY post_time DESC LIMIT 10";
$result_posts = $connect->query($sql_posts);

$posts = [];
if ($result_posts) {
    while ($row = $result_posts->fetch_assoc()) {
        $posts[] = $row;
    }
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
    <style>
        .loader {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: inline-block;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen" x-data="{ sidebarToggle: false }">

    <div class="flex h-screen overflow-hidden">
        
        <?php require_once 'includes/sidebar.php'; ?>

        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            
            <div :class="sidebarToggle ? 'block lg:hidden' : 'hidden'" class="fixed z-20 h-screen w-full bg-gray-900/50 hidden" @click="sidebarToggle = false"></div>

            <main class="w-full bg-gray-50 min-h-screen transition-all duration-200 ease-in-out">
                
                <div class="lg:hidden flex items-center justify-between bg-white p-4 border-b border-gray-200">
                    <span class="font-bold text-xl text-gray-800">Dashboard</span>
                    <button @click="sidebarToggle = !sidebarToggle" class="text-gray-600 focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>

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

                                <div class="flex flex-col sm:flex-row items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100 mb-2">
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
                                        <button onclick="openFoodModal()" class="w-full sm:w-auto bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-md shadow-orange-200 flex items-center justify-center gap-2">
                                            <i class="fas fa-plus"></i> Add Meal
                                        </button>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between p-4 bg-blue-50 rounded-xl border border-blue-100 mb-4">
                                    <div class="flex items-center gap-4">
                                        <div class="p-3 bg-white rounded-full text-blue-500 shadow-sm">
                                            <i class="fas fa-tint"></i>
                                        </div>
                                        <div>
                                            <h5 class="font-semibold text-gray-900">Hydration Check</h5>
                                            <p class="text-sm text-gray-500">Target: 8 Glasses</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div id="waterInputContainer" class="flex items-center gap-2">
                                            <button onclick="logWaterIntake()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition shadow-md shadow-blue-200 flex items-center gap-2">
                                                <i class="fas fa-plus"></i> Add Glass
                                            </button>
                                        </div>
                                        <div id="waterGoalReached" class="hidden text-green-600 font-bold text-sm bg-green-100 px-3 py-1 rounded-full">Goal Reached! 🎉</div>
                                    </div>
                                </div>

                                <div id="water-saved-message" class="text-center text-xs text-green-600 font-semibold opacity-0 transition-opacity duration-500 mb-4">
                                    ✅ Hydration logged!
                                </div>
                                <div id="saved-message" class="text-center text-xs text-green-600 font-semibold opacity-0 transition-opacity duration-500 mb-4">
                                    ✅ Meal Saved!
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
                                        <span class="block text-xs text-cyan-600 font-bold uppercase">Glasses</span>
                                        <span id="bmr4" class="text-lg font-bold text-gray-800">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-12 lg:col-span-4">
                            <div class="bg-white rounded-2xl border border-gray-200 shadow-lg h-[600px] flex flex-col">
                                
                                <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-t-2xl">
                                    <h3 class="text-black font-bold text-lg uppercase flex items-center gap-2">
                                        <i class="fas fa-clock"></i> RECENT POSTS
                                    </h3>
                                </div>
                                
                                <div id="news-feed" class="flex-1 overflow-y-auto p-4 space-y-4 scrollbar-thin scrollbar-thumb-gray-200">
                                    <?php if (!empty($posts)): ?>
                                        <?php foreach ($posts as $post): ?>
                                            <a href="sns.php?highlight=<?= $post['post_id'] ?>" class="block">
                                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 hover:bg-white hover:shadow-md transition duration-200 group">
                                                    <div class="flex items-start space-x-3">
                                                        <img src="<?php echo htmlspecialchars($post['user_avatar'] ?? 'photos/user.png'); ?>" 
                                                             alt="User" 
                                                             class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm">
                                                        <div class="flex-1">
                                                            <div class="flex items-center gap-2">
                                                                <p class="font-bold text-gray-900 text-sm group-hover:text-indigo-600 transition"><?php echo htmlspecialchars($post['user_name']); ?></p>
                                                                <span class="text-xs text-gray-400">• <?php echo date("M d", strtotime($post['post_time'])); ?></span>
                                                            </div>
                                                            <p class="mt-1 text-sm text-gray-600 leading-relaxed line-clamp-2">
                                                                <?php echo htmlspecialchars($post['post_content']); ?>
                                                            </p>
                                                            
                                                            <div class="flex gap-4 mt-3 text-xs text-gray-400">
                                                                <span class="flex items-center gap-1"><i class="fas fa-heart text-red-400"></i> <?= $post['like_count'] ?></span>
                                                                <span class="flex items-center gap-1"><i class="fas fa-comment text-blue-400"></i> <?= $post['comment_count'] ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
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

                    </div> 
                </div>
            </main>
        </div>
    </div>

    <div id="foodModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeFoodModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start w-full">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Log a Meal</h3>
                            <div class="mt-4">
                                <div class="relative">
                                    <input type="text" id="modalFoodSearch" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500" placeholder="Search for food (e.g. apple, burger)">
                                    <div id="searchLoader" class="absolute right-3 top-3 hidden loader"></div>
                                </div>
                                <div id="foodResults" class="mt-2 max-h-40 overflow-y-auto text-sm text-gray-600 border-t border-gray-100 hidden"></div>
                                <div id="selectedItemDetails" class="mt-4 hidden bg-orange-50 p-4 rounded-lg">
                                    <h4 id="selectedFoodName" class="font-bold text-orange-800"></h4>
                                    <div class="grid grid-cols-3 gap-2 mt-2 text-xs text-gray-600">
                                        <div>Cal: <span id="selCal">0</span></div>
                                        <div>Prot: <span id="selProt">0</span>g</div>
                                        <div>Carb: <span id="selCarb">0</span>g</div>
                                    </div>
                                    <div class="mt-3">
                                        <label class="block text-xs font-bold text-gray-700">Amount (grams)</label>
                                        <input type="number" id="servingSize" value="100" class="mt-1 w-24 px-2 py-1 border rounded text-sm">
                                        <p class="text-[10px] text-gray-400 mt-1">Macros will adjust automatically.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="confirmMeal()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Add</button>
                    <button type="button" onclick="closeFoodModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- Modal Logic ---
        let currentSelectedFood = null;
        let searchTimeout = null;

        function openFoodModal() {
            document.getElementById('foodModal').classList.remove('hidden');
            document.getElementById('modalFoodSearch').focus();
        }

        function closeFoodModal() {
            document.getElementById('foodModal').classList.add('hidden');
            document.getElementById('modalFoodSearch').value = '';
            document.getElementById('foodResults').classList.add('hidden');
            document.getElementById('selectedItemDetails').classList.add('hidden');
            currentSelectedFood = null;
        }

        // Live Search
        document.getElementById('modalFoodSearch').addEventListener('keyup', function() {
            clearTimeout(searchTimeout);
            const query = this.value;
            if(query.length < 2) return;

            document.getElementById('searchLoader').classList.remove('hidden');

            searchTimeout = setTimeout(async () => {
                const apiKey = "FmEM2rbCs+c9j0rAbzaJRA==IVZqSzB9NOhvqjAs"; 
                const url = `https://api.calorieninjas.com/v1/nutrition?query=${encodeURIComponent(query)}`;
                
                try {
                    const response = await fetch(url, { headers: { 'X-Api-Key': apiKey } });
                    const data = await response.json();
                    
                    const resultsDiv = document.getElementById('foodResults');
                    resultsDiv.innerHTML = '';
                    resultsDiv.classList.remove('hidden');
                    document.getElementById('searchLoader').classList.add('hidden');

                    if(data.items && data.items.length > 0) {
                        data.items.forEach(item => {
                            const div = document.createElement('div');
                            div.className = "p-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 flex justify-between items-center";
                            div.innerHTML = `<span>${item.name}</span> <span class="text-xs text-gray-400">${item.calories} cal / 100g</span>`;
                            div.onclick = () => selectFood(item);
                            resultsDiv.appendChild(div);
                        });
                    } else {
                        resultsDiv.innerHTML = '<div class="p-2 text-gray-400">No results found.</div>';
                    }
                } catch (e) {
                    console.error(e);
                    document.getElementById('searchLoader').classList.add('hidden');
                }
            }, 500);
        });

        function selectFood(item) {
            currentSelectedFood = item;
            document.getElementById('foodResults').classList.add('hidden');
            document.getElementById('selectedItemDetails').classList.remove('hidden');
            
            document.getElementById('selectedFoodName').innerText = item.name;
            document.getElementById('servingSize').value = item.serving_size_g;
            
            updateCalculatedMacros();
        }

        document.getElementById('servingSize').addEventListener('input', updateCalculatedMacros);

        function updateCalculatedMacros() {
            if(!currentSelectedFood) return;
            const grams = parseFloat(document.getElementById('servingSize').value) || 0;
            const ratio = grams / currentSelectedFood.serving_size_g;

            document.getElementById('selCal').innerText = (currentSelectedFood.calories * ratio).toFixed(1);
            document.getElementById('selProt').innerText = (currentSelectedFood.protein_g * ratio).toFixed(1);
            document.getElementById('selCarb').innerText = (currentSelectedFood.carbohydrates_total_g * ratio).toFixed(1);
        }

        async function confirmMeal() {
            if(!currentSelectedFood) {
                alert("Please select a food item first.");
                return;
            }

            const grams = parseFloat(document.getElementById('servingSize').value) || 0;
            const ratio = grams / currentSelectedFood.serving_size_g;

            const mealData = {
                meal_name: `${currentSelectedFood.name} (${grams}g)`,
                calories: (currentSelectedFood.calories * ratio).toFixed(1),
                protein: (currentSelectedFood.protein_g * ratio).toFixed(1),
                carbs: (currentSelectedFood.carbohydrates_total_g * ratio).toFixed(1)
            };

            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(mealData)
                });
                const result = await response.json();

                if (result.success) {
                    const message = document.getElementById("saved-message");
                    message.classList.remove("opacity-0");
                    setTimeout(() => message.classList.add("opacity-0"), 3000);
                    
                    closeFoodModal();
                    updateTotals();
                    updateCalorieData();
                    updateProteinData();
                    updateCarbsData();
                } else {
                    alert("Error adding meal: " + (result.error || "Unknown error"));
                }
            } catch (e) {
                alert("Connection error.");
            }
        }

        async function logWaterIntake() {
            const glassSize = 250; 
            const formData = new FormData();
            formData.append("water_amount", glassSize);

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
                    updateTotals();
                } else {
                    alert("Error logging water: " + (result.error || "Unknown error"));
                }
            } catch (error) {
                alert("Error logging water.");
            }
        }

        async function updateTotals() {
            try {
                const response = await fetch(window.location.href + (window.location.search ? '&' : '?') + 'fetch_totals=1');
                const data = await response.json();
                if (data.totalCalories !== undefined) {
                    document.getElementById("bmr1").innerHTML = `<strong>${Math.round(data.totalCalories)}</strong> kcal`;
                    document.getElementById("bmr2").innerHTML = `<strong>${Math.round(data.totalProtein)}</strong> g`;
                    document.getElementById("bmr3").innerHTML = `<strong>${Math.round(data.totalCarbs)}</strong> g`;
                    
                    const glassesConsumed = Math.floor(data.totalWater / 250);
                    const glassesGoal = data.waterGoalGlasses || 8; 

                    document.getElementById("bmr4").innerHTML = `<strong>${glassesConsumed}</strong> / ${glassesGoal}`;

                    const waterGoalReached = document.getElementById("waterGoalReached");
                    
                    if (glassesConsumed >= glassesGoal) {
                        waterGoalReached.classList.remove("hidden");
                    } else {
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
                    document.getElementById("calorieIntake").innerText = `${Math.round(data.todayValue).toLocaleString()} cal`;
                    updateMetricData("calorie", data);
                }
            } catch (error) { console.error(error); }
        }

        async function updateProteinData() {
            try {
                const response = await fetch(window.location.href + (window.location.search ? '&' : '?') + 'fetch_protein_data=1');
                const data = await response.json();
                if (data.todayValue !== undefined) {
                    document.getElementById("proteinIntake").innerText = `${Math.round(data.todayValue).toLocaleString()} g`;
                    updateMetricData("protein", data);
                }
            } catch (error) { console.error(error); }
        }

        async function updateCarbsData() {
            try {
                const response = await fetch(window.location.href + (window.location.search ? '&' : '?') + 'fetch_carbs_data=1');
                const data = await response.json();
                if (data.todayValue !== undefined) {
                    document.getElementById("carbsIntake").innerText = `${Math.round(data.todayValue).toLocaleString()} g`;
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