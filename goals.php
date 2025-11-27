<?php
include 'php_action/db_connect.php';
include 'php_action/get_profile.php';

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ==========================================
//  AJAX API: Fetch Goals & Data based on Client Date
// ==========================================
if (isset($_GET['fetch_goals_data'])) {
    $date = $_GET['date'] ?? date('Y-m-d');
    
    // 1. Fetch User Goals
    $sql_goal = "SELECT daily_calories AS calories, protein_goal AS protein, carbs_goal AS carbs 
                 FROM goals WHERE user_id = ? ORDER BY goal_id DESC LIMIT 1";
    $stmt = $connect->prepare($sql_goal);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $goal = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Default goals if not set
    $dailyGoal = [
        "calories" => $goal['calories'] ?? 2000,
        "protein" => $goal['protein'] ?? 150,
        "carbs" => $goal['carbs'] ?? 250
    ];

    // 2. Fetch Today's Intake (Using Client Date)
    $sql_intake = "SELECT SUM(calories) AS val_cal, SUM(protein) AS val_prot, SUM(carbs) AS val_carb 
                   FROM meals WHERE user_id = ? AND DATE(date_added) = ?";
    $stmt = $connect->prepare($sql_intake);
    $stmt->bind_param("is", $user_id, $date);
    $stmt->execute();
    $intake = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $dailyIntake = [
        "calories" => (int)($intake['val_cal'] ?? 0),
        "protein" => (int)($intake['val_prot'] ?? 0),
        "carbs" => (int)($intake['val_carb'] ?? 0)
    ];

    // 3. Fetch Weekly Intake (Last 7 Days from Client Date)
    $sql_weekly = "SELECT SUM(calories) AS val_cal, SUM(protein) AS val_prot, SUM(carbs) AS val_carb 
                   FROM meals WHERE user_id = ? AND DATE(date_added) BETWEEN DATE_SUB(?, INTERVAL 6 DAY) AND ?";
    $stmt = $connect->prepare($sql_weekly);
    $stmt->bind_param("iss", $user_id, $date, $date);
    $stmt->execute();
    $wIntake = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // 4. Fetch Chart Data (History Trends)
    $sql_chart = "SELECT DATE(date_added) as log_date, 
                         SUM(calories) as total_calories, 
                         SUM(protein) as total_protein, 
                         SUM(carbs) as total_carbs 
                  FROM meals 
                  WHERE user_id = ? AND DATE(date_added) BETWEEN DATE_SUB(?, INTERVAL 6 DAY) AND ?
                  GROUP BY DATE(date_added) 
                  ORDER BY DATE(date_added) ASC";
    $stmt = $connect->prepare($sql_chart);
    $stmt->bind_param("iss", $user_id, $date, $date);
    $stmt->execute();
    $result_chart = $stmt->get_result();

    $historyDates = [];
    $historyCal = [];
    $historyProt = [];
    $historyCarb = [];

    // Initialize map to ensure all 7 days show even if empty
    $chartDataMap = [];
    for ($i = 6; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("$date -$i days"));
        $displayDate = date('M d', strtotime($d));
        $historyDates[] = $displayDate;
        $chartDataMap[$d] = ['cal' => 0, 'prot' => 0, 'carb' => 0];
    }

    // Fill map with actual data
    while ($row = $result_chart->fetch_assoc()) {
        $d = $row['log_date'];
        if (isset($chartDataMap[$d])) {
            $chartDataMap[$d]['cal'] = (int)$row['total_calories'];
            $chartDataMap[$d]['prot'] = (int)$row['total_protein'];
            $chartDataMap[$d]['carb'] = (int)$row['total_carbs'];
        }
    }

    // Flatten map to arrays
    $historyCal = array_column($chartDataMap, 'cal');
    $historyProt = array_column($chartDataMap, 'prot');
    $historyCarb = array_column($chartDataMap, 'carb');

    echo json_encode([
        'goals' => $dailyGoal,
        'daily' => $dailyIntake,
        'weekly' => [
            'calories' => (int)($wIntake['val_cal'] ?? 0),
            'protein' => (int)($wIntake['val_prot'] ?? 0),
            'carbs' => (int)($wIntake['val_carb'] ?? 0),
        ],
        'charts' => [
            'dates' => $historyDates,
            'calories' => $historyCal,
            'protein' => $historyProt,
            'carbs' => $historyCarb
        ]
    ]);
    exit();
}

