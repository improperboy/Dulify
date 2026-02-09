<?php
// Include database configuration and email helper
require_once "../includes/config.php";
require_once "../includes/email_helper.php";

// Initialize variables
$email = $email_err = $success_message = "";

// Check if email is provided in the URL
if(isset($_GET["email"]) && !empty($_GET["email"])) {
    $email = trim($_GET["email"]);
    
    // Validate email format
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    }
}

// Process form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if(empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email address.";
    } else {
        $email = trim($_POST["email"]);
        
        // Check email format
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err = "Invalid email format.";
        }
    }
    
    // Check input errors before processing
    if(empty($email_err)) {
        // Prepare a select statement to check if the email exists and is not verified
        $sql = "SELECT id, username, email_verified FROM users WHERE email = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $email);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if email exists
                if(mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $user_id, $username, $email_verified);
                    mysqli_stmt_fetch($stmt);
                    
                    // Check if email is already verified
                    if($email_verified == 1) {
                        $email_err = "This email is already verified. You can <a href='login.php' class='text-green-600 hover:underline'>login here</a>.";
                    } else {
                        // Generate new verification token and expiry
                        $verification_token = generateVerificationToken();
                        $token_expiry = calculateTokenExpiry(24); // 24 hours
                        
                        // Update the user's verification token
                        $update_sql = "UPDATE users SET verification_token = ?, token_expiry = ? WHERE id = ?";
                        
                        if($update_stmt = mysqli_prepare($conn, $update_sql)) {
                            // Bind variables to the prepared statement as parameters
                            mysqli_stmt_bind_param($update_stmt, "ssi", $verification_token, $token_expiry, $user_id);
                            
                            // Attempt to execute the prepared statement
                            if(mysqli_stmt_execute($update_stmt)) {
                                // Send verification email
                                if(sendVerificationEmail($email, $username, $verification_token)) {
                                    $success_message = "Verification email has been resent to your email address.";
                                    $email = ""; // Clear the form
                                } else {
                                    $email_err = "Failed to send verification email. Please try again later.";
                                }
                            } else {
                                $email_err = "Something went wrong. Please try again later.";
                            }
                            
                            // Close statement
                            mysqli_stmt_close($update_stmt);
                        }
                    }
                } else {
                    $email_err = "No account found with that email address.";
                }
            } else {
                $email_err = "Oops! Something went wrong. Please try again later.";
            }
            
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resend Verification Email - Dulify</title>
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
                    <a href="../index.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Home</a>
                    <a href="../about.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">About</a>
                    <a href="../services.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Services</a>
                    <a href="login.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-300">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Resend Verification Section -->
    <section class="pt-24 pb-12 md:pt-32 md:pb-20">
        <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-lg shadow-md">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Resend Verification Email</h2>
                    <p class="text-gray-600">Enter your email address to receive a new verification link</p>
                    <div class="w-20 h-1 primary-bg mx-auto mt-4 rounded-full"></div>
                </div>

                <?php if(!empty($success_message)): ?>
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-500"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700"><?php echo $success_message; ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" id="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:ring-green-500 <?php echo (!empty($email_err)) ? 'border-red-500' : ''; ?>" value="<?php echo $email; ?>">
                        <?php if(!empty($email_err)): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo $email_err; ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <button type="submit" class="w-full primary-bg hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition duration-300 transform hover:-translate-y-1">
                            <span class="relative z-10">Resend Verification Email</span>
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="text-gray-600">Remember your password? <a href="login.php" class="primary-text hover:text-green-600 font-medium transition duration-300">Login here</a></p>
                    </div>
                </form>
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
                            <li><a href="../index.php" class="text-gray-400 hover:text-white transition">Home</a></li>
                            <li><a href="../about.php" class="text-gray-400 hover:text-white transition">About</a></li>
                            <li><a href="../services.php" class="text-gray-400 hover:text-white transition">Services</a></li>
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