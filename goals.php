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

    <style>
        /* Custom styles for avatar enhancement */
        .avatar-enhanced {
            border: 2px solid #e5e7eb;
            /* Light gray border */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            /* Subtle shadow */
            transition: transform 0.2s ease-in-out;
        }

        .avatar-enhanced:hover {
            transform: scale(1.05);
            /* Slight zoom on hover */
        }

        .avatar-loading {
            background: #f3f4f6;
            /* Light gray background while loading */
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
            /* Light red background for error */
            display: flex;
            align-items: center;
            justify-content: center;
            color: #dc2626;
            /* Red text */
            font-size: 12px;
        }
    </style>

    <script>
        function toggleModal(show) {
            const modal = document.getElementById("goalModal");
            modal.classList.toggle("hidden", !show);
        }

        // Open Modal
        function openModal() {
            document.getElementById("editModal").classList.remove("hidden");

            // Prefill the form with current values
            document.getElementById("editFirstName").value = document.getElementById("firstNameDisplay").textContent;
            document.getElementById("editLastName").value = document.getElementById("lastNameDisplay").textContent;
            document.getElementById("editAge").value = document.getElementById("ageDisplay").textContent;
            document.getElementById("editHeight").value = document.getElementById("heightDisplay").textContent.replace(" cm", "");
            document.getElementById("editWeight").value = document.getElementById("weightDisplay").textContent.replace(" kg", "");
            // Prefill avatar if available
            const avatarPath = "<?php echo htmlspecialchars($user['profile_avatar'] ?? 'photos/default.png'); ?>";
            const profileAvatar = document.getElementById("profileAvatar");
            const editAvatarPreview = document.getElementById("editAvatarPreview");
            setImageLoading(profileAvatar);
            setImageLoading(editAvatarPreview);
            profileAvatar.onload = () => removeImageLoading(profileAvatar);
            profileAvatar.onerror = () => setImageError(profileAvatar);
            editAvatarPreview.onload = () => removeImageLoading(editAvatarPreview);
            editAvatarPreview.onerror = () => setImageError(editAvatarPreview);
            profileAvatar.src = avatarPath;
            editAvatarPreview.src = avatarPath;
        }

        // Close Modal
        function closeModal() {
            document.getElementById("editModal").classList.add("hidden");
        }

        // Handle Form Submission
        document.getElementById("editProfileForm").addEventListener("submit", function(event) {
            event.preventDefault();

            const formData = new FormData(this);
            formData.append("user_id", <?= $_SESSION['user_id'] ?>);

            // AJAX Request to PHP
            fetch("php_action/update_profile.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the table with new values
                        document.getElementById("firstNameDisplay").textContent = data.updatedData.first_name || document.getElementById("editFirstName").value;
                        document.getElementById("lastNameDisplay").textContent = data.updatedData.last_name || document.getElementById("editLastName").value;
                        document.getElementById("ageDisplay").textContent = data.updatedData.age || document.getElementById("editAge").value;
                        document.getElementById("heightDisplay").textContent = (data.updatedData.height || document.getElementById("editHeight").value) + " cm";
                        document.getElementById("weightDisplay").textContent = (data.updatedData.weight || document.getElementById("editWeight").value) + " kg";
                        // Update avatars
                        const profileAvatar = document.getElementById("profileAvatar");
                        const editAvatarPreview = document.getElementById("editAvatarPreview");
                        const newAvatarPath = data.updatedData.profile_avatar || "<?php echo htmlspecialchars($user['profile_avatar'] ?? 'photos/user.png'); ?>";
                        profileAvatar.src = newAvatarPath;
                        editAvatarPreview.src = newAvatarPath;
                        setImageLoading(profileAvatar);
                        setImageLoading(editAvatarPreview);
                        profileAvatar.onload = () => removeImageLoading(profileAvatar);
                        profileAvatar.onerror = () => setImageError(profileAvatar);
                        editAvatarPreview.onload = () => removeImageLoading(editAvatarPreview);
                        editAvatarPreview.onerror = () => setImageError(editAvatarPreview);
                        profileAvatar.src = newAvatarPath;
                        editAvatarPreview.src = newAvatarPath;
                        closeModal();
                    } else {
                        alert("Error updating profile: " + data.error);
                    }
                })
                .catch(error => console.error("Error:", error));
        });

        // Preview avatar on selection
        document.getElementById("editAvatar").addEventListener("change", function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                const profileAvatar = document.getElementById("profileAvatar");
                const editAvatarPreview = document.getElementById("editAvatarPreview");
                setImageLoading(profileAvatar);
                setImageLoading(editAvatarPreview);
                reader.onload = function(e) {
                    profileAvatar.onload = () => removeImageLoading(profileAvatar);
                    profileAvatar.onerror = () => setImageError(profileAvatar);
                    editAvatarPreview.onload = () => removeImageLoading(editAvatarPreview);
                    editAvatarPreview.onerror = () => setImageError(editAvatarPreview);
                    profileAvatar.src = e.target.result;
                    editAvatarPreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Helper functions for image loading states
        function setImageLoading(img) {
            img.classList.add("avatar-loading");
            img.classList.remove("avatar-error", "avatar-enhanced");
            img.style.backgroundImage = "none"; // Clear any background
        }

        function removeImageLoading(img) {
            img.classList.remove("avatar-loading");
            img.classList.add("avatar-enhanced");
        }

        function setImageError(img) {
            img.classList.remove("avatar-loading", "avatar-enhanced");
            img.classList.add("avatar-error");
            img.src = "<?php echo htmlspecialchars($user['profile_avatar'] ?? 'photos/user.png'); ?>"; // Fallback to default
            img.alt = "Failed to load avatar";
            img.innerHTML = "Error loading image";
        }
    </script>
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
                                                <img id="profileAvatar" src="<?php echo htmlspecialchars($user['profile_avatar'] ?? 'photos/user.png'); ?>" alt="Profile Avatar" class="w-20 h-20 rounded-full object-cover">
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

                                    <form id="editProfileForm" enctype="multipart/form-data">
                                        <!-- Hidden Input for User ID -->
                                        <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">

                                        <div class="mb-3">
                                            <label class="block text-gray-700">First Name:</label>
                                            <input type="text" id="editFirstName" name="first_name" class="w-full border rounded px-3 py-2" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="block text-gray-700">Last Name:</label>
                                            <input type="text" id="editLastName" name="last_name" class="w-full border rounded px-3 py-2" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="block text-gray-700">Age:</label>
                                            <input type="number" id="editAge" name="age" class="w-full border rounded px-3 py-2" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="block text-gray-700">Height (cm):</label>
                                            <input type="number" id="editHeight" name="height" class="w-full border rounded px-3 py-2" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="block text-gray-700">Weight (kg):</label>
                                            <input type="number" id="editWeight" name="weight" class="w-full border rounded px-3 py-2" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="block text-gray-700">Profile Avatar:</label>
                                            <input type="file" id="editAvatar" name="profile_avatar" accept="image/*" class="w-full border rounded px-3 py-2">
                                            <img id="editAvatarPreview" src="<?php echo htmlspecialchars($user['profile_avatar'] ?? 'photos/user.png'); ?>" alt="Avatar Preview" class="mt-2 w-20 h-20 rounded-full object-cover">
                                        </div>

                                        <div class="flex justify-end mt-4">
                                            <button type="button" onclick="closeModal()" class="mr-2 px-4 py-2 bg-gray-300 rounded">Cancel</button>
                                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Save</button>
                                        </div>
                                    </form>
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
                </main>
            </div>
        </div>
        </div>
    </main>
</body>

<script defer src="bundle.js"></script>
<script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"rayId":"91b7c147fdd902a9","version":"2025.1.0","r":1,"token":"67f7a278e3374824ae6dd92295d38f77","serverTiming":{"name":{"cfExtPri":true,"cfL4":true,"cfSpeedBrain":true,"cfCacheStatus":true}}}' crossorigin="anonymous"></script>

<svg id="SvgjsSvg1001" width="2" height="0" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev" style="overflow: hidden; top: -100%; left: -100%; position: absolute; opacity: 0;">
    <defs id="SvgjsDefs1002"></defs>
    <polyline id="SvgjsPolyline1003" points="0,0"></polyline>
    <path id="SvgjsPath1004" d="M0 0 "></path>
</svg>

<div class="jvm-tooltip"></div>
</body>

</html>