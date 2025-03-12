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