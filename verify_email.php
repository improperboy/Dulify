<?php
// Include database configuration
require_once "includes/config.php";

// Initialize variables
$verification_success = false;
$verification_error = "";

// Check if token is provided in the URL
if(isset($_GET["token"]) && !empty($_GET["token"])) {
    $token = $_GET["token"];
    
    // Prepare a select statement to find the user with this token
    $sql = "SELECT id, email, token_expiry FROM users WHERE verification_token = ? AND email_verified = 0";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $token);
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);
            
            // Check if token exists
            if(mysqli_stmt_num_rows($stmt) == 1) {                    
                // Bind result variables
                mysqli_stmt_bind_result($stmt, $user_id, $email, $token_expiry);
                if(mysqli_stmt_fetch($stmt)) {
                    // Check if token has expired
                    $current_time = date("Y-m-d H:i:s");
                    if($token_expiry && $token_expiry < $current_time) {
                        $verification_error = "Your verification link has expired. Please request a new one.";
                    } else {
                        // Token is valid, update user as verified
                        $update_sql = "UPDATE users SET email_verified = 1, verification_token = NULL, token_expiry = NULL WHERE id = ?";
                        
                        if($update_stmt = mysqli_prepare($conn, $update_sql)) {
                            // Bind variables to the prepared statement as parameters
                            mysqli_stmt_bind_param($update_stmt, "i", $user_id);
                            
                            // Attempt to execute the prepared statement
                            if(mysqli_stmt_execute($update_stmt)) {
                                $verification_success = true;
                            } else {
                                $verification_error = "Something went wrong. Please try again later.";
                            }
                            
                            // Close statement
                            mysqli_stmt_close($update_stmt);
                        }
                    }
                }
            } else {
                $verification_error = "Invalid verification link or account already verified.";
            }
        } else {
            $verification_error = "Oops! Something went wrong. Please try again later.";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }
} else {
    $verification_error = "Verification token is missing.";
}

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - Dulify</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            scroll-behavior: smooth;
            background-color: #f8fafc;
        }
        .primary-bg {
            background-color: #4CAF50;
        }
        .primary-text {
            color: #4CAF50;
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
                    <a href="about.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">About</a>
                    <a href="services.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Services</a>
                    <a href="php/login.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-300">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Verification Section -->
    <section class="pt-24 pb-12 md:pt-32 md:pb-20">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-lg shadow-md">
                <?php if($verification_success): ?>
                    <div class="text-center">
                        <div class="mb-6 text-green-500">
                            <i class="fas fa-check-circle text-6xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Email Verified Successfully!</h2>
                        <p class="text-gray-600 mb-6">Your email has been verified successfully. You can now log in to your account.</p>
                        <a href="php/login.php" class="primary-bg hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition duration-300 inline-block">Login to Your Account</a>
                    </div>
                <?php else: ?>
                    <div class="text-center">
                        <div class="mb-6 text-red-500">
                            <i class="fas fa-exclamation-circle text-6xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Verification Failed</h2>
                        <p class="text-gray-600 mb-6"><?php echo $verification_error; ?></p>
                        <a href="php/login.php" class="primary-bg hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition duration-300 inline-block">Back to Login</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between">
                <div class="mb-8 md:mb-0">
                    <div class="flex items-center">
                        <i class="fas fa-school text-green-400 text-3xl mr-2"></i>
                        <span class="text-xl font-bold">Dulify</span>
                    </div>
                    <p class="mt-4 max-w-xs text-gray-400">Empowering schools and local businesses with affordable digital solutions.</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                        <ul class="space-y-2">
                            <li><a href="index.php" class="text-gray-400 hover:text-white transition">Home</a></li>
                            <li><a href="about.php" class="text-gray-400 hover:text-white transition">About</a></li>
                            <li><a href="services.php" class="text-gray-400 hover:text-white transition">Services</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Legal</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-white transition">Privacy Policy</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition">Terms of Service</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400">&copy; <?php echo date("Y"); ?> Dulify. All rights reserved.</p>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>