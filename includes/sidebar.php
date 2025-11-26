<?php
// Get the current file name to set active state
$current_page = basename($_SERVER['PHP_SELF']);

// Menu Items Config
$menu_items = [
    [
        'name' => 'Dashboard',
        'link' => 'dashboard.php',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />'
    ],
    [
        'name' => 'Profile & Goals',
        'link' => 'goals.php',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />'
    ],
    [
        'name' => 'Meal Logs',
        'link' => 'meals.php',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />'
    ],
    [
        'name' => 'Community',
        'link' => 'sns.php',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />',
        'badge' => 3
    ]
];
?>

<aside class="fixed top-0 left-0 z-50 h-screen w-[280px] flex flex-col bg-white border-r border-gray-200 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 -translate-x-full shadow-xl lg:shadow-none">
    
    <div class="h-20 flex items-center px-8 border-b border-gray-50 shrink-0">
        <a href="dashboard.php" class="flex items-center gap-3 group">
            <div class="p-2 bg-indigo-50 rounded-xl group-hover:bg-indigo-100 transition-colors duration-300">
                <img src="photos/plan.png" class="h-8 w-auto" alt="BiteTrack Logo">
            </div>
            <span class="text-xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">
                BiteTrack
            </span>
        </a>
    </div>

    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-2">
        <?php foreach ($menu_items as $item): 
            $isActive = ($current_page == $item['link']);
            
            $linkClass = $isActive 
                ? "bg-indigo-50 text-indigo-700 shadow-sm border-indigo-100" 
                : "text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-transparent";
            
            $iconClass = $isActive 
                ? "text-indigo-600" 
                : "text-gray-400 group-hover:text-gray-600";
        ?>
            <a href="<?= $item['link'] ?>" 
               class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-medium border transition-all duration-200 group relative <?= $linkClass ?>">
                
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" 
                     class="w-5 h-5 transition-colors duration-200 <?= $iconClass ?>">
                    <?= $item['icon'] ?>
                </svg>

                <span><?= $item['name'] ?></span>

                <?php if ($isActive): ?>
                    <span class="absolute right-4 w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                <?php endif; ?>

                <?php if (isset($item['badge']) && !$isActive): ?>
                    <span class="ml-auto bg-red-100 text-red-600 py-0.5 px-2 rounded-full text-xs font-bold">
                        <?= $item['badge'] ?>
                    </span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="p-4 border-t border-gray-100 shrink-0">
        <a href="logout.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 transition-all duration-200 group">
            <div class="p-1.5 bg-red-100 rounded-lg group-hover:bg-red-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-9A2.25 2.25 0 0 0 2.25 5.25v13.5A2.25 2.25 0 0 0 4.5 21h9a2.25 2.25 0 0 0 2.25-2.25V15m6-3H9m0 0 3-3m-3 3 3 3" />
                </svg>
            </div>
            <span class="font-semibold">Sign Out</span>
        </a>
    </div>
</aside>