// ---------------------------------------------------------
// 1. FETCH WEIGHT HISTORY (Server Side is fine for general history)
// ---------------------------------------------------------
$weight_data = [];
$weight_labels = [];

$tableCheck = $connect->query("SHOW TABLES LIKE 'weight_logs'");
if ($tableCheck && $tableCheck->num_rows > 0) {
    $weight_sql = "SELECT weight, DATE_FORMAT(date_logged, '%b %d') as log_date 
                   FROM weight_logs WHERE user_id = ? ORDER BY date_logged ASC";
    $stmt = $connect->prepare($weight_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $weight_data[] = (float)$row['weight'];
        $weight_labels[] = $row['log_date'];
    }
    $stmt->close();
}

// Fallback if no history
if (empty($weight_data)) {
    $weight_data[] = (float)($user['weight'] ?? 0);
    $weight_labels[] = "Current";
}

$weight_data_json = json_encode($weight_data);
$weight_labels_json = json_encode($weight_labels);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BiteTrack - Your Goals</title>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script defer src="script.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <link rel="stylesheet" href="custom/css/custom.css">
    <link rel="stylesheet" href="css/button.css">
    <link rel="stylesheet" href="css/sidebar.css">

    <style>
        .progress-bar-fill { transition: width 1s ease-in-out; }
        .avatar-hover:hover { transform: scale(1.02); transition: transform 0.2s; }
        
        /* SCROLLABLE CHARTS */
        .scrolling-wrapper {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch; /* smooth scrolling on iOS */
            padding-bottom: 1rem;
            gap: 1rem;
        }
        
        /* Card sizing for scroll */
        .scrolling-card {
            flex: 0 0 auto;
            width: 85vw; /* Mobile: 85% of screen width */
        }
        
        @media (min-width: 640px) {
            .scrolling-card { width: 400px; } /* Desktop: Fixed width */
        }

        /* Visible Scrollbar Styles */
        .scrolling-wrapper::-webkit-scrollbar {
            height: 8px; /* Height of horizontal scrollbar */
        }
        
        .scrolling-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .scrolling-wrapper::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        .scrolling-wrapper::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen" x-data="{ sidebarToggle: false }">

    <div class="flex h-screen overflow-hidden">
        
        <?php require_once 'includes/sidebar.php'; ?>

        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            
            <div :class="sidebarToggle ? 'block lg:hidden' : 'hidden'" class="fixed z-20 h-screen w-full bg-gray-900/50 hidden" @click="sidebarToggle = false"></div>

            <main class="w-full bg-gray-50 min-h-screen transition-all duration-200 ease-in-out p-6">
                
                <div class="lg:hidden flex justify-between items-center mb-6">
                    <h1 class="text-xl font-bold text-gray-800">Goals & Profile</h1>
                    <button @click="sidebarToggle = !sidebarToggle" class="text-gray-600">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>

                <div class="mx-auto max-w-7xl">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800 hidden lg:block">Your Goals & Profile 🎯</h1>
                            <p class="text-sm text-gray-500 hidden lg:block">Manage your targets and track your progress.</p>
                        </div>
                        <div class="mt-4 sm:mt-0 gap-2 flex">
                            <button onclick="toggleModal('weightModal', true)" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-medium shadow-sm transition flex items-center gap-2">
                                <i class="fas fa-weight"></i> Log Weight
                            </button>
                            <button onclick="toggleModal('goalModal', true)" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium shadow-md transition flex items-center gap-2">
                                <i class="fas fa-bullseye"></i> Update Goals
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6">

                        <div class="col-span-12 lg:col-span-4 space-y-6">
                            
                            <div class="bg-white rounded-2xl border border-gray-100 shadow-lg overflow-hidden relative">
                                <div class="h-32 bg-gradient-to-r from-blue-500 to-indigo-600"></div>
                                <div class="px-6 pb-6 relative">
                                    <div class="relative -mt-12 mb-4 flex justify-between items-end">
                                        <div class="relative group">
                                            <img id="profileAvatar" 
                                                 src="<?php echo htmlspecialchars($user['profile_avatar'] ?? 'photos/user.png'); ?>" 
                                                 alt="Profile" 
                                                 class="w-24 h-24 rounded-full border-4 border-white shadow-md object-cover avatar-hover cursor-pointer bg-white"
                                                 onclick="toggleModal('editModal', true)">
                                            <button onclick="toggleModal('editModal', true)" class="absolute bottom-0 right-0 bg-white p-1.5 rounded-full text-gray-600 shadow-sm border hover:text-blue-600 transition">
                                                <i class="fas fa-camera text-xs"></i>
                                            </button>
                                        </div>
                                        <button onclick="toggleModal('editModal', true)" class="text-sm text-blue-600 font-medium hover:underline mb-2">
                                            Edit Profile
                                        </button>
                                    </div>

                                    <div>
                                        <h2 class="text-xl font-bold text-gray-800">
                                            <span id="firstNameDisplay"><?= htmlspecialchars($user['first_name']) ?></span>
                                            <span id="lastNameDisplay"><?= htmlspecialchars($user['last_name']) ?></span>
                                        </h2>
                                        <p class="text-gray-500 text-sm mb-6">Track your fitness journey.</p>

                                        <div class="grid grid-cols-3 gap-4 border-t border-gray-100 pt-4">
                                            <div class="text-center">
                                                <span class="block text-xs text-gray-400 uppercase tracking-wide">Age</span>
                                                <span class="text-lg font-bold text-gray-800" id="ageDisplay"><?= htmlspecialchars($user['age']) ?></span>
                                            </div>
                                            <div class="text-center border-l border-gray-100">
                                                <span class="block text-xs text-gray-400 uppercase tracking-wide">Height</span>
                                                <span class="text-lg font-bold text-gray-800">
                                                    <span id="heightDisplay"><?= htmlspecialchars($user['height']) ?></span> cm
                                                </span>
                                            </div>
                                            <div class="text-center border-l border-gray-100">
                                                <span class="block text-xs text-gray-400 uppercase tracking-wide">Weight</span>
                                                <span class="text-lg font-bold text-gray-800">
                                                    <span id="weightDisplay"><?= htmlspecialchars($user['weight']) ?></span> kg
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-2xl border border-gray-100 shadow-md p-5">
                                <h3 class="text-gray-800 font-bold mb-4 flex items-center gap-2">
                                    <i class="fas fa-weight-scale text-indigo-500"></i> Weight Journey
                                </h3>
                                <div class="relative h-40 w-full">
                                    <div id="weight-chart" class="w-full h-full"></div>
                                </div>
                                <div class="mt-4 text-center">
                                    <p class="text-xs text-gray-500">Current Weight</p>
                                    <p class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($user['weight']) ?> <span class="text-sm font-normal text-gray-500">kg</span></p>
                                </div>
                            </div>

                            <div class="bg-white rounded-2xl border border-gray-100 shadow-md p-5">
                                <h3 class="text-gray-800 font-bold mb-4 flex items-center gap-2">
                                    <i class="fas fa-calendar-week text-indigo-500"></i> Weekly Summary
                                </h3>
                                <div class="space-y-4">
                                    <div class="p-3 bg-yellow-50 rounded-xl">
                                        <div class="flex justify-between items-center mb-1">
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 rounded-full bg-yellow-200 flex items-center justify-center text-yellow-700 text-xs"><i class="fas fa-fire"></i></div>
                                                <span class="text-sm font-medium text-gray-700">Calories</span>
                                            </div>
                                            <span id="wkCalText" class="text-xs font-bold text-gray-800">...</span>
                                        </div>
                                        <div class="w-full bg-yellow-200 rounded-full h-1.5">
                                            <div id="wkCalBar" class="bg-yellow-500 h-1.5 rounded-full" style="width: 0%"></div>
                                        </div>
                                    </div>
                                    <div class="p-3 bg-blue-50 rounded-xl">
                                        <div class="flex justify-between items-center mb-1">
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 text-xs"><i class="fas fa-drumstick-bite"></i></div>
                                                <span class="text-sm font-medium text-gray-700">Protein</span>
                                            </div>
                                            <span id="wkProtText" class="text-xs font-bold text-gray-800">...</span>
                                        </div>
                                        <div class="w-full bg-blue-200 rounded-full h-1.5">
                                            <div id="wkProtBar" class="bg-blue-500 h-1.5 rounded-full" style="width: 0%"></div>
                                        </div>
                                    </div>
                                    <div class="p-3 bg-green-50 rounded-xl">
                                        <div class="flex justify-between items-center mb-1">
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 rounded-full bg-green-200 flex items-center justify-center text-green-700 text-xs"><i class="fas fa-bread-slice"></i></div>
                                                <span class="text-sm font-medium text-gray-700">Carbs</span>
                                            </div>
                                            <span id="wkCarbText" class="text-xs font-bold text-gray-800">...</span>
                                        </div>
                                        <div class="w-full bg-green-200 rounded-full h-1.5">
                                            <div id="wkCarbBar" class="bg-green-500 h-1.5 rounded-full" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div> 

                        <div class="col-span-12 lg:col-span-8 space-y-6">
                            
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center text-center relative overflow-hidden group hover:shadow-md transition">
                                    <div class="absolute top-0 right-0 w-16 h-16 bg-yellow-100 rounded-bl-full -mr-8 -mt-8 z-0 transition group-hover:bg-yellow-200"></div>
                                    <div class="z-10">
                                        <div class="text-yellow-500 mb-2 text-2xl"><i class="fas fa-fire-alt"></i></div>
                                        <p class="text-xs text-gray-400 uppercase font-bold tracking-wider">Daily Calories</p>
                                        <h3 id="goalCalDisplay" class="text-2xl font-extrabold text-gray-800 my-1">...</h3>
                                        <span class="text-xs text-gray-500">kcal target</span>
                                    </div>
                                </div>
                                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center text-center relative overflow-hidden group hover:shadow-md transition">
                                    <div class="absolute top-0 right-0 w-16 h-16 bg-blue-100 rounded-bl-full -mr-8 -mt-8 z-0 transition group-hover:bg-blue-200"></div>
                                    <div class="z-10">
                                        <div class="text-blue-500 mb-2 text-2xl"><i class="fas fa-dumbbell"></i></div>
                                        <p class="text-xs text-gray-400 uppercase font-bold tracking-wider">Daily Protein</p>
                                        <h3 id="goalProtDisplay" class="text-2xl font-extrabold text-gray-800 my-1">...</h3>
                                        <span class="text-xs text-gray-500">grams target</span>
                                    </div>
                                </div>
                                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center text-center relative overflow-hidden group hover:shadow-md transition">
                                    <div class="absolute top-0 right-0 w-16 h-16 bg-green-100 rounded-bl-full -mr-8 -mt-8 z-0 transition group-hover:bg-green-200"></div>
                                    <div class="z-10">
                                        <div class="text-green-500 mb-2 text-2xl"><i class="fas fa-wheat"></i></div>
                                        <p class="text-xs text-gray-400 uppercase font-bold tracking-wider">Daily Carbs</p>
                                        <h3 id="goalCarbDisplay" class="text-2xl font-extrabold text-gray-800 my-1">...</h3>
                                        <span class="text-xs text-gray-500">grams target</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                                <h3 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2">Daily Progress</h3>
                                
                                <div class="space-y-4">
                                    <div class="p-3 bg-yellow-50 rounded-xl">
                                        <div class="flex justify-between items-center mb-1">
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 rounded-full bg-yellow-200 flex items-center justify-center text-yellow-700 text-xs"><i class="fas fa-fire"></i></div>
                                                <span class="text-sm font-medium text-gray-700">Calories</span>
                                            </div>
                                            <div>
                                                <span id="dailyCalVal" class="text-xs font-bold text-gray-800">0</span>
                                                <span id="dailyCalTarget" class="text-xs text-gray-500">/ 0 kcal</span>
                                            </div>
                                        </div>
                                        <div class="w-full bg-yellow-200 rounded-full h-1.5">
                                            <div id="dailyCalBar" class="bg-yellow-500 h-1.5 rounded-full progress-bar-fill" style="width: 0%"></div>
                                        </div>
                                    </div>

                                    <div class="p-3 bg-blue-50 rounded-xl">
                                        <div class="flex justify-between items-center mb-1">
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 text-xs"><i class="fas fa-dumbbell"></i></div>
                                                <span class="text-sm font-medium text-gray-700">Protein</span>
                                            </div>
                                            <div>
                                                <span id="dailyProtVal" class="text-xs font-bold text-gray-800">0</span>
                                                <span id="dailyProtTarget" class="text-xs text-gray-500">/ 0 g</span>
                                            </div>
                                        </div>
                                        <div class="w-full bg-blue-200 rounded-full h-1.5">
                                            <div id="dailyProtBar" class="bg-blue-500 h-1.5 rounded-full progress-bar-fill" style="width: 0%"></div>
                                        </div>
                                    </div>

                                    <div class="p-3 bg-green-50 rounded-xl">
                                        <div class="flex justify-between items-center mb-1">
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 rounded-full bg-green-200 flex items-center justify-center text-green-700 text-xs"><i class="fas fa-bread-slice"></i></div>
                                                <span class="text-sm font-medium text-gray-700">Carbs</span>
                                            </div>
                                            <div>
                                                <span id="dailyCarbVal" class="text-xs font-bold text-gray-800">0</span>
                                                <span id="dailyCarbTarget" class="text-xs text-gray-500">/ 0 g</span>
                                            </div>
                                        </div>
                                        <div class="w-full bg-green-200 rounded-full h-1.5">
                                            <div id="dailyCarbBar" class="bg-green-500 h-1.5 rounded-full progress-bar-fill" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-bold text-gray-800 mb-4 ml-1">History Trends (Last 7 Days)</h3>
                                <div class="scrolling-wrapper">
                                    <div class="scrolling-card bg-white rounded-2xl border border-gray-200 shadow-lg p-4">
                                        <h4 class="text-sm font-semibold text-gray-600 mb-2">Calories</h4>
                                        <div id="calorie-chart" class="w-full h-48"></div>
                                    </div>
                                    <div class="scrolling-card bg-white rounded-2xl border border-gray-200 shadow-lg p-4">
                                        <h4 class="text-sm font-semibold text-gray-600 mb-2">Protein</h4>
                                        <div id="protein-chart" class="w-full h-48"></div>
                                    </div>
                                    <div class="scrolling-card bg-white rounded-2xl border border-gray-200 shadow-lg p-4">
                                        <h4 class="text-sm font-semibold text-gray-600 mb-2">Carbs</h4>
                                        <div id="carbs-chart" class="w-full h-48"></div>
                                    </div>
                                </div>
                            </div>

                        </div> 
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div id="goalModal" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm flex justify-center items-center transition-opacity">
        <div class="bg-white p-8 rounded-2xl w-full max-w-md shadow-2xl">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Set Daily Goals</h2>
                <button onclick="toggleModal('goalModal', false)" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            
            <form action="php_action/save_goal.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Calorie Target (kcal)</label>
                    <input type="number" id="inputGoalCal" name="calories" required class="w-full px-4 py-2 border rounded-lg focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Protein Target (g)</label>
                    <input type="number" id="inputGoalProt" name="protein" required class="w-full px-4 py-2 border rounded-lg focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Carbs Target (g)</label>
                    <input type="number" id="inputGoalCarb" name="carbs" required class="w-full px-4 py-2 border rounded-lg focus:ring-indigo-500">
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="toggleModal('goalModal', false)" class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-md">Save Goals</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm flex justify-center items-center transition-opacity">
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl overflow-y-auto max-h-[90vh]">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Edit Profile</h3>
                <button onclick="toggleModal('editModal', false)" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            <form id="editProfileForm" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">First Name</label>
                        <input type="text" id="editFirstName" name="first_name" class="mt-1 w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Last Name</label>
                        <input type="text" id="editLastName" name="last_name" class="mt-1 w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Age</label>
                        <input type="number" id="editAge" name="age" class="mt-1 w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Height (cm)</label>
                        <input type="number" step="0.01" id="editHeight" name="height" class="mt-1 w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Weight (kg)</label>
                        <input type="number" step="0.01" id="editWeight" name="weight" class="mt-1 w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">Save Details</button>
                </div>
            </form>
            <hr class="my-6 border-gray-200">
            <h4 class="text-lg font-semibold text-gray-800 mb-3">Update Photo</h4>
            <form id="updateProfileImageForm" class="flex items-center gap-4">
                <div class="shrink-0">
                    <img id="editAvatarPreview" src="<?php echo htmlspecialchars($user['profile_avatar'] ?? 'photos/user.png'); ?>" alt="Preview" class="w-16 h-16 rounded-full object-cover border">
                </div>
                <div class="flex-1">
                    <input id="editAvatar" name="profile_avatar" type="file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                    <p class="mt-1 text-xs text-gray-500">JPG, GIF or PNG. Max 2MB.</p>
                </div>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm h-fit">Upload</button>
            </form>
        </div>
    </div>

    <div id="weightModal" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm flex justify-center items-center transition-opacity">
        <div class="bg-white p-8 rounded-2xl w-full max-w-xs shadow-2xl text-center">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Log Weight</h2>
            <form id="logWeightForm" class="space-y-4">
                <div>
                    <input type="number" step="0.01" id="logWeightInput" placeholder="kg" class="text-center text-3xl w-full border-none border-b-2 border-indigo-200 focus:border-indigo-600 focus:ring-0 placeholder-gray-300" required>
                </div>
                <div class="flex justify-center gap-2 mt-4">
                    <button type="button" onclick="toggleModal('weightModal', false)" class="px-4 py-2 bg-gray-100 rounded-lg text-sm">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // --- 1. Client Date Function ---
        function getClientDate() {
            const now = new Date();
            const offset = now.getTimezoneOffset();
            const localDate = new Date(now.getTime() - (offset * 60 * 1000));
            return localDate.toISOString().split('T')[0];
        }

        // --- 2. Fetch Data (Timezone Synced) ---
        async function fetchGoalsData() {
            const date = getClientDate();
            try {
                // Call self with query param
                const response = await fetch(`goals.php?fetch_goals_data=1&date=${date}`);
                const data = await response.json();
                
                updateUI(data);
                renderHistoryCharts(data.charts);
            } catch (e) {
                console.error("Fetch error:", e);
            }
        }

        function updateUI(data) {
            // Update Goals Display
            document.getElementById('goalCalDisplay').innerText = data.goals.calories;
            document.getElementById('goalProtDisplay').innerText = data.goals.protein;
            document.getElementById('goalCarbDisplay').innerText = data.goals.carbs;

            // Prefill Goal Modal Inputs
            document.getElementById('inputGoalCal').value = data.goals.calories;
            document.getElementById('inputGoalProt').value = data.goals.protein;
            document.getElementById('inputGoalCarb').value = data.goals.carbs;

            // Update Daily Progress Bars
            document.getElementById('dailyCalVal').innerText = data.daily.calories;
            document.getElementById('dailyCalTarget').innerText = `/ ${data.goals.calories} kcal`;
            updateBar('dailyCalBar', data.daily.calories, data.goals.calories);

            document.getElementById('dailyProtVal').innerText = data.daily.protein;
            document.getElementById('dailyProtTarget').innerText = `/ ${data.goals.protein} g`;
            updateBar('dailyProtBar', data.daily.protein, data.goals.protein);

            document.getElementById('dailyCarbVal').innerText = data.daily.carbs;
            document.getElementById('dailyCarbTarget').innerText = `/ ${data.goals.carbs} g`;
            updateBar('dailyCarbBar', data.daily.carbs, data.goals.carbs);

            // Update Weekly Summary
            const wkCalGoal = data.goals.calories * 7;
            const wkProtGoal = data.goals.protein * 7;
            const wkCarbGoal = data.goals.carbs * 7;

            document.getElementById('wkCalText').innerText = `${data.weekly.calories} / ${wkCalGoal}`;
            updateBar('wkCalBar', data.weekly.calories, wkCalGoal);

            document.getElementById('wkProtText').innerText = `${data.weekly.protein}g / ${wkProtGoal}g`;
            updateBar('wkProtBar', data.weekly.protein, wkProtGoal);

            document.getElementById('wkCarbText').innerText = `${data.weekly.carbs}g / ${wkCarbGoal}g`;
            updateBar('wkCarbBar', data.weekly.carbs, wkCarbGoal);
        }

        function updateBar(elementId, val, max) {
            const pct = max > 0 ? Math.min(100, (val / max) * 100) : 0;
            document.getElementById(elementId).style.width = pct + "%";
        }

        // --- 3. Charts ---
        function renderHistoryCharts(chartData) {
            createChart("#calorie-chart", chartData.calories, chartData.dates, "#F59E0B", "Calories", 180).render();
            createChart("#protein-chart", chartData.protein, chartData.dates, "#3B82F6", "Protein", 180).render();
            createChart("#carbs-chart", chartData.carbs, chartData.dates, "#10B981", "Carbs", 180).render();
        }

        function createChart(containerId, data, dates, color, name, height) {
            document.querySelector(containerId).innerHTML = ""; // Clear existing
            return new ApexCharts(document.querySelector(containerId), {
                series: [{ name: name, data: data }],
                chart: { type: "area", height: height, toolbar: { show: false }, zoom: { enabled: false } },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                fill: { type: "gradient", gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.3 } },
                colors: [color],
                xaxis: { categories: dates, labels: { show: true, rotate: -45, style: {fontSize: '10px'} }, axisBorder: { show: false }, axisTicks: { show: false } },
                yaxis: { show: false },
                grid: { show: false },
                tooltip: { theme: "light" }
            });
        }

        // --- Weight Chart (Server Side Data) ---
        var weightHistory = <?= $weight_data_json ?>;
        var weightDates = <?= $weight_labels_json ?>;

        new ApexCharts(document.querySelector("#weight-chart"), {
            series: [{ name: "Weight", data: weightHistory }],
            chart: { type: "area", height: 160, toolbar: { show: false } },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            fill: { type: "gradient", gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.3 } },
            colors: ["#6366f1"],
            xaxis: { categories: weightDates, labels: { show: false }, axisBorder: { show: false }, axisTicks: { show: false } },
            yaxis: { show: false },
            grid: { show: false, padding: { left: 0, right: 0 } },
            tooltip: { theme: "light", y: { formatter: function (val) { return val + " kg" } } }
        }).render();

        // --- Modal & Form Logic (Preserved) ---
        function toggleModal(modalId, show) {
            const modal = document.getElementById(modalId);
            if (show) {
                modal.classList.remove("hidden");
                if(modalId === 'editModal') prefillEditModal();
            } else {
                modal.classList.add("hidden");
            }
        }

        function prefillEditModal() {
            $("#editFirstName").val($("#firstNameDisplay").text());
            $("#editLastName").val($("#lastNameDisplay").text());
            $("#editAge").val($("#ageDisplay").text());
            $("#editHeight").val($("#heightDisplay").text().replace(" cm", ""));
            $("#editWeight").val($("#weightDisplay").text().replace(" kg", ""));
        }

        // --- AJAX Forms ---
        $("#editProfileForm").on("submit", function(e) {
            e.preventDefault();
            $.ajax({
                url: 'php_action/update_profile_details.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({ icon: 'success', title: 'Updated!', text: 'Profile details saved.', timer: 1500, showConfirmButton: false });
                        $("#firstNameDisplay").text(response.updatedData.first_name);
                        $("#lastNameDisplay").text(response.updatedData.last_name);
                        $("#ageDisplay").text(response.updatedData.age);
                        $("#heightDisplay").text(response.updatedData.height);
                        $("#weightDisplay").text(response.updatedData.weight);
                        toggleModal('editModal', false);
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: response.messages });
                    }
                }
            });
        });

        $("#updateProfileImageForm").on("submit", function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: 'php_action/update_avatar.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({ icon: 'success', title: 'Nice pic!', text: 'Avatar updated successfully.', timer: 1500, showConfirmButton: false });
                        $("#profileAvatar").attr("src", response.updatedData.profile_avatar);
                        toggleModal('editModal', false);
                    } else {
                        Swal.fire({ icon: 'error', title: 'Upload Failed', text: response.messages });
                    }
                }
            });
        });

        $("#logWeightForm").on("submit", function(e) {
            e.preventDefault();
            let newWeight = $("#logWeightInput").val();
            let currentData = {
                first_name: $("#firstNameDisplay").text(),
                last_name: $("#lastNameDisplay").text(),
                age: $("#ageDisplay").text(),
                height: $("#heightDisplay").text().trim().replace(' cm', ''),
                weight: newWeight
            };

            $.ajax({
                url: 'php_action/update_profile_details.php',
                type: 'POST',
                data: currentData,
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        Swal.fire({ icon: 'success', title: 'Logged!', text: 'Current weight updated.', timer: 1500, showConfirmButton: false });
                        $("#weightDisplay").text(newWeight);
                        toggleModal('weightModal', false);
                        setTimeout(() => location.reload(), 1000); 
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: response.messages });
                    }
                }
            });
        });

        $("#editAvatar").on('change', function(){
            const file = this.files[0];
            if (file){
                let reader = new FileReader();
                reader.onload = function(event){ $('#editAvatarPreview').attr('src', event.target.result); }
                reader.readAsDataURL(file);
            }
        });

        // INIT
        document.addEventListener("DOMContentLoaded", fetchGoalsData);
    </script>
</body>
</html>