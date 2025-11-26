<?php
session_start();
require_once 'php_action/db_connect.php';

// 1. Check Login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Fetch Current User Info
$sql_user = "SELECT user_name, profile_avatar FROM users WHERE user_id = ?";
$stmt = $connect->prepare($sql_user);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_user = $stmt->get_result();
$user = $result_user->fetch_assoc();
$user_name = $user['user_name'] ?? 'Unknown';
$user_avatar = $user['profile_avatar'] ?? 'photos/user.png';
$stmt->close();

// 3. Handle New Post Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['content'])) {
    $content = trim($_POST['content']);
    
    if (!empty($content)) {
        // FIX: We MUST include 'user_id' in the INSERT statement
        $stmt = $connect->prepare("INSERT INTO posts (user_id, user_name, user_avatar, post_content, post_time) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("isss", $user_id, $user_name, $user_avatar, $content);
        
        if ($stmt->execute()) {
            // Redirect to prevent resubmission on refresh
            header('Location: sns.php');
            exit();
        } else {
            // Optional: Handle error
            // echo "Error: " . $connect->error;
        }
        $stmt->close();
    }
}

// 4. Fetch Posts (Initial Load)
$sql = "SELECT * FROM posts ORDER BY post_time DESC";
$result = $connect->query($sql);
$posts = [];
while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BiteTrack - Community</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="custom/css/custom.css">
    <link rel="stylesheet" href="css/sidebar.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script defer src="script.js"></script>
</head>

<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen">

    <div class="flex h-screen overflow-hidden">
        
        <?php include 'includes/sidebar.php'; ?>

        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            
            <div :class="sidebarToggle ? 'block lg:hidden' : 'hidden'" class="fixed z-20 h-screen w-full bg-gray-900/50 hidden"></div>

            <main class="w-full bg-gray-50 min-h-screen p-6">
                <div class="mx-auto max-w-6xl">
                    
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-800">Community Feed 🌏</h1>
                        <p class="text-gray-500">Share your progress and connect with others.</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        
                        <div class="lg:col-span-2 space-y-6">
                            
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                                <form method="POST" class="flex gap-4">
                                    <img src="<?php echo htmlspecialchars($user_avatar); ?>" 
                                         alt="User" 
                                         class="w-12 h-12 rounded-full object-cover border border-gray-200 shrink-0">
                                    
                                    <div class="flex-1">
                                        <textarea name="content" 
                                                  rows="2" 
                                                  class="w-full bg-gray-50 border-0 rounded-xl p-3 text-gray-700 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition resize-none"
                                                  placeholder="What's on your mind, <?php echo htmlspecialchars($user_name); ?>?" required></textarea>
                                        
                                        <div class="flex justify-between items-center mt-3">
                                            <div class="flex gap-2 text-gray-400">
                                                <button type="button" class="hover:text-indigo-500 transition"><i class="fas fa-image"></i></button>
                                                <button type="button" class="hover:text-indigo-500 transition"><i class="fas fa-smile"></i></button>
                                            </div>
                                            <button type="submit" 
                                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-full text-sm font-semibold shadow-md transition transform active:scale-95">
                                                Post
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div id="news-feed" class="space-y-6">
                                <?php if (!empty($posts)): ?>
                                    <?php foreach ($posts as $post): ?>
                                        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition duration-200">
                                            <div class="flex items-center justify-between mb-4">
                                                <div class="flex items-center gap-3">
                                                    <img src="<?php echo htmlspecialchars($post['user_avatar'] ?? 'photos/user.png'); ?>" 
                                                         alt="User" 
                                                         class="w-10 h-10 rounded-full object-cover border border-gray-100">
                                                    <div>
                                                        <h4 class="font-bold text-gray-900 text-sm">
                                                            <?php echo htmlspecialchars($post['user_name']); ?>
                                                        </h4>
                                                        <span class="text-xs text-gray-400">
                                                            <?php echo date("F j, g:i a", strtotime($post['post_time'])); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <button class="text-gray-300 hover:text-gray-500">
                                                    <i class="fas fa-ellipsis-h"></i>
                                                </button>
                                            </div>

                                            <p class="text-gray-700 leading-relaxed text-sm sm:text-base">
                                                <?php echo nl2br(htmlspecialchars($post['post_content'])); ?>
                                            </p>

                                            <div class="border-t border-gray-50 mt-4 pt-3 flex gap-6 text-gray-400 text-sm">
                                                <button class="flex items-center gap-2 hover:text-red-500 transition">
                                                    <i class="far fa-heart"></i> Like
                                                </button>
                                                <button class="flex items-center gap-2 hover:text-blue-500 transition">
                                                    <i class="far fa-comment"></i> Comment
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center text-gray-400 py-10">
                                        <i class="fas fa-comment-slash text-4xl mb-2 opacity-30"></i>
                                        <p>No posts yet. Be the first!</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="hidden lg:block space-y-6">
                            
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-6">
                                <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">
                                    Community Guidelines 🛡️
                                </h3>
                                <ul class="space-y-3 text-sm text-gray-600">
                                    <li class="flex gap-3 items-start">
                                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                                        <span>Be kind and respectful to other members.</span>
                                    </li>
                                    <li class="flex gap-3 items-start">
                                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                                        <span>Share helpful nutrition tips and progress.</span>
                                    </li>
                                    <li class="flex gap-3 items-start">
                                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                                        <span>Avoid spamming or self-promotion.</span>
                                    </li>
                                </ul>
                                <div class="mt-6 pt-4 border-t border-gray-100">
                                    <p class="text-xs text-gray-400 text-center">© 2025 BiteTrack Community</p>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </main>
        </div>
    </div>

</body>
</html>
<?php $connect->close(); ?>