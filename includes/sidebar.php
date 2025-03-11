<?php
// Get the current file name
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    .bg-gray-200.text-gray-500 {
        pointer-events: none;
        /* Disable click events */
    }
</style>

<aside
    class="sidebar fixed top-0 left-0 z-9999 flex h-screen w-[290px] flex-col overflow-y-auto border-r border-gray-200 bg-white px-5 transition-all duration-300 lg:static lg:translate-x-0 -translate-x-full shadow-lg">
    <nav class="mt-4">
        <div class="flex items-center gap-4 p-2 mb-2 mt-2">
            <a href="dashboard.php" class="flex items-center gap-1 logo-hover">
                <img src="photos/plan.png" class="h-10 w-auto" alt="BiteTrack Logo">
                <span class="text-lg font-bold text-gray-900">BiteTrack</span>
            </a>
        </div>
        <ul class="flex flex-col gap-4">
            <li>
                <a href="dashboard.php"
                    class="flex items-center p-2 rounded-lg group 
                    <?= $current_page == 'dashboard.php' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'hover:bg-gray-100 text-gray-900' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="goals.php"
                    class="flex items-center p-2 rounded-lg group 
                    <?= $current_page == 'goals.php' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'hover:bg-gray-100 text-gray-900' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">Profile and Goals</span>
                </a>
            </li>
            <li>
                <a href="meals.php"
                    class="flex items-center p-2 rounded-lg group 
                    <?= $current_page == 'meals.php' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'hover:bg-gray-100 text-gray-900' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                    </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">Meal Logs</span>
                </a>
            </li>
            <li>
                <a href="sns.php"
                    class="flex items-center p-2 rounded-lg group 
                    <?= $current_page == 'sns.php' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'hover:bg-gray-100 text-gray-900' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />
                    </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">Community Page</span>
                    <span
                        class="inline-flex items-center justify-center w-3 h-3 p-3 ms-3 text-sm font-medium text-blue-800 bg-blue-300 rounded-full">3</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Logout Button -->
    <div class="mt-auto mb-4">
        <a href="logout.php"
            class="flex items-center p-2 rounded-lg bg-red-500 text-white hover:bg-red-600 transition w-full">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-9A2.25 2.25 0 0 0 2.25 5.25v13.5A2.25 2.25 0 0 0 4.5 21h9a2.25 2.25 0 0 0 2.25-2.25V15m6-3H9m0 0 3-3m-3 3 3 3" />
            </svg>
            <span class="flex-1 ms-3">Logout</span>
        </a>
    </div>
</aside>