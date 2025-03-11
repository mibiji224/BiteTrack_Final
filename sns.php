<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BiteTrack - Your Community!</title>

    <!-- Stylesheets -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="custom/css/custom.css">
    <link rel="stylesheet" href="css/button.css">
    <link rel="stylesheet" href="css/sidebar.css">

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script defer src="script.js"></script>
</head>

<body class="flex flex-col min-h-screen">
    <!-- Main Content -->
    <main class="flex-grow">
        <div class="flex h-screen overflow-hidden">
            <?php include 'includes/sidebar.php'; ?>    
            <!--MAIN Content Area -->
            <div class="relative flex flex-col flex-1 overflow-x-hidden">
                <!-- Overlay for Small Screens -->
                <div :class="sidebarToggle ? 'block lg:hidden' : 'hidden'" class="fixed z-10 h-screen w-full bg-gray-900/50 hidden"></div>
                <!--MAIN SNS START-->
                <main class="p-6 mx-auto max-w-full min-h-screen">
                    <!-- Main Grid Layout -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 h-full w-screen max-w-full flex-1">
                        <div class="col-span-2 w-full bg-white rounded-2xl flex flex-col h-full min-h-[80vh] shadow-lg ">
                            <div class="col-span-12">
                                <div class="rounded-2xl border border-gray-300 bg-white shadow-lg">
                                    <!-- Header -->
                                    <div class="bg-gradient-to-r from-[#FCD404] to-[#FB6F74] text-white text-lg font-semibold py-1 px-6 rounded-t-2xl">
                                        News Feed ✨
                                    </div>

                                    <!-- Add New Post (Static) -->
                                    <div class="p-2 border-b border-gray-300 bg-gray-50">
                                        <div class="flex space-x-3">
                                            <img src="photos/user.png" alt="User Icon" class="w-12 h-12 rounded-full border border-gray-300">
                                            <input type="text" placeholder="What's on your mind?" class="flex-1 px-4 py-2 border border-gray-300 rounded-full outline-none text-gray-700">
                                            <button class="bg-gradient-to-r from-[#FCD404] to-[#FB6F74] text-white px-4 py-2 rounded-full hover:opacity-90 transition">
                                                Post
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Scrollable Section (Only Posts Scroll) -->
                                    <div class="relative h-full overflow-y-auto p-2 space-y-6 scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-200 flex-1">
                                        <!-- Post Loop (Sample Post) -->
                                        <div class="space-y-6">
                                            <!-- Single Post -->
                                            <div class="bg-gray-50 p-3 rounded-xl shadow-sm border border-gray-200">
                                                <div class="flex items-start space-x-3">
                                                    <img src="photos/user.png" alt="User Icon" class="w-10 h-10 rounded-full border border-gray-300">
                                                    <div class="flex-1">
                                                        <p class="text-gray-900 font-semibold text-sm">Desiree Soronio</p>
                                                        <p class="text-gray-800 text-xs md:text-sm">
                                                            Fitness isn’t about being better than someone else; it’s about being better than you used to be. Keep going.
                                                            <span class="font-semibold text-blue-500">#SelfImprovement</span>
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Actions: Like, Repost, Reply -->
                                                <div class="flex items-center justify-between mt-2">
                                                    <div class="flex space-x-3 pr-4">
                                                        <button class="flex items-center space-x-1 text-gray-600 hover:text-red-500 transition text-xs">
                                                            <i class="fa-solid fa-heart"></i>
                                                            <span>Like</span>
                                                        </button>
                                                        <button class="flex items-center space-x-1 text-gray-600 hover:text-green-500 transition text-xs">
                                                            <i class="fa-solid fa-retweet"></i>
                                                            <span>Repost</span>
                                                        </button>
                                                    </div>

                                                    <div class="flex items-center border border-gray-300 rounded-full px-3 py-1 w-full">
                                                        <input type="text" placeholder="Write a reply..." class="w-full outline-none bg-transparent text-gray-700 text-xs placeholder-gray-500">
                                                        <button class="text-blue-500 hover:text-blue-700 transition ml-2">
                                                            <i class="fa-solid fa-paper-plane"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr class="border-gray-300">

                                            <!-- More Posts Here (Looped Content) -->
                                            <!--HERE-->
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>

                        <div class="w-full bg-white rounded-2xl flex flex-col h-full min-h-[80vh] shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">
                                Notifications
                            </h3>
                            <p>Sample Text</p>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </main>
    <!-- Main Content End -->
    <!-- MAIN CONTENT START -->
    <div>

    </div>

    <!-- Scripts -->
    <script defer src="bundle.js"></script>
</body>

</html>