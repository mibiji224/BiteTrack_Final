<?php
require_once 'php_action/db_connect.php';

session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$errors = [];
$show_modal = false; // Logic to control modal visibility

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = md5($_POST['password']); 

    if (empty($username) || empty($password)) {
        if ($username == "") $errors[] = "Username is required";
        if ($_POST['password'] == "") $errors[] = "Password is required";
        $show_modal = true; // Keep modal open if there are errors
    } else {
        $sql = "SELECT user_id FROM users WHERE user_name = ? AND password = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['user_id'];
            header('Location: dashboard.php');
            exit();
        } else {
            $errors[] = "Incorrect username or password";
            $show_modal = true; // Keep modal open on failure
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BiteTrack - Welcome</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .text-gradient {
            background: linear-gradient(to right, #FCD404, #FB6F74);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .bg-gradient-brand {
            background: linear-gradient(to right, #FCD404, #FB6F74);
        }
        /* Modal Animation */
        .modal {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .modal.active {
            opacity: 1;
            visibility: visible;
        }
        .modal-content {
            transform: scale(0.95);
            transition: transform 0.3s ease;
        }
        .modal.active .modal-content {
            transform: scale(1);
        }
    </style>
</head>

<body class="bg-white text-gray-900 flex flex-col min-h-screen relative">

    <nav class="w-full py-4 px-6 lg:px-12 flex justify-between items-center sticky top-0 bg-white/80 backdrop-blur-md z-40 border-b border-gray-100">
        <div class="flex items-center gap-2">
            <img src="photos/plan.png" class="h-10 w-auto" alt="Logo">
            <span class="text-2xl font-bold tracking-tight text-gray-900">BiteTrack</span>
        </div>
        <div id="nav-buttons">
            <button onclick="openLogin()" class="text-gray-600 hover:text-gray-900 font-semibold px-4 transition">Log In</button>
            <a href="register.php" class="bg-gray-900 text-white px-5 py-2.5 rounded-full font-medium hover:bg-gray-800 transition shadow-lg shadow-gray-200">Sign Up</a>
        </div>
    </nav>

    <header class="relative overflow-hidden pt-16 pb-24 lg:pt-32 lg:pb-40">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 grid lg:grid-cols-2 gap-12 items-center">
            <div class="text-center lg:text-left">
                <h1 class="text-5xl lg:text-6xl font-extrabold tracking-tight text-gray-900 leading-tight mb-6">
                    Smart Nutrition <br>
                    <span class="text-gradient">Made Simple.</span>
                </h1>
                <p class="text-lg text-gray-600 mb-8 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                    Your ultimate companion for effortless food tracking. Monitor your meals, hit your macros, and achieve your health goals with BiteTrack.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="register.php" class="bg-gradient-brand text-white px-8 py-3.5 rounded-full font-bold text-lg hover:opacity-90 transition shadow-xl shadow-orange-200">
                        Start Tracking Free
                    </a>
                    <button onclick="openLogin()" class="px-8 py-3.5 rounded-full font-bold text-lg text-gray-700 border border-gray-200 hover:bg-gray-50 transition">
                        Log In
                    </button>
                </div>
            </div>
            <div class="relative">
                <div class="absolute -inset-4 bg-gradient-to-r from-yellow-100 to-pink-100 rounded-full blur-3xl opacity-30 animate-pulse"></div>
                <img src="photos/mealbg.jpg" alt="App Preview" class="relative rounded-2xl shadow-2xl border border-gray-100 transform rotate-2 hover:rotate-0 transition duration-500">
            </div>
        </div>
    </header>

    <section id="about" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 hover:-translate-y-1 transition duration-300">
                    <img src="photos/food2.jpg" class="w-full h-48 object-cover rounded-2xl mb-6" alt="Track">
                    <h3 class="text-xl font-bold mb-2">Track Meals</h3>
                    <p class="text-gray-600">Log breakfast, lunch, and dinner in seconds.</p>
                </div>
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 hover:-translate-y-1 transition duration-300">
                    <img src="photos/meal.gif" class="w-full h-48 object-cover rounded-2xl mb-6" alt="Goals">
                    <h3 class="text-xl font-bold mb-2">Set Goals</h3>
                    <p class="text-gray-600">Define your calorie and macro targets easily.</p>
                </div>
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 hover:-translate-y-1 transition duration-300">
                    <img src="photos/food3.jpg" class="w-full h-48 object-cover rounded-2xl mb-6" alt="Progress">
                    <h3 class="text-xl font-bold mb-2">See Progress</h3>
                    <p class="text-gray-600">Visualize your journey with beautiful charts.</p>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <div id="loginModal" class="modal fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm <?= $show_modal ? 'active' : '' ?>">
        
        <div class="modal-content bg-white w-full max-w-md rounded-3xl shadow-2xl p-8 relative m-4">
            
            <button onclick="closeLogin()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition">
                <i class="fas fa-times"></i>
            </button>

            <div class="text-center mb-8">
                <img src="photos/plan.png" class="h-12 w-auto mx-auto mb-4" alt="Logo">
                <h2 class="text-2xl font-bold text-gray-900">Welcome back!</h2>
                <p class="text-sm text-gray-500">Please enter your details to sign in.</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 text-red-600 text-sm flex items-start gap-2">
                    <i class="fas fa-exclamation-circle mt-0.5"></i>
                    <div>
                        <?php foreach ($errors as $error) echo "<p>$error</p>"; ?>
                    </div>
                </div>
            <?php endif; ?>

            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Username</label>
                    <input type="text" name="username" required 
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                           placeholder="Enter username">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Password</label>
                    <input type="password" name="password" required 
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                           placeholder="Enter password">
                </div>
                
                <button type="submit" class="w-full bg-gradient-brand text-white font-bold py-3.5 rounded-xl shadow-lg shadow-orange-200 hover:opacity-90 transition transform active:scale-[0.98]">
                    Sign In
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    New to BiteTrack? 
                    <a href="register.php" class="font-bold text-indigo-600 hover:text-indigo-700">Create Account</a>
                </p>
                <button onclick="closeLogin()" class="mt-4 text-xs text-gray-400 hover:text-gray-600 underline">
                    Back to Home
                </button>
            </div>
        </div>
    </div>

    <script>
        function openLogin() {
            document.getElementById('loginModal').classList.add('active');
        }

        function closeLogin() {
            document.getElementById('loginModal').classList.remove('active');
            // Clear URL if errors existed to prevent re-showing modal on refresh without resubmission
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
        }

        // Close modal if clicking outside the box
        document.getElementById('loginModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLogin();
            }
        });
    </script>

</body>
</html>