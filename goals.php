<?php
include 'php_action/fetch_goals.php';
include 'php_action/get_profile.php';

// Pre-calculate weekly goals to keep HTML clean
$weekly_calories = 0;
$weekly_protein = 0;
$weekly_carbs = 0;

if ($goal) {
    $weekly_calories = $goal['calories'] * 7;
    $weekly_protein = $goal['protein'] * 7;
    $weekly_carbs = $goal['carbs'] * 7;
}
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
    
    <link rel="stylesheet" href="custom/css/custom.css">
    <link rel="stylesheet" href="css/button.css">
    <link rel="stylesheet" href="css/sidebar.css">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.5.2/css/fileinput.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.5.2/js/fileinput.min.js"></script>

    <style>
        /* Smooth transitions for progress bars */
        .progress-bar-fill {
            transition: width 1s ease-in-out;
        }
        .avatar-hover:hover {
            transform: scale(1.02);
            transition: transform 0.2s;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen">

    <div class="flex h-screen overflow-hidden">
        
        <?php require_once 'includes/sidebar.php'; ?>

        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            
            <div :class="sidebarToggle ? 'block lg:hidden' : 'hidden'" class="fixed z-20 h-screen w-full bg-gray-900/50 hidden"></div>

            <main class="w-full bg-gray-50 min-h-screen transition-all duration-200 ease-in-out p-6">
                <div class="mx-auto max-w-7xl">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Your Goals & Profile 🎯</h1>
                            <p class="text-sm text-gray-500">Manage your targets and track your progress.</p>
                        </div>
                        <div class="mt-4 sm:mt-0">
                            <button onclick="toggleModal(true)" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium shadow-md transition flex items-center gap-2">
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
                                        <div class="relative">
                                            <img id="profileAvatar" 
                                                 src="<?php echo htmlspecialchars($user['profile_avatar'] ?? 'photos/user.png'); ?>" 
                                                 alt="Profile" 
                                                 class="w-24 h-24 rounded-full border-4 border-white shadow-md object-cover avatar-hover cursor-pointer"
                                                 onclick="openModal()">
                                            <button onclick="openModal()" class="absolute bottom-0 right-0 bg-white p-1.5 rounded-full text-gray-600 shadow-sm border hover:text-blue-600 transition">
                                                <i class="fas fa-camera text-xs"></i>
                                            </button>
                                        </div>
                                        <button onclick="openModal()" class="text-sm text-blue-600 font-medium hover:underline mb-2">
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
                                    <i class="fas fa-calendar-week text-indigo-500"></i> Weekly Targets
                                </h3>
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-xl">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-yellow-200 flex items-center justify-center text-yellow-700 text-xs"><i class="fas fa-fire"></i></div>
                                            <span class="text-sm font-medium text-gray-700">Calories</span>
                                        </div>
                                        <span class="font-bold text-gray-800"><?= htmlspecialchars($weekly_calories) ?></span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-xl">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 text-xs"><i class="fas fa-drumstick-bite"></i></div>
                                            <span class="text-sm font-medium text-gray-700">Protein</span>
                                        </div>
                                        <span class="font-bold text-gray-800"><?= htmlspecialchars($weekly_protein) ?>g</span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-xl">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-green-200 flex items-center justify-center text-green-700 text-xs"><i class="fas fa-bread-slice"></i></div>
                                            <span class="text-sm font-medium text-gray-700">Carbs</span>
                                        </div>
                                        <span class="font-bold text-gray-800"><?= htmlspecialchars($weekly_carbs) ?>g</span>
                                    </div>
                                </div>
                            </div>

                        </div> <div class="col-span-12 lg:col-span-8 space-y-6">
                            
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center text-center relative overflow-hidden group hover:shadow-md transition">
                                    <div class="absolute top-0 right-0 w-16 h-16 bg-yellow-100 rounded-bl-full -mr-8 -mt-8 z-0 transition group-hover:bg-yellow-200"></div>
                                    <div class="z-10">
                                        <div class="text-yellow-500 mb-2 text-2xl"><i class="fas fa-fire-alt"></i></div>
                                        <p class="text-xs text-gray-400 uppercase font-bold tracking-wider">Daily Calories</p>
                                        <h3 class="text-2xl font-extrabold text-gray-800 my-1"><?= $goal ? htmlspecialchars($goal['calories']) : '0' ?></h3>
                                        <span class="text-xs text-gray-500">kcal target</span>
                                    </div>
                                </div>
                                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center text-center relative overflow-hidden group hover:shadow-md transition">
                                    <div class="absolute top-0 right-0 w-16 h-16 bg-blue-100 rounded-bl-full -mr-8 -mt-8 z-0 transition group-hover:bg-blue-200"></div>
                                    <div class="z-10">
                                        <div class="text-blue-500 mb-2 text-2xl"><i class="fas fa-dumbbell"></i></div>
                                        <p class="text-xs text-gray-400 uppercase font-bold tracking-wider">Daily Protein</p>
                                        <h3 class="text-2xl font-extrabold text-gray-800 my-1"><?= $goal ? htmlspecialchars($goal['protein']) : '0' ?></h3>
                                        <span class="text-xs text-gray-500">grams target</span>
                                    </div>
                                </div>
                                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center text-center relative overflow-hidden group hover:shadow-md transition">
                                    <div class="absolute top-0 right-0 w-16 h-16 bg-green-100 rounded-bl-full -mr-8 -mt-8 z-0 transition group-hover:bg-green-200"></div>
                                    <div class="z-10">
                                        <div class="text-green-500 mb-2 text-2xl"><i class="fas fa-wheat"></i></div>
                                        <p class="text-xs text-gray-400 uppercase font-bold tracking-wider">Daily Carbs</p>
                                        <h3 class="text-2xl font-extrabold text-gray-800 my-1"><?= $goal ? htmlspecialchars($goal['carbs']) : '0' ?></h3>
                                        <span class="text-xs text-gray-500">grams target</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                                <h3 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2">Current Progress</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div class="space-y-5">
                                        <h4 class="text-sm font-semibold text-gray-500 uppercase">Today</h4>
                                        
                                        <div>
                                            <div class="flex justify-between text-xs mb-1">
                                                <span class="font-medium text-gray-700">Calories</span>
                                                <span class="font-bold text-gray-900"><?= $calories_percentage_daily ?>%</span>
                                            </div>
                                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                                <div class="progress-bar-fill h-2.5 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500" style="width: <?= $calories_percentage_daily ?>%"></div>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="flex justify-between text-xs mb-1">
                                                <span class="font-medium text-gray-700">Protein</span>
                                                <span class="font-bold text-gray-900"><?= $protein_percentage_daily ?>%</span>
                                            </div>
                                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                                <div class="progress-bar-fill h-2.5 rounded-full bg-gradient-to-r from-blue-400 to-indigo-500" style="width: <?= $protein_percentage_daily ?>%"></div>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="flex justify-between text-xs mb-1">
                                                <span class="font-medium text-gray-700">Carbs</span>
                                                <span class="font-bold text-gray-900"><?= $carbs_percentage_daily ?>%</span>
                                            </div>
                                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                                <div class="progress-bar-fill h-2.5 rounded-full bg-gradient-to-r from-green-400 to-emerald-500" style="width: <?= $carbs_percentage_daily ?>%"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-5">
                                        <h4 class="text-sm font-semibold text-gray-500 uppercase">This Week</h4>
                                        
                                        <div>
                                            <div class="flex justify-between text-xs mb-1">
                                                <span class="font-medium text-gray-700">Calories</span>
                                                <span class="font-bold text-gray-900"><?= $calories_percentage_weekly ?>%</span>
                                            </div>
                                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                                <div class="progress-bar-fill h-2.5 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 opacity-80" style="width: <?= $calories_percentage_weekly ?>%"></div>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="flex justify-between text-xs mb-1">
                                                <span class="font-medium text-gray-700">Protein</span>
                                                <span class="font-bold text-gray-900"><?= $protein_percentage_weekly ?>%</span>
                                            </div>
                                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                                <div class="progress-bar-fill h-2.5 rounded-full bg-gradient-to-r from-blue-400 to-indigo-500 opacity-80" style="width: <?= $protein_percentage_weekly ?>%"></div>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="flex justify-between text-xs mb-1">
                                                <span class="font-medium text-gray-700">Carbs</span>
                                                <span class="font-bold text-gray-900"><?= $carbs_percentage_weekly ?>%</span>
                                            </div>
                                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                                <div class="progress-bar-fill h-2.5 rounded-full bg-gradient-to-r from-green-400 to-emerald-500 opacity-80" style="width: <?= $carbs_percentage_weekly ?>%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6">
                                <h3 class="text-lg font-bold text-gray-800 mb-4">History Trends</h3>
                                <div class="space-y-8">
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-600 mb-2">Calories History</h4>
                                        <div id="calorie-chart" class="w-full h-48"></div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-600 mb-2">Protein History</h4>
                                            <div id="protein-chart" class="w-full h-40"></div>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-600 mb-2">Carbs History</h4>
                                            <div id="carbs-chart" class="w-full h-40"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div> </div>
                </div>
            </main>
        </div>
    </div>

    <div id="goalModal" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm flex justify-center items-center transition-opacity">
        <div class="bg-white p-8 rounded-2xl w-full max-w-md shadow-2xl transform transition-all scale-100">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Set Daily Goals</h2>
                <button onclick="toggleModal(false)" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            
            <form action="php_action/save_goal.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Calorie Target (kcal)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400"><i class="fas fa-fire"></i></div>
                        <input type="number" name="calories" value="<?= $goal['calories'] ?? '' ?>" required class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Protein Target (g)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400"><i class="fas fa-drumstick-bite"></i></div>
                        <input type="number" name="protein" value="<?= $goal['protein'] ?? '' ?>" required class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Carbs Target (g)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400"><i class="fas fa-bread-slice"></i></div>
                        <input type="number" name="carbs" value="<?= $goal['carbs'] ?? '' ?>" required class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="toggleModal(false)" class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-md transition">Save Goals</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm flex justify-center items-center transition-opacity">
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl overflow-y-auto max-h-[90vh]">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Edit Profile</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            
            <div id="edit-profile-messages" class="mb-4"></div>

            <form id="editProfileForm" action="php_action/update_profile_details.php" method="POST" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">First Name</label>
                        <input type="text" id="editFirstName" name="first_name" class="mt-1 w-full px-3 py-2 border rounded-lg focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Last Name</label>
                        <input type="text" id="editLastName" name="last_name" class="mt-1 w-full px-3 py-2 border rounded-lg focus:ring-blue-500">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Age</label>
                        <input type="number" id="editAge" name="age" class="mt-1 w-full px-3 py-2 border rounded-lg focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Height (cm)</label>
                        <input type="number" id="editHeight" name="height" class="mt-1 w-full px-3 py-2 border rounded-lg focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Weight (kg)</label>
                        <input type="number" id="editWeight" name="weight" class="mt-1 w-full px-3 py-2 border rounded-lg focus:ring-blue-500">
                    </div>
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit" id="editProfileBtn" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">Save Details</button>
                </div>
            </form>

            <hr class="my-6 border-gray-200">

            <h4 class="text-lg font-semibold text-gray-800 mb-3">Update Photo</h4>
            <form id="updateProfileImageForm" action="php_action/update_avatar.php" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <input id="editAvatar" name="profile_avatar" type="file" class="file-input">
                    <div id="kv-avatar-errors-1" class="mt-2"></div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="shrink-0">
                        <img id="editAvatarPreview" src="" alt="Preview" class="w-16 h-16 rounded-full object-cover border">
                    </div>
                    <button type="submit" id="editAvatarBtn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">Upload New Photo</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        var dates = <?= $dates_json ?>;
        var calories = <?= $calories_json ?>;
        var protein = <?= $protein_json ?>;
        var carbs = <?= $carbs_json ?>;

        function createChart(containerId, data, color, name, height = 200) {
            return new ApexCharts(document.querySelector(containerId), {
                series: [{ name: name, data: data }],
                chart: {
                    type: "area",
                    height: height,
                    toolbar: { show: false },
                    zoom: { enabled: false }
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                fill: {
                    type: "gradient",
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.3,
                        stops: [0, 90, 100]
                    }
                },
                colors: [color],
                xaxis: {
                    categories: dates,
                    labels: { show: false },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: { show: false },
                grid: { show: false },
                tooltip: { theme: "light" }
            });
        }

        createChart("#calorie-chart", calories, "#F59E0B", "Calories", 180).render();
        createChart("#protein-chart", protein, "#3B82F6", "Protein", 150).render();
        createChart("#carbs-chart", carbs, "#10B981", "Carbs", 150).render();

        // Modal Logic
        function toggleModal(show) {
            const modal = document.getElementById("goalModal");
            if (show) {
                modal.classList.remove("hidden");
            } else {
                modal.classList.add("hidden");
            }
        }

        function openModal() {
            const modal = document.getElementById("editModal");
            modal.classList.remove("hidden");
            // Prefill logic
            $("#editFirstName").val($("#firstNameDisplay").text());
            $("#editLastName").val($("#lastNameDisplay").text());
            $("#editAge").val($("#ageDisplay").text());
            $("#editHeight").val($("#heightDisplay").text().replace(" cm", ""));
            $("#editWeight").val($("#weightDisplay").text().replace(" kg", ""));
            
            const avatarPath = $("#profileAvatar").attr('src');
            $("#editAvatarPreview").attr('src', avatarPath);
            
            // Re-init file input if needed (simplified for this view)
             $("#editAvatar").fileinput('refresh', {
                showClose: false,
                showCaption: false,
                browseLabel: 'Browse',
                removeLabel: '',
                browseIcon: '<i class="fas fa-folder-open"></i>',
                removeIcon: '<i class="fas fa-trash"></i>',
                allowedFileExtensions: ["jpg", "png", "gif"]
            });
        }

        function closeModal() {
            document.getElementById("editModal").classList.add("hidden");
            window.location.reload();
        }

        // Keep existing AJAX form handlers...
        // (Paste your existing $("#editProfileForm")... and $("#updateProfileImageForm")... logic here exactly as it was in your previous file)
        // For brevity, I assume you will copy-paste the JS block from your old file below this comment.
        
        // ... [INSERT YOUR EXISTING AJAX JS HERE] ...
        
        // Small fix for the AJAX part: ensure it targets the new IDs properly. 
        // Since I kept the IDs (editProfileForm, editFirstName, etc.) the same, your old JS should work perfectly!
    </script>
    
    <script>
       // Paste the large block of JS (starting from // Handle profile details form submission) 
       // from your previous file right here.
       // Make sure to keep the setImageLoading/Error functions too.
       
       // Quick placeholder for the avatar preview update in modal
        $("#editAvatar").on('change', function(){
            const file = this.files[0];
            if (file){
                let reader = new FileReader();
                reader.onload = function(event){
                    $('#editAvatarPreview').attr('src', event.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>