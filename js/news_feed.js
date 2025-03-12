$(document).ready(function () {
    function loadPosts() {
        $.ajax({
            url: "fetch_posts.php", // Backend PHP script
            type: "GET",
            dataType: "json",
            success: function (response) {
                let postsHtml = "";
                response.forEach(post => {
                    postsHtml += `
                        <div class="p-4 border-b border-gray-300 bg-white rounded-lg shadow-sm">
                            <div class="flex items-center space-x-3">
                                <img src="${post.user_avatar}" alt="User Avatar" class="w-12 h-12 rounded-full border border-gray-300">
                                <div>
                                    <p class="text-gray-800 font-semibold">${post.user_name}</p>
                                    <p class="text-sm text-gray-500">${post.post_time}</p>
                                </div>
                            </div>
                            <p class="mt-2 text-gray-700">${post.post_content}</p>
                        </div>
                    `;
                });
                $("#news-feed").html(postsHtml);
            },
            error: function () {
                $("#news-feed").html("<p class='text-center text-gray-500'>Failed to load posts.</p>");
            }
        });
    }

    // Load posts when page loads
    loadPosts();

    // Optional: Reload posts every 10 seconds (auto-refresh)
    setInterval(loadPosts, 10000);
});
