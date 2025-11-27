<?php
session_start();
require_once 'php_action/db_connect.php';

// 1. Check Login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$_SESSION['last_sns_visit'] = time();

// 2. Fetch Current User
$sql_user = "SELECT user_name, profile_avatar FROM users WHERE user_id = ?";
$stmt = $connect->prepare($sql_user);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$user_name = $user['user_name'] ?? 'Unknown';
$user_avatar = $user['profile_avatar'] ?? 'photos/user.png';
$stmt->close();

// 3. Determine Tab (Universal vs My Posts)
$active_tab = $_GET['tab'] ?? 'universal'; // Default to 'universal'

// 4. Build Query Based on Tab
$sql = "SELECT p.*, 
       (SELECT COUNT(*) FROM post_likes WHERE post_id = p.post_id) as like_count,
       (SELECT COUNT(*) FROM post_likes WHERE post_id = p.post_id AND user_id = ?) as user_liked,
       (SELECT COUNT(*) FROM post_comments WHERE post_id = p.post_id) as comment_count
       FROM posts p ";

if ($active_tab === 'my_posts') {
    $sql .= "WHERE p.user_id = ? "; // Filter by current user
}

$sql .= "ORDER BY p.post_time DESC";

$stmt = $connect->prepare($sql);

if ($active_tab === 'my_posts') {
    $stmt->bind_param("ii", $user_id, $user_id); // Bind twice (one for like check, one for filter)
} else {
    $stmt->bind_param("i", $user_id); // Bind once (just for like check)
}

