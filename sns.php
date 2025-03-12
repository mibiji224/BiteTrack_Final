<?php
session_start();
require_once 'php_action/db_connect.php';


$user_id = $_SESSION['user_id'];

$user_id = $_SESSION['user_id'];

$sql_user = "SELECT user_name, profile_avatar FROM users WHERE user_id = '$user_id'";
$result_user = $connect->query($sql_user);
$user = $result_user->fetch_assoc();
$user_name = $user['user_name'] ?? 'Unknown';
$user_avatar = $user['profile_avatar'] ?? 'photos/user.png';


// Handle new post submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['content'])) {
    $content = $connect->real_escape_string(trim($_POST['content']));
    $avatar = 'photos/user.png'; // Default avatar or fetch from user table if available
    if (!empty($content)) {
        $sql = "INSERT INTO posts (user_name, user_avatar, post_content, post_time) VALUES ('$user_name', '$avatar', '$content', NOW())";
        $connect->query($sql);
        header('Location: sns.php');
        exit();
    }
}

// Fetch posts (for initial page load)
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
                                    <div class="rounded-2xl border border-gray-300 bg-white shadow-lg w-full max-w-2xl">
                                        <!-- Header -->
                                        <div class="bg-gradient-to-r from-[#FCD404] to-[#FB6F74] text-white text-lg font-semibold py-1 px-6 rounded-t-2xl">
                                            News Feed ✨
                                        </div>

                                        <!-- Add New Post -->
                                        <div class="p-4 border-b border-gray-300 bg-gray-50">
                                            <form method="POST" class="flex space-x-3">
                                                <img src="photos/user.png" alt="User Icon" class="w-12 h-12 rounded-full border border-gray-300">
                                                <input id="post-content" name="content" type="text" placeholder="What's on your mind?"
                                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-full outline-none text-gray-700">
                                                <button type="submit" id="post-button"
                                                    class="bg-gradient-to-r from-[#FCD404] to-[#FB6F74] text-white px-4 py-2 rounded-full hover:opacity-90 transition">
                                                    Post
                                                </button>
                                            </form>
                                        </div>

                                        <!-- News Feed (Posts Load Here) -->
                                        <div id="news-feed" class="relative h-96 overflow-y-auto p-2 space-y-6 scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-200 flex-1">
                                            <?php foreach ($posts as $post): ?>
                                                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                                                    <div class="flex items-center space-x-3">
                                                        <img src="photos/user.png" alt="User Icon" class="w-10 h-10 rounded-full">
                                                        <div>
                                                            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($post['user_name']); ?></p>
                                                            <p class="text-sm text-gray-500"><?php echo $post['post_time']; ?></p>
                                                        </div>
                                                    </div>
                                                    <p class="mt-2 text-gray-700"><?php echo htmlspecialchars($post['post_content']); ?></p>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>


                                </div>
                            </div>


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

    <script>
        // Function to fetch posts dynamically
        async function fetchPosts() {
            try {
                const response = await fetch('fetch_posts.php');
                const posts = await response.json();
                
                const newsFeed = document.getElementById('news-feed');
                newsFeed.innerHTML = '';
                
                posts.forEach(post => {
                    const postElement = document.createElement('div');
                    postElement.className = 'bg-white p-4 rounded-xl border border-gray-200 shadow-sm';
                    postElement.innerHTML = `
                        <div class="flex items-center space-x-3">
                            <img src="${post.user_avatar}" alt="User Icon" class="w-10 h-10 rounded-full">
                            <div>
                                <p class="font-semibold text-gray-800">${post.user_name}</p>
                                <p class="text-sm text-gray-500">${post.post_time}</p>
                            </div>
                        </div>
                        <p class="mt-2 text-gray-700">${post.post_content}</p>
                    `;
                    newsFeed.appendChild(postElement);
                });
            } catch (error) {
                console.error('Error fetching posts:', error);
            }
        }

        // Function to add a new post
        async function addPost(content) {
            try {
                const response = await fetch('add_post.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `content=${encodeURIComponent(content)}`
                });

                const result = await response.json();
                if (result.success) {
                    document.getElementById('post-content').value = ''; // Clear input
                    fetchPosts(); // Refresh posts
                } else {
                    alert('Failed to post: ' + result.error);
                }
            } catch (error) {
                console.error('Error adding post:', error);
            }
        }

        // Handle form submission
        document.getElementById('post-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const content = document.getElementById('post-content').value.trim();
            if (content) {
                await addPost(content);
            }
        });

        // Initial fetch
        fetchPosts();

        // Optional: Auto-refresh every 30 seconds
        // setInterval(fetchPosts, 30000);
    </script>
</body>

</html>
<?php $connect->close(); ?>