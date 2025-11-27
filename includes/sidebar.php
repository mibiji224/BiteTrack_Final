<?php
// Get current page for active state styling
$current_page = basename($_SERVER['PHP_SELF']);

// Logic for Community Badge: 
// If the session variable 'last_sns_visit' is NOT set, show the red badge.
// (You set this variable in sns.php when they visit the page)
$show_community_badge = !isset($_SESSION['last_sns_visit']);

// Menu Items Configuration
$menu_items = [
    [
        'name' => 'Dashboard',
        'link' => 'dashboard.php',
        'icon' => '<i class="fas fa-home text-lg"></i>'
    ],
    [
        'name' => 'Profile & Goals',
        'link' => 'goals.php',
        'icon' => '<i class="fas fa-bullseye text-lg"></i>'
    ],
    [
        'name' => 'Meal Logs',
        'link' => 'meals.php',
        'icon' => '<i class="fas fa-utensils text-lg"></i>'
    ],
    [
        'name' => 'Community',
        'link' => 'sns.php',
        'icon' => '<i class="fas fa-users text-lg"></i>',
        'badge' => $show_community_badge // Pass badge status
    ]
];
?>

<aside 
    :class="sidebarToggle ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto shrink-0 shadow-xl lg:shadow-none flex flex-col"
    @click.away="sidebarToggle = false"
>
    
    <div class="h-20 flex items-center justify-center border-b border-gray-50 shrink-0">
        <a href="dashboard.php" class="flex items-center gap-3 group">
            <div class="p-2 bg-indigo-50 rounded-xl group-hover:bg-indigo-100 transition-colors duration-300">
                <img src="photos/plan.png" class="h-8 w-auto" alt="BiteTrack Logo">
            </div>
            <span class="text-xl font-bold text-gray-800 tracking-tight group-hover:text-indigo-600 transition-colors">
                BiteTrack
            </span>
        </a>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <?php foreach ($menu_items as $item): 
            $isActive = ($current_page == $item['link']);
            
            // Dynamic Styling Classes
            $baseClass = "flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 relative group";
            $activeClass = "bg-indigo-50 text-indigo-600 shadow-sm border border-indigo-100";
            $inactiveClass = "text-gray-600 hover:bg-gray-50 hover:text-gray-900 border border-transparent";
            
            $finalClass = $isActive ? $activeClass : $inactiveClass;
            $iconColor = $isActive ? "text-indigo-600" : "text-gray-400 group-hover:text-gray-600";
        ?>
            <a href="<?= $item['link'] ?>" class="<?= $baseClass ?> <?= $finalClass ?>" @click="sidebarToggle = false">
                
                <span class="<?= $iconColor ?> transition-colors duration-200 w-6 text-center flex justify-center">
                    <?= $item['icon'] ?>
                </span>

                <span><?= $item['name'] ?></span>
                
                <?php if ($isActive): ?>
                    <span class="absolute right-3 w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                <?php endif; ?>

                <?php if (isset($item['badge']) && $item['badge']): ?>
                    <span class="ml-auto flex h-2.5 w-2.5 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                    </span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="p-4 border-t border-gray-100 shrink-0">
        <a href="logout.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 transition-all duration-200 group border border-red-100">
            <div class="p-1.5 bg-red-100 rounded-lg group-hover:bg-red-200 transition-colors">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <span>Sign Out</span>
        </a>
    </div>
</aside>