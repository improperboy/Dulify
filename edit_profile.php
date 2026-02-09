<?php
// Include database configuration
require_once "includes/config.php";

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: php/login.php");
    exit;
}

// Initialize variables
$update_success = $update_error = "";
$user_id = $_SESSION["id"];

// Fetch user data
$sql = "SELECT username, email, business_name, business_type, phone FROM users WHERE id = ?";
if($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if(mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if(mysqli_num_rows($result) == 1) {
            $user_data = mysqli_fetch_assoc($result);
            $email = $user_data["email"];
            $business_name = $user_data["business_name"];
            $business_type = $user_data["business_type"];
            $phone = $user_data["phone"];
        } else {
            // Redirect to dashboard if user data not found
            header("Location: dashboard.php");
            exit();
        }
    } else {
        $update_error = "Error fetching user data: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
} else {
    $update_error = "Database error: " . mysqli_error($conn);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = trim($_POST["email"]);
    $business_name = trim($_POST["business_name"]);
    $business_type = trim($_POST["business_type"]);
    $phone = isset($_POST["phone"]) ? trim($_POST["phone"]) : "";
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $update_error = "Invalid email format";
    } else {
        // Check if email already exists for another user
        $check_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
        if($check_stmt = mysqli_prepare($conn, $check_sql)) {
            mysqli_stmt_bind_param($check_stmt, "si", $email, $user_id);
            if(mysqli_stmt_execute($check_stmt)) {
                $check_result = mysqli_stmt_get_result($check_stmt);
                
                if(mysqli_num_rows($check_result) > 0) {
                    $update_error = "Email is already in use by another account";
                } else {
                    // Update user profile
                    $update_sql = "UPDATE users SET email = ?, business_name = ?, business_type = ?, phone = ? WHERE id = ?";
                    if($update_stmt = mysqli_prepare($conn, $update_sql)) {
                        mysqli_stmt_bind_param($update_stmt, "ssssi", $email, $business_name, $business_type, $phone, $user_id);
                        
                        if(mysqli_stmt_execute($update_stmt)) {
                            $update_success = "Your profile has been updated successfully";
                            
                            // No need to update session data - dashboard reads directly from database
                        } else {
                            $update_error = "Error updating profile: " . mysqli_error($conn);
                        }
                        mysqli_stmt_close($update_stmt);
                    } else {
                        $update_error = "Database error preparing update: " . mysqli_error($conn);
                    }
                }
            } else {
                $update_error = "Error checking email: " . mysqli_error($conn);
            }
            mysqli_stmt_close($check_stmt);
        } else {
            $update_error = "Database error checking email: " . mysqli_error($conn);
        }
    }
}

// Database connection will be closed automatically at the end of the script
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Dulify</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            scroll-behavior: smooth;
        }
        .primary-bg {
            background-color: #4CAF50;
        }
        .primary-text {
            color: #4CAF50;
        }
        .secondary-bg {
            background-color: #FFC107;
        }
        .secondary-text {
            color: #FFC107;
        }
        .nav-link:hover {
            color: #4CAF50;
        }
        .mobile-menu {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-md fixed w-full z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-school primary-text text-3xl mr-2"></i>
                        <span class="text-xl font-bold text-gray-800">Dulify</span>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Home</a>
                    <a href="services.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Services</a>
                    <a href="about.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">About</a>
                    <a href="dashboard.php" class="primary-bg hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-300">Dashboard</a>
                    <a href="php/logout.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition duration-300">Logout</a>
                </div>
                <div class="md:hidden flex items-center">
                    <button id="menu-btn" class="text-gray-500 hover:text-gray-900 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile menu -->
        <div id="mobile-menu" class="mobile-menu hidden md:hidden bg-white shadow-lg">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="index.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Home</a>
                <a href="services.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Services</a>
                <a href="about.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">About</a>
                <a href="dashboard.php" class="block px-3 py-2 rounded-md text-base font-medium text-white bg-green-500 hover:bg-green-600">Dashboard</a>
                <a href="php/logout.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Edit Profile Section -->
    <section class="pt-24 pb-12 md:pt-32 md:pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-md mx-auto">
                <div class="mb-6">
                    <a href="dashboard.php?tab=profile" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Profile
                    </a>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-1">Edit Your Profile</h2>
                        <p class="text-gray-600">Update your account information</p>
                    </div>
                    
                    <?php if($update_success): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            <?php echo $update_success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($update_error): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <?php echo $update_error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-medium mb-2">Username</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition duration-300" value="<?php echo htmlspecialchars($user_data["username"]); ?>" disabled>
                            <p class="text-xs text-gray-500 mt-1">Username cannot be changed</p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                            <input type="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition duration-300" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-medium mb-2">Business/School Name</label>
                            <input type="text" name="business_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition duration-300" value="<?php echo htmlspecialchars($business_name); ?>" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-medium mb-2">Business Type</label>
                            <select name="business_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition duration-300" required>
                                <option value="school" <?php if($business_type == "school") echo "selected"; ?>>School</option>
                                <option value="grocery" <?php if($business_type == "grocery") echo "selected"; ?>>Grocery Store</option>
                                <option value="local_business" <?php if($business_type == "local_business") echo "selected"; ?>>Local Business</option>
                                <option value="other" <?php if($business_type == "other") echo "selected"; ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-medium mb-2">Phone (Optional)</label>
                            <input type="text" name="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition duration-300" value="<?php echo htmlspecialchars($phone); ?>">
                        </div>
                        
                        <div class="mb-4">
                            <button type="submit" class="w-full primary-bg hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition duration-300">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-school text-green-400 text-3xl mr-2"></i>
                        <span class="text-xl font-bold">Dulify</span>
                    </div>
                    <p class="text-gray-400">Empowering local schools and small businesses with affordable digital solutions.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-400 hover:text-white transition duration-300">Home</a></li>
                        <li><a href="services.php" class="text-gray-400 hover:text-white transition duration-300">Services</a></li>
                        <li><a href="about.php" class="text-gray-400 hover:text-white transition duration-300">About Us</a></li>
                        <li><a href="dashboard.php" class="text-gray-400 hover:text-white transition duration-300">Dashboard</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact Us</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-start">
                            <i class="fas fa-envelope mt-1 mr-2"></i>
                            <span>hello@dulify.com</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone-alt mt-1 mr-2"></i>
                            <span>+91 79915 15802</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-2"></i>
                            <span>Pukhrayan, Kanpur Dehat, Uttar Pradesh 209111</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="privacy.php" class="text-gray-400 hover:text-white transition duration-300">Privacy Policy</a></li>
                        <li><a href="terms.php" class="text-gray-400 hover:text-white transition duration-300">Terms of Service</a></li>
                        <li><a href="cookies.php" class="text-gray-400 hover:text-white transition duration-300">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; <?php echo date("Y"); ?>2025 @Dulify. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>