<?php
include 'php_action/fetch_goals.php';
include 'php_action/get_profile.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BiteTrack - Your Goals!</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script defer src="script.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="custom/css/custom.css">
    <link rel="stylesheet" href="css/button.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <!-- Bootstrap File Input CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.5.2/css/fileinput.min.css" rel="stylesheet">
    <!-- Bootstrap File Input JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.5.2/js/fileinput.min.js"></script>

    <style>
        /* Custom styles for avatar enhancement */
        .avatar-enhanced {
            border: 2px solid #e5e7eb;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }

        .avatar-enhanced:hover {
            transform: scale(1.05);
        }

        .avatar-loading {
            background: #f3f4f6;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }

            100% {
                opacity: 1;
            }
        }

        .avatar-error {
            background: #fee2e2;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #dc2626;
            font-size: 12px;
        }
    </style>
</head>

<body class="flex flex-col min-h-screen">
    <main class="flex-grow">
        <div class="flex h-screen overflow-hidden">
            <?php require_once 'includes/sidebar.php'; ?>
            <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-hidden">
                <div :class="sidebarToggle ? 'block lg:hidden' : 'hidden'" class="fixed z-9 h-screen w-full bg-gray-900/50 hidden"></div>

                <main class="p-6 mx-auto max-w-full min-h-screen">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 h-full w-screen max-w-full flex-1">
                        <!-- Profile Section -->
                        <div class="w-full bg-white rounded-2xl flex flex-col max-h-full min-h-[80vh]">
                            <div class="relative w-full shadow-md rounded-2xl flex-none overflow-hidden">
                                <table class="w-full text-sm text-left text-gray-700 border-collapse">
                                    <thead class="text-gray-900 bg-gray-200 rounded-t-2xl">
                                        <tr>
                                            <th colspan="2" class="px-6 py-4 text-left text-lg font-semibold bg-gray-200 rounded-t-2xl">
                                                <div class="flex items-center justify-between">
                                                    <h3 class="text-lg font-semibold text-gray-800">Your Profile</h3>
                                                    <button onclick="openModal()">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-500 cursor-pointer hover:text-blue-700 transition">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-300 bg-white rounded-b-2xl overflow-hidden">
                                        <tr class="hover:bg-gray-100 transition">
                                            <td class="px-6 py-3 w-1/5 font-semibold">Avatar:</td>
                                            <td class="px-6 py-3">
                                                <img id="profileAvatar" src="<?php echo htmlspecialchars($user['profile_avatar'] ?? 'photos/user.png'); ?>" alt="Profile Avatar" class="w-20 h-20 rounded-full object-cover avatar-enhanced">
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-100 transition">
                                            <td class="px-6 py-3 w-1/5 font-semibold">First Name:</td>
                                            <td class="px-6 py-3" id="firstNameDisplay"><?php echo htmlspecialchars($user['first_name']); ?></td>
                                        </tr>
                                        <tr class="bg-gray-50 hover:bg-gray-100 transition">
                                            <td class="px-6 py-3 w-1/5 font-semibold">Last Name:</td>
                                            <td class="px-6 py-3" id="lastNameDisplay"><?php echo htmlspecialchars($user['last_name']); ?></td>
                                        </tr>
                                        <tr class="hover:bg-gray-100 transition">
                                            <td class="px-6 py-3 w-1/5 font-semibold">Age:</td>
                                            <td class="px-6 py-3" id="ageDisplay"><?php echo htmlspecialchars($user['age']); ?></td>
                                        </tr>
                                        <tr class="hover:bg-gray-100 transition">
                                            <td class="px-6 py-3 w-1/5 font-semibold">Height:</td>
                                            <td class="px-6 py-3" id="heightDisplay"><?php echo htmlspecialchars($user['height']); ?> cm</td>
                                        </tr>
                                        <tr class="bg-gray-50 hover:bg-gray-100 transition rounded-b-2xl">
                                            <td class="px-6 py-3 w-1/5 font-semibold">Weight:</td>
                                            <td class="px-6 py-3" id="weightDisplay"><?php echo htmlspecialchars($user['weight']); ?> kg</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Modal -->
                            <div id="editModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
                                <div class="bg-white rounded-lg p-6 w-96 shadow-lg">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Edit Profile</h3>
                                    <div id="edit-profile-messages" class="mb-4"></div>
                                    <div id="edit-profile-content" class="div-result">
                                        <!-- Profile Details Form -->
                                        <form id="editProfileForm" action="php_action/update_profile_details.php" method="POST">
                                            <div class="mb-3 form-group">
                                                <label class="block text-gray-700">First Name:</label>
                                                <input type="text" id="editFirstName" name="first_name" class="w-full border rounded px-3 py-2" required>
                                            </div>
                                            <div class="mb-3 form-group">
                                                <label class="block text-gray-700">Last Name:</label>
                                                <input type="text" id="editLastName" name="last_name" class="w-full border rounded px-3 py-2" required>
                                            </div>
                                            <div class="mb-3 form-group">
                                                <label class="block text-gray-700">Age:</label>
                                                <input type="number" id="editAge" name="age" class="w-full border rounded px-3 py-2" required>
                                            </div>
                                            <div class="mb-3 form-group">
                                                <label class="block text-gray-700">Height (cm):</label>
                                                <input type="number" id="editHeight" name="height" class="w-full border rounded px-3 py-2" required>
                                            </div>
                                            <div class="mb-3 form-group">
                                                <label class="block text-gray-700">Weight (kg):</label>
                                                <input type="number" id="editWeight" name="weight" class="w-full border rounded px-3 py-2" required>
                                            </div>
                                            <div class="flex justify-end mt-4">
                                                <button type="button" onclick="closeModal()" class="mr-2 px-4 py-2 bg-gray-300 rounded">Cancel</button>
                                                <button type="submit" id="editProfileBtn" class="px-4 py-2 bg-blue-500 text-white rounded">Save</button>
                                            </div>
                                        </form>
                                        <!-- Avatar Update Form -->
                                        <form id="updateProfileImageForm" action="php_action/update_avatar.php" method="POST" enctype="multipart/form-data" class="mt-4">
                                            <div class="mb-3 form-group">
                                                <label class="block text-gray-700">Profile Avatar:</label>
                                                <div class="center-block">
                                                    <input id="editAvatar" name="profile_avatar" type="file" class="file-input">
                                                </div>
                                                <div id="kv-avatar-errors-1" class="mt-2"></div>
                                            </div>
                                            <div class="mb-3 form-group">
                                                <label class="block text-gray-700">Avatar Preview:</label>
                                                <img id="editAvatarPreview" src="<?php echo htmlspecialchars($user['profile_avatar'] ?? 'photos/user.png'); ?>" alt="Avatar Preview" class="mt-2 w-20 h-20 rounded-full object-cover avatar-enhanced">
                                            </div>
                                            <div class="flex justify-end mt-4">
                                                <button type="submit" id="editAvatarBtn" class="px-4 py-2 bg-green-500 text-white rounded">Update Avatar</button>
                                            </div>
                                        </form>
                                        <div class="flex justify-end mt-4">
                                                <button type="button" id="doneEdit" onclick="closeModal()" class="px-4 py-2 bg-blue-500 text-white rounded">Done</button>
                                            </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Goals Section (Scrollable) -->
                            <div class="mt-4 shadow-lg shadow-gray-500/50 overflow-hidden rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-md flex-grow overflow-y-auto max-h-full">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-lg font-semibold text-gray-800">Goals</h3>
                                    <button onclick="toggleModal(true)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-500 cursor-pointer hover:text-blue-700 transition">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Goals modal -->
                                <div id="goalModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center hidden">
                                    <div class="bg-white p-6 rounded-lg w-96 shadow-lg relative left-[-133px]">
                                        <h2 class="text-xl font-semibold mb-4">Set Your Daily Goals</h2>
                                        <form action="php_action/save_goal.php" method="POST">
                                            <div class="mb-4">
                                                <label class="block text-sm font-medium">Calorie Intake (kcal)</label>
                                                <input type="number" name="calories" required class="w-full px-3 py-2 border rounded-lg">
                                            </div>
                                            <div class="mb-4">
                                                <label class="block text-sm font-medium">Protein Intake (g)</label>
                                                <input type="number" name="protein" required class="w-full px-3 py-2 border rounded-lg">
                                            </div>
                                            <div class="mb-4">
                                                <label class="block text-sm font-medium">Carbs Intake (g)</label>
                                                <input type="number" name="carbs" required class="w-full px-3 py-2 border rounded-lg">
                                            </div>
                                            <div class="flex justify-end space-x-2">
                                                <button type="button" onclick="toggleModal(false)" class="px-4 py-2 bg-gray-300 rounded-md">Cancel</button>
                                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Goals List -->
                                <div class="space-y-4">
                                    <?php
                                    if ($goal) {
                                        echo '<div class="w-full rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition p-4">';
                                        echo '<h5 class="text-md font-semibold text-gray-900">Daily Calorie Goal</h5>';
                                        echo '<p class="text-sm text-gray-700">Target: ' . htmlspecialchars($goal['calories']) . ' kcal</p>';
                                        echo '</div>';

                                        echo '<div class="w-full rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition p-4">';
                                        echo '<h5 class="text-md font-semibold text-gray-900">Daily Protein Goal</h5>';
                                        echo '<p class="text-sm text-gray-700">Target: ' . htmlspecialchars($goal['protein']) . ' g</p>';
                                        echo '</div>';

                                        echo '<div class="w-full rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition p-4">';
                                        echo '<h5 class="text-md font-semibold text-gray-900">Daily Carbs Goal</h5>';
                                        echo '<p class="text-sm text-gray-700">Target: ' . htmlspecialchars($goal['carbs']) . ' g</p>';
                                        echo '</div>';

                                        $weekly_calories = $goal['calories'] * 7;
                                        $weekly_protein = $goal['protein'] * 7;
                                        $weekly_carbs = $goal['carbs'] * 7;
                                    } else {
                                        echo '<div class="w-full rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition p-4">';
                                        echo '<h5 class="text-md font-semibold text-gray-900">Daily Calorie Goal</h5>';
                                        echo '<p class="text-sm text-gray-700">Set calorie goal now</p>';
                                        echo '</div>';

                                        echo '<div class="w-full rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition p-4">';
                                        echo '<h5 class="text-md font-semibold text-gray-900">Daily Protein Goal</h5>';
                                        echo '<p class="text-sm text-gray-700">Set protein goal now</p>';
                                        echo '</div>';

                                        echo '<div class="w-full rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition p-4">';
                                        echo '<h5 class="text-md font-semibold text-gray-900">Daily Carbs Goal</h5>';
                                        echo '<p class="text-sm text-gray-700">Set carbs goal now</p>';
                                        echo '</div>';

                                        echo '<p class="text-gray-700">No daily goals found.</p>';
                                        $weekly_calories = $weekly_protein = $weekly_carbs = 0;
                                    }
                                    ?>

                                    <h3 class="text-lg font-semibold text-gray-900 mt-6">Weekly Goals</h3>
                                    <div class="w-full rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition p-4">
                                        <h5 class="text-md font-semibold text-gray-900">Weekly Calorie Goal</h5>
                                        <p class="text-sm text-gray-700">Target: <?= htmlspecialchars($weekly_calories) ?> kcal</p>
                                    </div>

                                    <div class="w-full rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition p-4">
                                        <h5 class="text-md font-semibold text-gray-900">Weekly Protein Goal</h5>
                                        <p class="text-sm text-gray-700">Target: <?= htmlspecialchars($weekly_protein) ?> g</p>
                                    </div>

                                    <div class="w-full rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition p-4">
                                        <h5 class="text-md font-semibold text-gray-900">Weekly Carbs Goal</h5>
                                        <p class="text-sm text-gray-700">Target: <?= htmlspecialchars($weekly_carbs) ?> g</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Edit Goals Section -->
                        <div class="w-full bg-white rounded-2xl flex flex-col items-center justify-start h-full min-h-[80vh]">
                            <div class="relative w-full shadow-lg shadow-gray-500/50 rounded-2xl flex-none overflow-hidden self-start">
                                <table class="w-full text-sm text-left text-gray-700 border-collapse">
                                    <thead class="text-gray-900 rounded-t-lg">
                                        <tr>
                                            <th colspan="3" class="px-4 py-3 text-left text-lg font-semibold rounded-t-lg">
                                                <div class="flex items-center justify-between">
                                                    <h3 class="text-lg font-semibold text-gray-800">Your Daily Progress</h3>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-300 bg-white rounded-b-lg">
                                        <tr class="hover:bg-gray-100 transition">
                                            <td class="px-4 py-2 w-2/5 font-semibold">Calorie Intake</td>
                                            <td class="px-4 py-2">
                                                <div class="w-full bg-gray-200 rounded-full">
                                                    <div class="text-xs font-medium text-white text-center p-0.5 leading-none rounded-full" style="background: linear-gradient(to right, #FCD404, #FB6F74); width: <?= $calories_percentage_daily ?>%">
                                                        <?= $calories_percentage_daily ?>%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="bg-gray-50 hover:bg-gray-100 transition">
                                            <td class="px-4 py-2 font-semibold">Protein Consumption</td>
                                            <td class="px-4 py-2">
                                                <div class="w-full bg-gray-200 rounded-full">
                                                    <div class="text-xs font-medium text-white text-center p-0.5 leading-none rounded-full" style="background: linear-gradient(to right, #A0D8EF, #4A90E2); width: <?= $protein_percentage_daily ?>%">
                                                        <?= $protein_percentage_daily ?>%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-100 transition">
                                            <td class="px-4 py-2 font-semibold">Carbs Intake</td>
                                            <td class="px-4 py-2">
                                                <div class="w-full bg-gray-200 rounded-full">
                                                    <div class="text-xs font-medium text-white text-center p-0.5 leading-none rounded-full" style="background: linear-gradient(to right, #A8E6CF, #50C878); width: <?= $carbs_percentage_daily ?>%">
                                                        <?= $carbs_percentage_daily ?>%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="relative w-full shadow-lg shadow-gray-500/50 rounded-2xl flex-none overflow-hidden self-start mt-6">
                                <table class="w-full text-sm text-left text-gray-700 border-collapse">
                                    <thead class="text-gray-900 rounded-t-lg">
                                        <tr>
                                            <th colspan="3" class="px-4 py-3 text-left text-lg font-semibold rounded-t-lg">
                                                <div class="flex items-center justify-between">
                                                    <h3 class="text-lg font-semibold text-gray-800">Your Weekly Progress</h3>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-300 bg-white rounded-b-lg">
                                        <tr class="hover:bg-gray-100 transition">
                                            <td class="px-4 py-2 w-2/5 font-semibold">Calorie Intake</td>
                                            <td class="px-4 py-2">
                                                <div class="w-full bg-gray-200 rounded-full">
                                                    <div class="text-xs font-medium text-white text-center p-0.5 leading-none rounded-full" style="background: linear-gradient(to right, #FCD404, #FB6F74); width: <?= $calories_percentage_weekly ?>%">
                                                        <?= $calories_percentage_weekly ?>%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="bg-gray-50 hover:bg-gray-100 transition">
                                            <td class="px-4 py-2 font-semibold">Protein Consumption</td>
                                            <td class="px-4 py-2">
                                                <div class="w-full bg-gray-200 rounded-full">
                                                    <div class="text-xs font-medium text-white text-center p-0.5 leading-none rounded-full" style="background: linear-gradient(to right, #A0D8EF, #4A90E2); width: <?= $protein_percentage_weekly ?>%">
                                                        <?= $protein_percentage_weekly ?>%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-100 transition">
                                            <td class="px-4 py-2 font-semibold">Carbs Intake</td>
                                            <td class="px-4 py-2">
                                                <div class="w-full bg-gray-200 rounded-full">
                                                    <div class="text-xs font-medium text-white text-center p-0.5 leading-none rounded-full" style="background: linear-gradient(to right, #A8E6CF, #50C878); width: <?= $carbs_percentage_weekly ?>%">
                                                        <?= $carbs_percentage_weekly ?>%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="relative w-full shadow-lg shadow-gray-500/50 rounded-2xl flex-none overflow-hidden self-start mt-6">
                                <div class="overflow-y-auto max-h-[500px] p-4 bg-white">
                                    <div class="flex flex-col space-y-4">
                                        <div class="min-w-[320px] p-4 rounded-2xl bg-white shadow-lg">
                                            <h3 class="text-xl font-semibold text-gray-800">Calorie Intake</h3>
                                            <p class="text-gray-600">Your daily calories</p>
                                            <div id="calorie-chart" class="w-full h-60"></div>
                                        </div>

                                        <div class="min-w-[320px] p-4 rounded-2xl bg-white shadow-lg">
                                            <h3 class="text-xl font-semibold text-gray-800">Protein Intake</h3>
                                            <p class="text-gray-600">Your daily protein</p>
                                            <div id="protein-chart" class="w-full h-60"></div>
                                        </div>

                                        <div class="min-w-[320px] p-4 rounded-2xl bg-white shadow-lg">
                                            <h3 class="text-xl font-semibold text-gray-800">Carbohydrate Intake</h3>
                                            <p class="text-gray-600">Your daily carbs</p>
                                            <div id="carbs-chart" class="w-full h-60"></div>
                                        </div>
                                    </div>
                                </div>

                                <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
                                <script>
                                    var dates = <?= $dates_json ?>;
                                    var calories = <?= $calories_json ?>;
                                    var protein = <?= $protein_json ?>;
                                    var carbs = <?= $carbs_json ?>;

                                    function createChart(containerId, data, color, name) {
                                        return new ApexCharts(document.querySelector(containerId), {
                                            series: [{
                                                name: name,
                                                data: data
                                            }],
                                            chart: {
                                                type: "line",
                                                height: 240,
                                                toolbar: {
                                                    show: false
                                                }
                                            },
                                            dataLabels: {
                                                enabled: false
                                            },
                                            colors: [color],
                                            stroke: {
                                                width: 3,
                                                curve: "smooth"
                                            },
                                            markers: {
                                                size: 4,
                                                colors: ["#FCD404"],
                                                strokeWidth: 2
                                            },
                                            xaxis: {
                                                categories: dates,
                                                labels: {
                                                    style: {
                                                        colors: "#616161",
                                                        fontSize: "12px",
                                                        fontWeight: 400
                                                    }
                                                }
                                            },
                                            yaxis: {
                                                labels: {
                                                    style: {
                                                        colors: "#616161",
                                                        fontSize: "12px",
                                                        fontWeight: 400
                                                    }
                                                }
                                            },
                                            grid: {
                                                show: true,
                                                borderColor: "#dddddd",
                                                strokeDashArray: 5
                                            },
                                            tooltip: {
                                                theme: "dark"
                                            }
                                        });
                                    }

                                    createChart("#calorie-chart", calories, "#FB6F74", "Calorie Intake").render();
                                    createChart("#protein-chart", protein, "#4A90E2", "Protein Intake").render();
                                    createChart("#carbs-chart", carbs, "#50C878", "Carbohydrate Intake").render();
                                </script>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </main>

    <script defer src="bundle.js"></script>
    <script>
        function toggleModal(show) {
            const modal = document.getElementById("goalModal");
            if (modal) {
                modal.classList.toggle("hidden", !show);
            } else {
                console.error("Goal modal not found");
            }
        }

        function openModal() {
            const modal = document.getElementById("editModal");
            if (modal) {
                modal.classList.remove("hidden");

                // Prefill the form with current values
                $("#editFirstName").val($("#firstNameDisplay").text());
                $("#editLastName").val($("#lastNameDisplay").text());
                $("#editAge").val($("#ageDisplay").text());
                $("#editHeight").val($("#heightDisplay").text().replace(" cm", ""));
                $("#editWeight").val($("#weightDisplay").text().replace(" kg", ""));

                const avatarPath = $("#profileAvatar").attr('src');
                const editAvatarPreview = $("#editAvatarPreview");
                setImageLoading(editAvatarPreview);
                editAvatarPreview.onload = () => removeImageLoading(editAvatarPreview);
                editAvatarPreview.onerror = () => setImageError(editAvatarPreview);
                editAvatarPreview.attr('src', avatarPath);

                // Initialize fileinput plugin
                $("#editAvatar").fileinput({
                    overwriteInitial: true,
                    maxFileSize: 2500,
                    showClose: false,
                    showCaption: false,
                    browseLabel: '',
                    removeLabel: '',
                    browseIcon: '<i class="glyphicon glyphicon-folder-open"></i>',
                    removeIcon: '<i class="glyphicon glyphicon-remove"></i>',
                    removeTitle: 'Cancel or reset changes',
                    elErrorContainer: '#kv-avatar-errors-1',
                    msgErrorClass: 'alert alert-block alert-danger',
                    defaultPreviewContent: '<img src="' + avatarPath + '" alt="Profile Image" style="width:100%;">',
                    layoutTemplates: {
                        main2: '{preview} {remove} {browse}'
                    },
                    allowedFileExtensions: ["jpg", "png", "gif", "JPG", "PNG", "GIF"]
                });

                // Update preview when a new file is selected
                $("#editAvatar").on('fileloaded', function(event, file, previewId, index, reader) {
                    $("#editAvatarPreview").attr('src', reader.result);
                });

                // Reset preview if file is cleared
                $("#editAvatar").on('filecleared', function(event) {
                    $("#editAvatarPreview").attr('src', avatarPath);
                });
            } else {
                console.error("Edit modal not found");
            }
        }

        function closeModal() {
            const modal = document.getElementById("editModal");
            if (modal) {
                modal.classList.add("hidden");
                $(".text-danger").remove();
                $(".form-group").removeClass('has-error').removeClass('has-success');
                $('#edit-profile-messages').empty();
                // Reload the page after modal is dismissed
                window.location.reload();
            } else {
                console.error("Cannot close modal: editModal not found");
            }
        }

        // Handle profile details form submission
        $("#editProfileForm").unbind('submit').bind('submit', function(event) {
            event.preventDefault();

            var firstName = $("#editFirstName").val();
            var lastName = $("#editLastName").val();
            var age = $("#editAge").val();
            var height = $("#editHeight").val();
            var weight = $("#editWeight").val();

            if (firstName == "") {
                $("#editFirstName").after('<p class="text-danger">First Name field is required</p>');
                $('#editFirstName').closest('.form-group').addClass('has-error');
            } else {
                $("#editFirstName").find('.text-danger').remove();
                $("#editFirstName").closest('.form-group').addClass('has-success');
            }

            if (lastName == "") {
                $("#editLastName").after('<p class="text-danger">Last Name field is required</p>');
                $('#editLastName').closest('.form-group').addClass('has-error');
            } else {
                $("#editLastName").find('.text-danger').remove();
                $("#editLastName").closest('.form-group').addClass('has-success');
            }

            if (age == "") {
                $("#editAge").after('<p class="text-danger">Age field is required</p>');
                $('#editAge').closest('.form-group').addClass('has-error');
            } else {
                $("#editAge").find('.text-danger').remove();
                $("#editAge").closest('.form-group').addClass('has-success');
            }

            if (height == "") {
                $("#editHeight").after('<p class="text-danger">Height field is required</p>');
                $('#editHeight').closest('.form-group').addClass('has-error');
            } else {
                $("#editHeight").find('.text-danger').remove();
                $("#editHeight").closest('.form-group').addClass('has-success');
            }

            if (weight == "") {
                $("#editWeight").after('<p class="text-danger">Weight field is required</p>');
                $('#editWeight').closest('.form-group').addClass('has-error');
            } else {
                $("#editWeight").find('.text-danger').remove();
                $("#editWeight").closest('.form-group').addClass('has-success');
            }

            if (firstName && lastName && age && height && weight) {
                $("#editProfileBtn").prop('disabled', true).text('Saving...');

                var formData = new FormData(this);
                for (var pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }

                $.ajax({
                    url: window.location.pathname.replace(/[^/]*$/, '') + 'php_action/update_profile_details.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log("Profile Update Response:", response);
                        $("#editProfileBtn").prop('disabled', false).text('Save');
                        if (response.success) {
                            $("#firstNameDisplay").text(response.updatedData.first_name || firstName);
                            $("#lastNameDisplay").text(response.updatedData.last_name || lastName);
                            $("#ageDisplay").text(response.updatedData.age || age);
                            $("#heightDisplay").text((response.updatedData.height || height) + " cm");
                            $("#weightDisplay").text((response.updatedData.weight || weight) + " kg");
                            $('#edit-profile-messages').html('<div class="alert alert-success">' +
                                '<button type="button" class="close" data-dismiss="alert">×</button>' +
                                '<strong><i class="fas fa-check-circle"></i></strong> ' + response.messages +
                                '</div>');
                            $(".alert-success").delay(500).show(10, function() {
                                $(this).delay(3000).hide(10, function() {
                                    closeModal(); // This will trigger the reload
                                });
                            });
                        } else {
                            $('#edit-profile-messages').html('<div class="alert alert-danger">' +
                                '<button type="button" class="close" data-dismiss="alert">×</button>' +
                                '<strong><i class="fas fa-exclamation-circle"></i></strong> ' + response.messages +
                                '</div>');
                        }
                        $(".text-danger").remove();
                        $(".form-group").removeClass('has-error').removeClass('has-success');
                    },
                    error: function(xhr, status, error) {
                        console.log("AJAX Error:", xhr.status, xhr.statusText, xhr.responseText);
                        $("#editProfileBtn").prop('disabled', false).text('Save');
                        $('#edit-profile-messages').html('<div class="alert alert-danger">' +
                            '<button type="button" class="close" data-dismiss="alert">×</button>' +
                            '<strong><i class="fas fa-exclamation-circle"></i></strong> Error: ' + xhr.statusText +
                            '</div>');
                    }
                });
            }
            return false;
        });

        // Handle avatar update form submission
        $("#updateProfileImageForm").unbind('submit').bind('submit', function(event) {
            event.preventDefault();

            var avatar = $("#editAvatar").val();
            if (avatar == "") {
                $("#editAvatar").closest('.center-block').after('<p class="text-danger">Profile Avatar field is required</p>');
                $('#editAvatar').closest('.form-group').addClass('has-error');
            } else {
                $("#editAvatar").find('.text-danger').remove();
                $("#editAvatar").closest('.form-group').addClass('has-success');
            }

            if (avatar) {
                $("#editAvatarBtn").prop('disabled', true).text('Updating...');

                var formData = new FormData(this);
                $.ajax({
                    url: window.location.pathname.replace(/[^/]*$/, '') + 'php_action/update_avatar.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log("Avatar Update Response:", response);
                        $("#editAvatarBtn").prop('disabled', false).text('Update Avatar');
                        if (response.success) {
                            const newAvatarPath = response.updatedData.profile_avatar || $("#profileAvatar").attr('src');
                            const profileAvatar = $("#profileAvatar");
                            const editAvatarPreview = $("#editAvatarPreview");
                            setImageLoading(profileAvatar);
                            setImageLoading(editAvatarPreview);
                            profileAvatar.onload = () => removeImageLoading(profileAvatar);
                            profileAvatar.onerror = () => setImageError(profileAvatar);
                            editAvatarPreview.onload = () => removeImageLoading(editAvatarPreview);
                            editAvatarPreview.onerror = () => setImageError(editAvatarPreview);
                            profileAvatar.attr('src', newAvatarPath);
                            editAvatarPreview.attr('src', newAvatarPath);
                            $('#edit-profile-messages').html('<div class="alert alert-success">' +
                                '<button type="button" class="close" data-dismiss="alert">×</button>' +
                                '<strong><i class="fas fa-check-circle"></i></strong> ' + response.messages +
                                '</div>');
                            $(".alert-success").delay(500).show(10, function() {
                                $(this).delay(3000).hide(10, function() {
                                    closeModal(); // This will trigger the reload
                                });
                            });
                        } else {
                            $('#edit-profile-messages').html('<div class="alert alert-danger">' +
                                '<button type="button" class="close" data-dismiss="alert">×</button>' +
                                '<strong><i class="fas fa-exclamation-circle"></i></strong> ' + response.messages +
                                '</div>');
                        }
                        $(".fileinput-remove-button").click();
                        $(".text-danger").remove();
                        $(".form-group").removeClass('has-error').removeClass('has-success');
                    },
                    error: function(xhr, status, error) {
                        console.log("AJAX Error:", xhr.status, xhr.statusText, xhr.responseText);
                        $("#editAvatarBtn").prop('disabled', false).text('Update Avatar');
                        $('#edit-profile-messages').html('<div class="alert alert-danger">' +
                            '<button type="button" class="close" data-dismiss="alert">×</button>' +
                            '<strong><i class="fas fa-exclamation-circle"></i></strong> Error: ' + xhr.statusText +
                            '</div>');
                    }
                });
            }
            return false;
        });

        // Helper functions for image loading states
        function setImageLoading(img) {
            if (img && img.length) {
                img.classList.add("avatar-loading");
                img.classList.remove("avatar-error", "avatar-enhanced");
                img.style.backgroundImage = "none";
            } else {
                console.error("Image element not found for loading state");
            }
        }

        function removeImageLoading(img) {
            if (img && img.length) {
                img.classList.remove("avatar-loading");
                img.classList.add("avatar-enhanced");
            } else {
                console.error("Image element not found for removing loading state");
            }
        }

        function setImageError(img) {
            if (img && img.length) {
                img.classList.remove("avatar-loading", "avatar-enhanced");
                img.classList.add("avatar-error");
                img.src = "<?php echo htmlspecialchars($user['profile_avatar'] ?? 'photos/user.png'); ?>";
                img.alt = "Failed to load avatar";
                img.innerHTML = "Error loading image";
            } else {
                console.error("Image element not found for error state");
            }
        }
    </script>
</body>

</html>