$stmt->execute();
$result = $stmt->get_result();
$posts = [];
while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BiteTrack - Community</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <style>
        /* Fix glitching/flashing elements on load */
        [x-cloak] { display: none !important; }
        
        .emoji-grid { display: grid; grid-template-columns: repeat(8, 1fr); gap: 5px; max-height: 150px; overflow-y: auto; }
        .emoji-btn { font-size: 1.2rem; padding: 5px; cursor: pointer; border-radius: 5px; }
        .emoji-btn:hover { background: #f3f4f6; }
        
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen" x-data="{ sidebarToggle: false }">

    <div class="flex h-screen overflow-hidden">
        
        <?php include 'includes/sidebar.php'; ?>

        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            
            <div :class="sidebarToggle ? 'block lg:hidden' : 'hidden'" class="fixed z-20 h-screen w-full bg-gray-900/50 hidden" @click="sidebarToggle = false"></div>

            <div class="lg:hidden flex items-center justify-between bg-white p-4 border-b border-gray-200 sticky top-0 z-10">
                <span class="font-bold text-xl text-gray-800">Community</span>
                <button @click="sidebarToggle = !sidebarToggle" class="text-gray-600 focus:outline-none">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>

            <main class="w-full bg-gray-50 min-h-screen p-4 lg:p-6">
                <div class="mx-auto max-w-3xl">
                    
                    <div class="flex space-x-1 bg-gray-200 p-1 rounded-xl mb-6 shadow-inner">
                        <a href="?tab=universal" 
                           class="flex-1 text-center py-2.5 text-sm font-bold rounded-lg transition-all <?= $active_tab === 'universal' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' ?>">
                            <i class="fas fa-globe-americas mr-2"></i> Universal
                        </a>
                        <a href="?tab=my_posts" 
                           class="flex-1 text-center py-2.5 text-sm font-bold rounded-lg transition-all <?= $active_tab === 'my_posts' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' ?>">
                            <i class="fas fa-user mr-2"></i> My Posts
                        </a>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-8">
                        <form action="php_action/add_post.php" method="POST" enctype="multipart/form-data" class="flex gap-4">
                            <img src="<?php echo htmlspecialchars($user_avatar); ?>" class="w-12 h-12 rounded-full object-cover border border-gray-200 shrink-0">
                            
                            <div class="flex-1 relative" x-data="{ showEmojis: false }">
                                <textarea id="postContent" name="content" rows="2" class="w-full bg-gray-50 border-0 rounded-xl p-3 text-gray-700 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition resize-none" placeholder="What's on your mind?" required></textarea>
                                
                                <div id="imagePreviewContainer" class="hidden mt-2 relative w-fit">
                                    <img id="imagePreview" src="" class="h-24 rounded-lg border">
                                    <button type="button" onclick="clearImage()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs shadow-sm hover:bg-red-600"><i class="fas fa-times"></i></button>
                                </div>

                                <div x-show="showEmojis" x-cloak @click.away="showEmojis = false" class="absolute top-full left-0 mt-2 bg-white border border-gray-200 shadow-xl rounded-lg p-2 z-20 w-64">
                                    <div class="emoji-grid">
                                        <?php 
                                        $emojis = ['😀','😁','😂','🤣','😃','😄','😅','😆','😉','😊','😋','😎','😍','😘','🥰','😗','😙','😚','🙂','🤗','🤩','🤔','🤨','😐','😑','😶','🙄','😏','😣','😥','😮','🤐','😯','😪','😫','😴','😌','😛','😜','😝','🤤','😒','😓','😔','😕','🙃','🤑','😲','☹️','🙁','😖','😞','😟','😤','😢','😭','😦','😧','😨','😩','🤯','😬','😰','😱','🥵','🥶','😳','🤪','😵','😡','😠','🤬'];
                                        foreach($emojis as $e) { echo "<div class='emoji-btn' onclick=\"insertEmoji('$e')\">$e</div>"; }
                                        ?>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center mt-3 border-t border-gray-100 pt-2">
                                    <div class="flex gap-4 text-gray-400">
                                        <label class="cursor-pointer hover:text-indigo-500 transition flex items-center gap-1">
                                            <i class="fas fa-image"></i>
                                            <input type="file" name="post_image" id="postImage" class="hidden" accept="image/*" onchange="previewImage(this)">
                                        </label>
                                        <button type="button" @click="showEmojis = !showEmojis" class="hover:text-yellow-500 transition"><i class="fas fa-smile"></i></button>
                                    </div>
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-full text-sm font-semibold shadow-md transition">Post</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div id="news-feed" class="space-y-6">
                        <?php if (!empty($posts)): ?>
                            <?php foreach ($posts as $post): 
                                $pid = $post['post_id'];
                                
                                // Fetch Comments
                                $c_sql = "SELECT c.*, u.user_name, u.profile_avatar 
                                          FROM post_comments c 
                                          JOIN users u ON c.user_id = u.user_id 
                                          WHERE c.post_id = ? ORDER BY c.created_at ASC";
                                $c_stmt = $connect->prepare($c_sql);
                                $c_stmt->bind_param("i", $pid);
                                $c_stmt->execute();
                                $comments = $c_stmt->get_result();
                            ?>
                                <div id="post-<?= $pid ?>" class="bg-white p-5 lg:p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition duration-200 relative" x-data="{ openDropdown: false }">
                                    
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-3">
                                            <img src="<?php echo htmlspecialchars($post['user_avatar'] ?? 'photos/user.png'); ?>" class="w-10 h-10 rounded-full object-cover border border-gray-100">
                                            <div>
                                                <h4 class="font-bold text-gray-900 text-sm"><?php echo htmlspecialchars($post['user_name']); ?></h4>
                                                <span class="text-xs text-gray-400"><?php echo date("M j, g:i a", strtotime($post['post_time'])); ?></span>
                                            </div>
                                        </div>
                                        
                                        <?php if ($post['user_id'] == $user_id): ?>
                                            <div class="relative">
                                                <button @click="openDropdown = !openDropdown" class="text-gray-300 hover:text-gray-600 p-2"><i class="fas fa-ellipsis-h"></i></button>
                                                <div x-show="openDropdown" x-cloak @click.away="openDropdown = false" class="absolute right-0 mt-2 w-32 bg-white rounded-lg shadow-xl border border-gray-100 z-10 overflow-hidden">
                                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"><i class="fas fa-edit mr-2"></i> Edit</a>
                                                    <a href="#" onclick="alert('Delete feature coming soon!')" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50"><i class="fas fa-trash-alt mr-2"></i> Delete</a>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <p class="text-gray-800 leading-relaxed text-sm sm:text-base mb-3"><?php echo nl2br(htmlspecialchars($post['post_content'])); ?></p>

                                    <?php if (!empty($post['post_image'])): ?>
                                        <div class="mb-4 rounded-xl overflow-hidden border border-gray-100">
                                            <img src="<?php echo htmlspecialchars($post['post_image']); ?>" class="w-full h-auto object-cover max-h-[400px]">
                                        </div>
                                    <?php endif; ?>

                                    <div class="flex items-center gap-6 pt-3 border-t border-gray-50">
                                        <button onclick="toggleLike(this, <?= $pid ?>)" class="flex items-center gap-2 text-sm transition <?= $post['user_liked'] ? 'text-red-500 font-bold' : 'text-gray-500 hover:text-red-500' ?>">
                                            <i class="<?= $post['user_liked'] ? 'fas' : 'far' ?> fa-heart"></i>
                                            <span class="like-count"><?= $post['like_count'] ?></span>
                                        </button>
                                        <button onclick="toggleComments(<?= $pid ?>)" class="flex items-center gap-2 text-sm text-gray-500 hover:text-blue-500 transition">
                                            <i class="far fa-comment"></i>
                                            <span id="comment-count-<?= $pid ?>"><?= $post['comment_count'] ?></span>
                                        </button>
                                    </div>

                                    <div id="comments-section-<?= $pid ?>" class="hidden mt-4 pt-4 border-t border-gray-100 bg-gray-50 -mx-5 -mb-5 p-5 rounded-b-2xl">
                                        <div id="comment-list-<?= $pid ?>" class="space-y-3 mb-4 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                                            <?php while ($com = $comments->fetch_assoc()): ?>
                                                <div class="flex gap-3">
                                                    <img src="<?= htmlspecialchars($com['profile_avatar'] ?? 'photos/user.png') ?>" class="w-8 h-8 rounded-full object-cover shrink-0">
                                                    <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm flex-1">
                                                        <p class="text-xs font-bold text-gray-800 mb-1"><?= htmlspecialchars($com['user_name']) ?></p>
                                                        <p class="text-sm text-gray-600 break-words"><?= htmlspecialchars($com['comment_text']) ?></p>
                                                    </div>
                                                </div>
                                            <?php endwhile; ?>
                                        </div>

                                        <form onsubmit="submitComment(event, <?= $pid ?>)" class="flex gap-2">
                                            <img src="<?= htmlspecialchars($user_avatar) ?>" class="w-8 h-8 rounded-full object-cover shrink-0">
                                            <div class="relative flex-1">
                                                <input type="text" name="comment" class="w-full px-4 py-2 rounded-full border border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Write a comment...">
                                                <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-blue-600 hover:text-blue-800 p-1">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </div>
                                        </form>
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
            </main>
        </div>
    </div>

    <script>
        // Emoji logic
        function insertEmoji(emoji) {
            document.getElementById('postContent').value += emoji;
        }

        // Image Preview Logic
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                    document.getElementById('imagePreviewContainer').classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function clearImage() {
            document.getElementById('postImage').value = '';
            document.getElementById('imagePreviewContainer').classList.add('hidden');
        }

        // Toggle Comments
        function toggleComments(postId) {
            const section = document.getElementById(`comments-section-${postId}`);
            section.classList.toggle('hidden');
        }

        // Like Logic (AJAX)
        function toggleLike(btn, postId) {
            const icon = btn.querySelector('i');
            const countSpan = btn.querySelector('.like-count');
            let count = parseInt(countSpan.innerText);
            const isLiking = !btn.classList.contains('text-red-500');

            // Optimistic update
            if (isLiking) {
                btn.classList.add('text-red-500', 'font-bold');
                btn.classList.remove('text-gray-500');
                icon.classList.replace('far', 'fas');
                countSpan.innerText = count + 1;
            } else {
                btn.classList.remove('text-red-500', 'font-bold');
                btn.classList.add('text-gray-500');
                icon.classList.replace('fas', 'far');
                countSpan.innerText = Math.max(0, count - 1);
            }

            const formData = new FormData();
            formData.append('post_id', postId);

            fetch('php_action/like_post.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => { if (data.success) countSpan.innerText = data.new_count; })
            .catch(err => console.error(err));
        }

        // Comment Logic (AJAX)
        function submitComment(e, postId) {
            e.preventDefault();
            const form = e.target;
            const input = form.querySelector('input[name="comment"]');
            const commentText = input.value;
            if (!commentText.trim()) return;

            const formData = new FormData();
            formData.append('post_id', postId);
            formData.append('comment', commentText);

            fetch('php_action/add_comment.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const list = document.getElementById(`comment-list-${postId}`);
                    const newComment = `
                        <div class="flex gap-3">
                            <img src="${data.user_avatar}" class="w-8 h-8 rounded-full object-cover shrink-0">
                            <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm flex-1">
                                <p class="text-xs font-bold text-gray-800 mb-1">${data.user_name}</p>
                                <p class="text-sm text-gray-600 break-words">${data.comment}</p>
                            </div>
                        </div>`;
                    list.innerHTML += newComment;
                    input.value = '';
                    
                    const countSpan = document.getElementById(`comment-count-${postId}`);
                    if (countSpan) countSpan.innerText = parseInt(countSpan.innerText || 0) + 1;

                    list.scrollTop = list.scrollHeight;
                }
            })
            .catch(err => console.error(err));
        }

        // Highlight Post from Dashboard Link
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const highlightId = urlParams.get('highlight');

            if (highlightId) {
                const postElement = document.getElementById('post-' + highlightId);
                if (postElement) {
                    postElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    postElement.classList.add('ring-4', 'ring-indigo-300', 'bg-indigo-50');
                    setTimeout(() => {
                        postElement.classList.remove('ring-4', 'ring-indigo-300', 'bg-indigo-50');
                    }, 3000);
                }
            }
        });
    </script>
</body>
</html>