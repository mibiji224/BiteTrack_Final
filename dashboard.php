<?php session_start();
$host = "localhost";
$username = "root";
$password = "";
$database = "nutrition_tracker";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION["user_id"];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $meal_name = $_POST['meal_name'];
    $calories = $_POST['calories'];
    $protein = $_POST['protein'];
    $carbs = $_POST['carbs'];

    $stmt = $conn->prepare("INSERT INTO meals (user_id, meal_name, calories, protein, carbs) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isddd", $user_id, $meal_name, $calories, $protein, $carbs);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Error adding meal: " . $conn->error]);
    }

    $stmt->close();
}

// Get current date and the start of the current week (Monday)
$currentDate = date('Y-m-d');
$startOfWeek = date('Y-m-d', strtotime('monday this week'));

// Default period is today
$period = isset($_GET['period']) ? $_GET['period'] : 'today';

// Fetch today's intake
$totalIntakeQuery = "SELECT SUM(calories) AS total_calories, SUM(protein) AS total_protein, SUM(carbs) AS total_carbs
                         FROM meals WHERE user_id = ? AND DATE(date_added) = CURDATE()";
$stmt = $conn->prepare($totalIntakeQuery);
$stmt->bind_param("i", $user_id);


$stmt->execute();
$totalIntakeResult = $stmt->get_result()->fetch_assoc();
$totalCalories = $totalIntakeResult['total_calories'] ?: 0;
$totalProtein = $totalIntakeResult['total_protein'] ?: 0;
$totalCarbs = $totalIntakeResult['total_carbs'] ?: 0;
$stmt->close();
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
        document.addEventListener("DOMContentLoaded", function () {
            const toggleBtn = document.getElementById("dashboardToggle");
            const dropdownMenu = document.getElementById("dashboardDropdown");

            toggleBtn.addEventListener("click", function (event) {
                event.preventDefault();
                dropdownMenu.classList.toggle("max-h-0");
                dropdownMenu.classList.toggle("opacity-0");
                dropdownMenu.classList.toggle("max-h-[200px]");
                dropdownMenu.classList.toggle("opacity-100");
            });
        });
        document.addEventListener("DOMContentLoaded", function () {
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

            const apiKey = "FmEM2rbCs+c9j0rAbzaJRA==IVZqSzB9NOhvqjAs"; // Replace with your CalorieNinjas API key
            const url = `https://api.calorieninjas.com/v1/nutrition?query=${encodeURIComponent(query)}`;

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Api-Key': apiKey
                    }
                });

                const data = await response.json();

                if (data.items && data.items.length > 0) {
                    let item = data.items[0]; // Taking the first result

                    document.getElementById("bmr1").innerHTML = `<strong>${item.calories}</strong> kcal`;
                    document.getElementById("bmr2").innerHTML = `<strong>${item.protein_g}</strong> g`;
                    document.getElementById("bmr3").innerHTML = `<strong>${item.carbohydrates_total_g}</strong> g`;
                } else {
                    document.getElementById("bmr1").innerHTML = "No data found";
                    document.getElementById("bmr2").innerHTML = "No data found";
                    document.getElementById("bmr3").innerHTML = "No data found";
                }
            } catch (error) {
                console.error("Error fetching data:", error);
                document.getElementById("bmr1").innerHTML = "Error fetching data";
                document.getElementById("bmr2").innerHTML = "Error fetching data";
                document.getElementById("bmr3").innerHTML = "Error fetching data";
            }
        }
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
                            <div class="col-span-12 space-y-6 xl:col-span-7">
                                <!-- Metric Group One -->
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6">
                                    <!-- Metric Item Start -->
                                    <div
                                        class="w-full max-w-[20rem] md:max-w-[24rem] lg:max-w-[28rem] rounded-2xl border border-gray-200 bg-white p-4 md:p-6 shadow-md mx-auto flex flex-col space-y-3">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <span class="text-sm text-gray-500">Calorie Intake</span>
                                                <h4 class="mt-1 text-lg font-bold text-gray-800">3,782 cal</h4>
                                            </div>
                                            <span
                                                class="flex items-center gap-1 rounded-full bg-red-100 py-0.5 px-2 text-sm font-medium text-red-600">
                                                <svg class="fill-current" width="12" height="12" viewBox="0 0 12 12"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M5.31462 10.3761C5.45194 10.5293 5.65136 10.6257 5.87329 10.6257C5.8736 10.6257 5.8739 10.6257 5.87421 10.6257C6.0663 10.6259 6.25845 10.5527 6.40505 10.4062L9.40514 7.4082C9.69814 7.11541 9.69831 6.64054 9.40552 6.34754C9.11273 6.05454 8.63785 6.05438 8.34486 6.34717L6.62329 8.06753V1.875C6.62329 1.46079 6.28751 1.125 5.87329 1.125C5.45908 1.125 5.12329 1.46079 5.12329 1.875V8.06422L3.40516 6.34719C3.11218 6.05439 2.6373 6.05454 2.3445 6.34752C2.0517 6.64051 2.05185 7.11538 2.34484 7.40818L5.31462 10.3761Z">
                                                    </path>
                                                </svg>
                                                11.01%
                                            </span>
                                        </div>

                                        <!-- Comment Section -->
                                        <div class="text-sm text-gray-500 text-center">
                                            Lower than last week.
                                        </div>
                                    </div>


                                    <!-- Metric Item Start -->
                                    <div
                                        class="rounded-2xl border border-gray-200 bg-white p-4 shadow-md md:p-5 flex flex-col space-y-3">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <span class="text-sm text-gray-500">Current Weight</span>
                                                <h4 class="mt-1 text-lg font-bold text-gray-800">52 kg</h4>
                                            </div>
                                            <span
                                                class="flex items-center gap-1 rounded-full bg-red-100 py-0.5 px-2 text-sm font-medium text-red-600">
                                                <svg class="fill-current" width="12" height="12" viewBox="0 0 12 12"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M5.31462 10.3761C5.45194 10.5293 5.65136 10.6257 5.87329 10.6257C5.8736 10.6257 5.8739 10.6257 5.87421 10.6257C6.0663 10.6259 6.25845 10.5527 6.40505 10.4062L9.40514 7.4082C9.69814 7.11541 9.69831 6.64054 9.40552 6.34754C9.11273 6.05454 8.63785 6.05438 8.34486 6.34717L6.62329 8.06753V1.875C6.62329 1.46079 6.28751 1.125 5.87329 1.125C5.45908 1.125 5.12329 1.46079 5.12329 1.875V8.06422L3.40516 6.34719C3.11218 6.05439 2.6373 6.05454 2.3445 6.34752C2.0517 6.64051 2.05185 7.11538 2.34484 7.40818L5.31462 10.3761Z">
                                                    </path>
                                                </svg>
                                                9.05%
                                            </span>
                                        </div>

                                        <!-- Weight Input -->
                                        <div class="flex items-center justify-between">
                                            <label for="weight-input" class="text-sm font-medium text-gray-700">Update
                                                Weight (kg):</label>
                                            <div class="flex items-center gap-2">
                                                <input id="weight-input" type="number"
                                                    class="w-20 p-1 border rounded-lg text-sm text-center focus:ring-2 focus:ring-blue-300 focus:outline-none"
                                                    min="0" step="0.1">
                                                <button
                                                    class="px-3 py-1 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition">Save</button>
                                            </div>
                                        </div>
                                    </div>

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
                                        <div
                                            class="w-full rounded-lg border border-gray-200 bg-white shadow-sm p-3 text-center">
                                            <h5 class="text-md font-semibold text-gray-900">Calories</h5>
                                            <div id="bmr1" class="text-lg text-gray-900 font-bold mt-1"><?= htmlspecialchars($totalCalories) ?> kcal</div>
                                        </div>

                                        <!-- Protein -->
                                        <div
                                            class="w-full rounded-lg border border-gray-200 bg-white shadow-sm p-3 text-center">
                                            <h5 class="text-md font-semibold text-gray-900">Protein</h5>
                                            <div id="bmr2" class="text-lg text-gray-900 font-bold mt-1"><?= htmlspecialchars($totalProtein) ?> g</div>
                                        </div>

                                        <!-- Carbohydrates -->
                                        <div
                                            class="w-full rounded-lg border border-gray-200 bg-white shadow-sm p-3 text-center">
                                            <h5 class="text-md font-semibold text-gray-900">Carbs</h5>
                                            <div id="bmr3" class="text-lg text-gray-900 font-bold mt-1"><?= htmlspecialchars($totalCarbs) ?> g</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ====== Chart One End -->
                            </div>
                            <!--DASHBOARD: GOALS -->



                            <div class="col-span-12 md:col-span-7 xl:col-span-3">
                                <!-- Chart Container -->
                                <div
                                    class="rounded-2xl border border-gray-200 bg-gray-100 h-full flex flex-col shadow-md">
                                    <div
                                        class="shadow-default rounded-2xl bg-white px-4 md:px-5 pb-6 pt-5 flex flex-col h-full">

                                        <!-- Title Section -->
                                        <div class="flex flex-col sm:flex-row justify-between items-start ">
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-800 ">
                                                    Weekly Progress
                                                </h3>
                                                <p class="mt-1 text-sm md:text-base text-gray-500 ">
                                                    Your progress for this week!
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Chart Section -->
                                        <div class="relative flex-1 max-h-full mt-4">
                                            <div id="chartTwo" class="h-full max-h-full"></div>
                                        </div>

                                        <!-- Bottom Text -->
                                        <p
                                            class="mx-auto mt-4 w-full max-w-[90%] md:max-w-[380px] text-center text-sm md:text-base text-gray-500">
                                            You are almost close to your goal!
                                        </p>

                                    </div>
                                </div>
                            </div>



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
                                    <div
                                        class="h-80 overflow-y-auto p-4 space-y-4 scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-200">
                                        <!-- Post Loop -->
                                        <div class="space-y-6">
                                            <!-- Single Post -->
                                            <div class="space-y-4">
                                                <div class="flex items-start space-x-3">
                                                    <img src="photos/user.png" alt="User Icon"
                                                        class="w-12 h-12 rounded-full border-2 border-gray-300">
                                                    <p class="text-gray-800 flex-1 text-sm md:text-base">
                                                        Fitness isn’t about being better than someone else; it’s about
                                                        being better than you used to be. Keep going.
                                                        <span
                                                            class="font-semibold text-blue-500">#SelfImprovement</span>
                                                    </p>
                                                </div>

                                                <!-- Actions: Like, Repost, Reply -->
                                                <div class="flex items-center justify-between mt-2">
                                                    <!-- Like & Repost Buttons -->
                                                    <div class="flex space-x-4">
                                                        <button
                                                            class="flex items-center space-x-2 text-gray-600 hover:text-red-500 transition duration-300">
                                                            <i class="fa-solid fa-heart"></i>
                                                            <span class="text-sm">Like</span>
                                                        </button>

                                                        <button
                                                            class="flex items-center space-x-2 text-gray-600 hover:text-green-500 transition duration-300">
                                                            <i class="fa-solid fa-retweet"></i>
                                                            <span class="text-sm">Repost</span>
                                                        </button>
                                                    </div>

                                                    <!-- Reply Input Field -->
                                                    <div
                                                        class="flex items-center border border-gray-300 rounded-full px-3 py-1 w-full max-w-md">
                                                        <input type="text" placeholder="Write a reply..."
                                                            class="w-full outline-none bg-transparent text-gray-700 text-sm placeholder-gray-500">
                                                        <button
                                                            class="text-blue-500 hover:text-blue-700 transition duration-300">
                                                            <i class="fa-solid fa-paper-plane"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr class="border-gray-300 my-4">

                                            <!-- More Posts (Looped) -->
                                            <div class="space-y-4">
                                                <div class="flex items-start space-x-3">
                                                    <img src="photos/user.png" alt="User Icon"
                                                        class="w-12 h-12 rounded-full border-2 border-gray-300">
                                                    <p class="text-gray-800 flex-1 text-sm md:text-base">
                                                        "Let food be thy medicine and medicine be thy food." –
                                                        Hippocrates. A well-balanced diet makes a difference.
                                                        <span class="font-semibold text-blue-500">#HealthyEating</span>
                                                    </p>
                                                </div>

                                                <!-- Actions -->
                                                <div class="flex items-center justify-between mt-2">
                                                    <div class="flex space-x-4">
                                                        <button
                                                            class="flex items-center space-x-2 text-gray-600 hover:text-red-500 transition duration-300">
                                                            <i class="fa-solid fa-heart"></i>
                                                            <span class="text-sm">Like</span>
                                                        </button>
                                                        <button
                                                            class="flex items-center space-x-2 text-gray-600 hover:text-green-500 transition duration-300">
                                                            <i class="fa-solid fa-retweet"></i>
                                                            <span class="text-sm">Repost</span>
                                                        </button>
                                                    </div>

                                                    <div
                                                        class="flex items-center border border-gray-300 rounded-full px-3 py-1 w-full max-w-md">
                                                        <input type="text" placeholder="Write a reply..."
                                                            class="w-full outline-none bg-transparent text-gray-700 text-sm placeholder-gray-500">
                                                        <button
                                                            class="text-blue-500 hover:text-blue-700 transition duration-300">
                                                            <i class="fa-solid fa-paper-plane"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr class="border-gray-300 my-4">

                                            <!-- Repeat for Each Post -->
                                            <div class="space-y-4">
                                                <div class="flex items-start space-x-3">
                                                    <img src="photos/user.png" alt="User Icon"
                                                        class="w-12 h-12 rounded-full border-2 border-gray-300">
                                                    <p class="text-gray-800 flex-1 text-sm md:text-base">
                                                        Cardio doesn’t have to be boring. Try different activities like
                                                        hiking, swimming, or sports to stay active.
                                                        <span class="font-semibold text-blue-500">#StayActive</span>
                                                    </p>
                                                </div>

                                                <!-- Actions -->
                                                <div class="flex items-center justify-between mt-2">
                                                    <div class="flex space-x-4">
                                                        <button
                                                            class="flex items-center space-x-2 text-gray-600 hover:text-red-500 transition duration-300">
                                                            <i class="fa-solid fa-heart"></i>
                                                            <span class="text-sm">Like</span>
                                                        </button>
                                                        <button
                                                            class="flex items-center space-x-2 text-gray-600 hover:text-green-500 transition duration-300">
                                                            <i class="fa-solid fa-retweet"></i>
                                                            <span class="text-sm">Repost</span>
                                                        </button>
                                                    </div>

                                                    <div
                                                        class="flex items-center border border-gray-300 rounded-full px-3 py-1 w-full max-w-md">
                                                        <input type="text" placeholder="Write a reply..."
                                                            class="w-full outline-none bg-transparent text-gray-700 text-sm placeholder-gray-500">
                                                        <button
                                                            class="text-blue-500 hover:text-blue-700 transition duration-300">
                                                            <i class="fa-solid fa-paper-plane"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr class="border-gray-300 my-4">
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