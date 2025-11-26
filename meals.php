<?php 
session_start();
include 'php_action/db_connect.php';

if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION["user_id"];

// Handle Add Meal
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $meal_name = $_POST['meal_name'];
    $calories = $_POST['calories'];
    $protein = $_POST['protein'];
    $carbs = $_POST['carbs'];

    $stmt = $connect->prepare("INSERT INTO meals (user_id, meal_name, calories, protein, carbs) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isddd", $user_id, $meal_name, $calories, $protein, $carbs);

    if ($stmt->execute()) {
        // Redirect to avoid form resubmission on refresh
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
        exit();
    } else {
        $error = "Error adding meal: " . $connect->error;
    }
    $stmt->close();
}

// Get current date and start of week
$currentDate = date('Y-m-d');
$startOfWeek = date('Y-m-d', strtotime('monday this week'));

// Default period is today
$period = isset($_GET['period']) ? $_GET['period'] : 'today';

if ($period === 'week') {
    // Fetch weekly intake
    $totalIntakeQuery = "SELECT SUM(calories) AS total_calories, SUM(protein) AS total_protein, SUM(carbs) AS total_carbs
                         FROM meals WHERE user_id = ? AND DATE(date_added) >= ?";
    $stmt = $connect->prepare($totalIntakeQuery);
    $stmt->bind_param("is", $user_id, $startOfWeek);
} else {
    // Fetch today's intake
    $totalIntakeQuery = "SELECT SUM(calories) AS total_calories, SUM(protein) AS total_protein, SUM(carbs) AS total_carbs
                         FROM meals WHERE user_id = ? AND DATE(date_added) = CURDATE()";
    $stmt = $connect->prepare($totalIntakeQuery);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$totalIntakeResult = $stmt->get_result()->fetch_assoc();
$totalCalories = $totalIntakeResult['total_calories'] ?: 0;
$totalProtein = $totalIntakeResult['total_protein'] ?: 0;
$totalCarbs = $totalIntakeResult['total_carbs'] ?: 0;
$stmt->close();

// Pagination Setup
$limit = 10; // Meals per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get Total Meals Count
if ($period === 'week') {
    $totalMealsQuery = "SELECT COUNT(*) as total FROM meals WHERE user_id = ? AND DATE(date_added) >= ?";
    $stmt = $connect->prepare($totalMealsQuery);
    $stmt->bind_param("is", $user_id, $startOfWeek);
} else {
    $totalMealsQuery = "SELECT COUNT(*) as total FROM meals WHERE user_id = ? AND DATE(date_added) = CURDATE()";
    $stmt = $connect->prepare($totalMealsQuery);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$totalMealsResult = $stmt->get_result()->fetch_assoc();
$totalMeals = $totalMealsResult['total'];
$totalPages = ceil($totalMeals / $limit);
$stmt->close();

// Fetch Meals
$meals = [];
if ($period === 'week') {
    $sql = "SELECT meal_name, calories, protein, carbs, date_added FROM meals WHERE user_id = ? AND DATE(date_added) >= ? ORDER BY date_added DESC LIMIT ? OFFSET ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("isii", $user_id, $startOfWeek, $limit, $offset);
} else {
    $sql = "SELECT meal_name, calories, protein, carbs, date_added FROM meals WHERE user_id = ? AND DATE(date_added) = CURDATE() ORDER BY date_added DESC LIMIT ? OFFSET ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("iii", $user_id, $limit, $offset);
}

$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $meals[] = $row;
}
$stmt->close();
$connect->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiteTrack - Meal Logs</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="custom/css/custom.css">
    <link rel="stylesheet" href="css/sidebar.css">

    <script>
        function changePeriod(period) {
            window.location.href = `?period=${period}`;
        }

        async function searchFood() {
            const foodInput = document.getElementById("food_search").value;
            const btn = document.getElementById("searchBtn");
            
            if (!foodInput) {
                alert("Please enter a food item!");
                return;
            }

            // Button Loading State
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
            btn.disabled = true;

            try {
                // NOTE: Keeping your API Key
                const response = await fetch(`https://api.calorieninjas.com/v1/nutrition?query=${foodInput}`, {
                    method: "GET",
                    headers: {
                        'X-Api-Key': 'FmEM2rbCs+c9j0rAbzaJRA==IVZqSzB9NOhvqjAs'
                    }
                });

                const data = await response.json();

                if (data.items.length === 0) {
                    alert("No data found for this food item!");
                    return;
                }

                const food = data.items[0];
                document.getElementById("meal_name").value = food.name;
                document.getElementById("calories").value = food.calories;
                document.getElementById("protein").value = food.protein_g;
                document.getElementById("carbs").value = food.carbohydrates_total_g;
            } catch (error) {
                console.error("Error fetching food data:", error);
                alert("Failed to retrieve food data!");
            } finally {
                btn.innerHTML = 'Search';
                btn.disabled = false;
            }
        }

        function toggleModal(show) {
            const modal = document.getElementById("mealModal");
            if(show) {
                modal.classList.remove("hidden");
                modal.classList.add("flex");
            } else {
                modal.classList.add("hidden");
                modal.classList.remove("flex");
            }
        }

        function filterTable() {
            let input = document.getElementById("table_search").value.toLowerCase();
            let rows = document.querySelectorAll("#mealTable tbody tr");

            rows.forEach(row => {
                let mealName = row.cells[0].textContent.toLowerCase();
                row.style.display = mealName.includes(input) ? "" : "none";
            });
        }
    </script>
</head>

<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen">

    <div class="flex h-screen overflow-hidden">
        
        <?php include 'includes/sidebar.php'; ?>

        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            <main class="w-full bg-gray-50 min-h-screen p-6">
                <div class="mx-auto max-w-7xl">

                    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Meal Logs 🥗</h1>
                            <p class="text-sm text-gray-500">Track your intake history and details.</p>
                        </div>
                        
                        <div class="mt-4 sm:mt-0 bg-white p-1 rounded-lg shadow-sm border border-gray-200 inline-flex">
                            <button onclick="changePeriod('today')" 
                                class="px-4 py-2 rounded-md text-sm font-medium transition <?= $period === 'today' ? 'bg-indigo-100 text-indigo-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' ?>">
                                Today
                            </button>
                            <button onclick="changePeriod('week')" 
                                class="px-4 py-2 rounded-md text-sm font-medium transition <?= $period === 'week' ? 'bg-indigo-100 text-indigo-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' ?>">
                                This Week
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
                        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between relative overflow-hidden group">
                            <div class="z-10">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Calories</p>
                                <h3 class="text-2xl font-extrabold text-gray-800 mt-1"><?= htmlspecialchars($totalCalories) ?></h3>
                                <span class="text-xs text-gray-500">kcal consumed</span>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-yellow-50 flex items-center justify-center text-yellow-500 text-xl z-10">
                                <i class="fas fa-fire"></i>
                            </div>
                            <div class="absolute right-0 top-0 h-full w-1 bg-yellow-400"></div>
                        </div>

                        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between relative overflow-hidden group">
                            <div class="z-10">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Protein</p>
                                <h3 class="text-2xl font-extrabold text-gray-800 mt-1"><?= htmlspecialchars($totalProtein) ?></h3>
                                <span class="text-xs text-gray-500">grams consumed</span>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 text-xl z-10">
                                <i class="fas fa-drumstick-bite"></i>
                            </div>
                            <div class="absolute right-0 top-0 h-full w-1 bg-blue-400"></div>
                        </div>

                        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between relative overflow-hidden group">
                            <div class="z-10">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Carbs</p>
                                <h3 class="text-2xl font-extrabold text-gray-800 mt-1"><?= htmlspecialchars($totalCarbs) ?></h3>
                                <span class="text-xs text-gray-500">grams consumed</span>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-500 text-xl z-10">
                                <i class="fas fa-bread-slice"></i>
                            </div>
                            <div class="absolute right-0 top-0 h-full w-1 bg-green-400"></div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 mb-6 justify-between">
                        <div class="relative w-full sm:w-96">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" id="table_search" onkeyup="filterTable()" 
                                class="pl-10 w-full p-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" 
                                placeholder="Search your meals...">
                        </div>
                        <button onclick="toggleModal(true)" 
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium shadow-md flex items-center justify-center gap-2 transition">
                            <i class="fas fa-plus"></i> Add Meal
                        </button>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table id="mealTable" class="w-full text-sm text-left text-gray-600">
                                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                                    <tr>
                                        <th class="px-6 py-4 font-semibold">Meal Name</th>
                                        <th class="px-6 py-4 font-semibold">Calories</th>
                                        <th class="px-6 py-4 font-semibold">Protein (g)</th>
                                        <th class="px-6 py-4 font-semibold">Carbs (g)</th>
                                        <th class="px-6 py-4 font-semibold">Date Added</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php if(count($meals) > 0): ?>
                                        <?php foreach ($meals as $meal): ?>
                                            <tr class="hover:bg-gray-50 transition duration-150">
                                                <td class="px-6 py-4 font-medium text-gray-900">
                                                    <?= htmlspecialchars($meal['meal_name']) ?>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                                        <?= htmlspecialchars($meal['calories']) ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-blue-600 font-medium">
                                                    <?= htmlspecialchars($meal['protein']) ?>
                                                </td>
                                                <td class="px-6 py-4 text-green-600 font-medium">
                                                    <?= htmlspecialchars($meal['carbs']) ?>
                                                </td>
                                                <td class="px-6 py-4 text-gray-400 text-xs">
                                                    <?= date('M d, h:i A', strtotime($meal['date_added'])) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                                <div class="flex flex-col items-center">
                                                    <i class="fas fa-utensils text-3xl mb-2 opacity-20"></i>
                                                    <p>No meals logged for this period.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                            <span class="text-xs text-gray-500">
                                Page <strong><?= $page ?></strong> of <strong><?= max(1, $totalPages) ?></strong>
                            </span>
                            <div class="inline-flex -space-x-px rounded-md shadow-sm">
                                <?php if ($page > 1): ?>
                                    <a href="?period=<?= $period ?>&page=<?= $page - 1 ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100">
                                        Previous
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <a href="?period=<?= $period ?>&page=<?= $page + 1 ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100">
                                        Next
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <div id="mealModal" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm justify-center items-center transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 transform transition-all scale-100 m-4">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Add New Meal</h3>
                <button onclick="toggleModal(false)" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="flex space-x-2 mb-6">
                <input type="text" id="food_search" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="Search food (e.g. '1 apple')">
                <button type="button" id="searchBtn" onclick="searchFood()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    Search
                </button>
            </div>

            <form id="mealForm" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Meal Name</label>
                    <input type="text" id="meal_name" name="meal_name" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500" readonly required>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Calories</label>
                        <input type="number" id="calories" name="calories" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm" readonly required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Protein (g)</label>
                        <input type="number" id="protein" name="protein" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm" readonly required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Carbs (g)</label>
                        <input type="number" id="carbs" name="carbs" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm" readonly required>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-2">
                    <button type="button" onclick="toggleModal(false)" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 shadow-sm transition">
                        Add Meal Log
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>