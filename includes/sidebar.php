<!-- DASHBOARD LINK -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
<link rel="stylesheet" href="custom/css/custom.css">
<link rel="stylesheet" href="css/button.css">
<link rel="stylesheet" href="css/sidebar.css">
<script defer src="script.js"></script>
<script src="https://cdn.tailwindcss.com"></script>

<aside :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'"
    class="sidebar fixed top-0 left-0 z-9999 flex h-screen w-[290px] flex-col overflow-y-auto border-r border-gray-200 bg-white px-5 transition-all duration-300 lg:static lg:translate-x-0 -translate-x-full shadow-lg"
    @click.outside="sidebarToggle = false">
    <!-- SIDEBAR LOGO -->
    <div class="flex items-center gap-4 p-2 mb-2 mt-2">
        <a href="dashboard.php" class="flex items-center gap-1 logo-hover">
            <img src="photos/plan.png" class="h-10 w-auto" alt="BiteTrack Logo">
            <span class="text-lg font-bold text-gray-900">BiteTrack</span>
        </a>
    </div>
    <!--LINE BREAK -->
    <hr class="border-gray-300 w-full mx-0">
    <!--WELCOME MESSAGE -->

    <!-- SIDEBAR HEADER -->
    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
        <!-- SIDEBAR Menu -->
        <nav class="mt-4">
            <ul class="flex flex-col gap-4">
                <!-- Menu Item: Dashboard -->
                <li>
                    <a href="dashboard.php"
                        class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>

                        <span class="flex-1 ms-3 whitespace-nowrap">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="goals.php"
                        class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>

                        <span class="flex-1 ms-3 whitespace-nowrap">Profile and Goals</span>
                    </a>
                </li>
                <li>
                    <a href="meals.php"
                        class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                        </svg>

                        <span class="flex-1 ms-3 whitespace-nowrap">Meal Logs</span>
                    </a>
                </li>
                <!-- Side Bar: COMMUNITY PAGE -->
                <li>
                    <a href="sns.php"
                        class="flex items-center p-2 text-gray-900 rounded-lg bg-gray-200 hover:bg-gray-100 group">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />
                        </svg>

                        <span class="flex-1 ms-3 whitespace-nowrap">Community Page</span>
                        <span
                            class="inline-flex items-center justify-center w-3 h-3 p-3 ms-3 text-sm font-medium text-blue-800 bg-blue-300 rounded-full">3</span>
                    </a>
                </li>
            </ul>
    </div>
    <section>
        <div id="sb_userprofile" class="absolute bottom-4 right-4 flex items-center gap-4">
            <!-- Logout Button -->
            <a href="logout.php" id="log_out" class="auth-buttons flex items-center gap-2 px-5 py-2 rounded-full text-black font-semibold shadow-md 
               bg-gradient-to-r from-yellow-400 to-red-400 hover:scale-105 hover:opacity-90 
               transition duration-300 ease-in-out">
                Logout
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </a>
        </div>
    </section>
</aside>
<!-- SIDEBAR END -->