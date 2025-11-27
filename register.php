<?php
require_once 'php_action/db_connect.php';

session_start();

$errors = array();

if ($_POST) {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if (empty($firstName) || empty($lastName) ||empty($email) || empty($username) || empty($password) || empty($confirmPassword)) {
        if ($firstName == "") {
            $errors[] = "First Name is required";
        }
        if ($lastName == "") {
            $errors[] = "Last Name is required";
        }
        if ($username == "") {
            $errors[] = "Username is required";
        }
        if ($email == "") {
            $errors[] = "Email is required";
        }
        if ($password == "") {
            $errors[] = "Password is required";
        }
        if ($confirmPassword == "") {
            $errors[] = "Confirm Password is required";
        }
    } elseif ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match";
    } else {
        $sql = "SELECT * FROM users WHERE user_name = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $hashedPassword = md5($password);
            $insertSql = "INSERT INTO users ( user_name, password, email, first_name, last_name) VALUES (?, ?, ?, ?, ?)";
            $stmt = $connect->prepare($insertSql);
            $stmt->bind_param("sssss",  $username, $hashedPassword, $email, $firstName, $lastName);

            if ($stmt->execute()) {
                $_SESSION['userId'] = $stmt->insert_id;
                header('Location: index.php'); // Fixed path
                exit();
            } else {
                $errors[] = "Failed to register. Please try again.";
            }
        } else {
            $errors[] = "Username already exists";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BiteTrack - Join Us</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen justify-center items-center p-4">

    <div class="w-full max-w-lg">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center p-3 bg-indigo-100 rounded-2xl mb-4 shadow-sm">
                <img src="photos/plan.png" alt="BiteTrack Logo" class="h-10 w-auto">
            </div>
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Create your account</h2>
            <p class="text-gray-500 mt-2">Start your healthy journey with BiteTrack today.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-8">
                
                <?php if ($errors): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg mb-6 animate-pulse">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span class="font-bold">Oops!</span>
                        </div>
                        <ul class="list-disc list-inside text-sm mt-1 ml-1">
                            <?php foreach ($errors as $value) { echo "<li>$value</li>"; } ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="space-y-5">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input type="text" id="first_name" name="first_name" placeholder="John" required
                                    class="pl-10 w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-all text-gray-800">
                            </div>
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input type="text" id="last_name" name="last_name" placeholder="Doe" required
                                    class="pl-10 w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-all text-gray-800">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input type="email" id="email" name="email" placeholder="you@example.com" required
                                class="pl-10 w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-all text-gray-800">
                        </div>
                    </div>

                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-at text-gray-400"></i>
                            </div>
                            <input type="text" id="username" name="username" placeholder="johndoe123" required
                                class="pl-10 w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-all text-gray-800">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input type="password" id="password" name="password" placeholder="••••••••" required
                                    class="pl-10 w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-all text-gray-800">
                            </div>
                        </div>
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-check-circle text-gray-400"></i>
                                </div>
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" required
                                    class="pl-10 w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-all text-gray-800">
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 duration-200 flex items-center justify-center gap-2">
                            <span>Create Account</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                </form>
            </div>
            
            <div class="bg-gray-50 px-8 py-4 border-t border-gray-100 text-center">
                <p class="text-sm text-gray-600">
                    Already have an account? 
                    <a href="index.php" class="text-indigo-600 hover:text-indigo-700 font-bold hover:underline transition">Sign in</a>
                </p>
            </div>
        </div>
        
        <div class="text-center mt-8 text-xs text-gray-400">
            &copy; <?php echo date('Y'); ?> BiteTrack. All rights reserved.
        </div>

    </div>

</body>
</html>