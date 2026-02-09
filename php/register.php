<?php
// Include database configuration and email helper
require_once "../includes/config.php";
require_once "../includes/email_helper.php";

// Define variables and initialize with empty values
$username = $password = $confirm_password = $email = $business_name = $business_type = $phone = "";
$username_err = $password_err = $confirm_password_err = $email_err = $business_name_err = $business_type_err = $phone_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter an email.";
    } elseif(!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)){
        $email_err = "Please enter a valid email address.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = trim($_POST["email"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "This email is already registered.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have at least 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Validate business name
    if(empty(trim($_POST["business_name"]))){
        $business_name_err = "Please enter your business or school name.";
    } else{
        $business_name = trim($_POST["business_name"]);
    }
    
    // Validate business type
    if(empty($_POST["business_type"])){
        $business_type_err = "Please select your business type.";
    } else{
        $business_type = $_POST["business_type"];
    }
    
    // Validate phone (optional)
    if(!empty(trim($_POST["phone"]))){
        if(!preg_match('/^[0-9+\-\s()]{10,20}$/', trim($_POST["phone"]))){
            $phone_err = "Please enter a valid phone number.";
        } else{
            $phone = trim($_POST["phone"]);
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err) && empty($business_name_err) && empty($business_type_err) && empty($phone_err)){
        
        // Generate verification token and expiry time
        $verification_token = generateVerificationToken();
        $token_expiry = calculateTokenExpiry(24); // Token expires in 24 hours
        
        // Debug: Log token generation (remove in production)
        error_log("DEBUG - Generated Token: " . $verification_token);
        error_log("DEBUG - Token Expiry: " . $token_expiry);
        error_log("DEBUG - Current Time: " . date('Y-m-d H:i:s'));
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, email, password, business_name, business_type, phone, user_type, email_verified, verification_token, token_expiry, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssssssss", $param_username, $param_email, $param_password, $param_business_name, $param_business_type, $param_phone, $param_user_type, $param_email_verified, $param_verification_token, $param_token_expiry);
            
            // Set parameters
            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_business_name = $business_name;
            $param_business_type = $business_type;
            $param_phone = $phone;
            $param_user_type = 'user'; // All registrations create regular users
            $param_email_verified = 0; // Email not verified yet
            $param_verification_token = $verification_token;
            $param_token_expiry = $token_expiry;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Get the inserted user ID
                $user_id = mysqli_insert_id($conn);
                
                // Log successful insertion
                error_log("DEBUG - User inserted successfully with ID: " . $user_id);
                
                // Verify the data was inserted correctly
                $verify_sql = "SELECT verification_token, token_expiry FROM users WHERE id = ?";
                if($verify_stmt = mysqli_prepare($conn, $verify_sql)){
                    mysqli_stmt_bind_param($verify_stmt, "i", $user_id);
                    mysqli_stmt_execute($verify_stmt);
                    mysqli_stmt_bind_result($verify_stmt, $db_token, $db_expiry);
                    mysqli_stmt_fetch($verify_stmt);
                    
                    error_log("DEBUG - Token in DB: " . $db_token);
                    error_log("DEBUG - Expiry in DB: " . $db_expiry);
                    
                    mysqli_stmt_close($verify_stmt);
                }
                
                // Send verification email
                if(sendVerificationEmail($email, $username, $verification_token)){
                    error_log("DEBUG - Verification email sent successfully");
                    // Redirect to verification pending page
                    header("location: verification_pending.php?email=" . urlencode($email));
                    exit();
                } else {
                    error_log("DEBUG - Failed to send verification email");
                    echo "Account created but verification email could not be sent. Please try to resend it.";
                }
            } else{
                error_log("DEBUG - Database insertion failed: " . mysqli_error($conn));
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        } else {
            error_log("DEBUG - Failed to prepare statement: " . mysqli_error($conn));
            echo "Oops! Something went wrong. Please try again later.";
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
    <title>Sign Up - Dulify</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            scroll-behavior: smooth;
            background-color: #f8fafc;
            overflow-x: hidden;
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
        .form-input:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.2);
        }
        
        /* Bubble Animation */
        .bubbles {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
            overflow: hidden;
        }
        
        .bubble {
            position: absolute;
            bottom: -100px;
            background: rgba(15, 255, 23, 0.1);
            border-radius: 50%;
            opacity: 2.5;
            animation: rise 15s infinite ease-in;
        }
        
        .bubble:nth-child(1) {
            width: 40px;
            height: 40px;
            left: 10%;
            animation-duration: 8s;
        }
        
        .bubble:nth-child(2) {
            width: 20px;
            height: 20px;
            left: 20%;
            animation-duration: 5s;
            animation-delay: 1s;
        }
        
        .bubble:nth-child(3) {
            width: 50px;
            height: 50px;
            left: 35%;
            animation-duration: 7s;
            animation-delay: 2s;
        }
        
        .bubble:nth-child(4) {
            width: 80px;
            height: 80px;
            left: 50%;
            animation-duration: 11s;
            animation-delay: 0s;
        }
        
        .bubble:nth-child(5) {
            width: 35px;
            height: 35px;
            left: 55%;
            animation-duration: 6s;
            animation-delay: 1s;
        }
        
        .bubble:nth-child(6) {
            width: 45px;
            height: 45px;
            left: 65%;
            animation-duration: 8s;
            animation-delay: 3s;
        }
        
        .bubble:nth-child(7) {
            width: 25px;
            height: 25px;
            left: 75%;
            animation-duration: 7s;
            animation-delay: 2s;
        }
        
        .bubble:nth-child(8) {
            width: 80px;
            height: 80px;
            left: 80%;
            animation-duration: 6s;
            animation-delay: 1s;
        }
        
        @keyframes rise {
            0% {
                bottom: -100px;
                transform: translateX(0);
            }
            50% {
                transform: translateX(100px);
            }
            100% {
                bottom: 1080px;
                transform: translateX(-200px);
            }
        }
        
        /* Form Animation */
        .form-container {
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Button Hover Effect */
        .btn-hover {
            transition: all 0.3s ease;
            transform: translateY(0);
        }
        
        .btn-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(76, 175, 80, 0.2);
        }
        
        /* Input Transition */
        .input-transition {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Bubble Background -->
    <div class="bubbles">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
    </div>

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
                    <a href="../index.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Home</a>
                    <a href="../about.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition duration-300">About</a>
                    <a href="../services.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Services</a>
                    <a href="login.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition duration-300">Login</a>
                    <a href="register.php" class="primary-bg hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-300 btn-hover">Sign Up</a>
                </div>
                <div class="md:hidden flex items-center">
                    <button id="menu-btn" class="text-gray-500 hover:text-gray-900 focus:outline-none transition duration-300">
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
                <a href="../index.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 transition duration-300">Home</a>
                <a href="../about.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 transition duration-300">About</a>
                <a href="../services.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 transition duration-300">Services</a>
                <a href="login.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100 transition duration-300">Login</a>
                <a href="register.php" class="block px-3 py-2 rounded-md text-base font-medium text-white primary-bg hover:bg-green-600 transition duration-300">Sign Up</a>
            </div>
        </div>
    </nav>

    <!-- Registration Form Section -->
    <section class="pt-24 pb-12 md:pt-32 md:pb-20">
        <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-lg shadow-lg form-container">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Create an Account</h2>
                    <p class="text-gray-600">Join Dulify and access affordable digital solutions</p>
                    <div class="w-20 h-1 primary-bg mx-auto mt-4 rounded-full"></div>
                </div>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" id="username" name="username" class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:border-green-500 focus:ring-green-500 input-transition <?php echo (!empty($username_err)) ? 'border-red-500' : ''; ?>" value="<?php echo $username; ?>">
                        <?php if(!empty($username_err)): ?>
                            <p class="mt-1 text-sm text-red-600 animate-pulse"><?php echo $username_err; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:border-green-500 focus:ring-green-500 input-transition <?php echo (!empty($email_err)) ? 'border-red-500' : ''; ?>" value="<?php echo $email; ?>">
                        <?php if(!empty($email_err)): ?>
                            <p class="mt-1 text-sm text-red-600 animate-pulse"><?php echo $email_err; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:border-green-500 focus:ring-green-500 input-transition <?php echo (!empty($password_err)) ? 'border-red-500' : ''; ?>" value="<?php echo $password; ?>">
                        <?php if(!empty($password_err)): ?>
                            <p class="mt-1 text-sm text-red-600 animate-pulse"><?php echo $password_err; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:border-green-500 focus:ring-green-500 input-transition <?php echo (!empty($confirm_password_err)) ? 'border-red-500' : ''; ?>" value="<?php echo $confirm_password; ?>">
                        <?php if(!empty($confirm_password_err)): ?>
                            <p class="mt-1 text-sm text-red-600 animate-pulse"><?php echo $confirm_password_err; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <label for="business_name" class="block text-sm font-medium text-gray-700 mb-1">Business/School Name</label>
                        <input type="text" id="business_name" name="business_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:border-green-500 focus:ring-green-500 input-transition <?php echo (!empty($business_name_err)) ? 'border-red-500' : ''; ?>" value="<?php echo $business_name; ?>">
                        <?php if(!empty($business_name_err)): ?>
                            <p class="mt-1 text-sm text-red-600 animate-pulse"><?php echo $business_name_err; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <label for="business_type" class="block text-sm font-medium text-gray-700 mb-1">Business Type</label>
                        <select id="business_type" name="business_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:border-green-500 focus:ring-green-500 input-transition <?php echo (!empty($business_type_err)) ? 'border-red-500' : ''; ?>">
                            <option value="" <?php if(empty($business_type)) echo "selected"; ?>>Select your business type</option>
                            <option value="school" <?php if($business_type == "school") echo "selected"; ?>>School</option>
                            <option value="grocery" <?php if($business_type == "grocery") echo "selected"; ?>>Grocery Store</option>
                            <option value="local_business" <?php if($business_type == "local_business") echo "selected"; ?>>Local Business</option>
                            <option value="other" <?php if($business_type == "other") echo "selected"; ?>>Other</option>
                        </select>
                        <?php if(!empty($business_type_err)): ?>
                            <p class="mt-1 text-sm text-red-600 animate-pulse"><?php echo $business_type_err; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone (Optional)</label>
                        <input type="text" id="phone" name="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:border-green-500 focus:ring-green-500 input-transition <?php echo (!empty($phone_err)) ? 'border-red-500' : ''; ?>" value="<?php echo $phone; ?>">
                        <?php if(!empty($phone_err)): ?>
                            <p class="mt-1 text-sm text-red-600 animate-pulse"><?php echo $phone_err; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <button type="submit" class="w-full primary-bg hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition duration-300 btn-hover transform hover:-translate-y-1">
                            <span class="relative z-10">Sign Up</span>
                        </button>
                    </div>
                    
                    <div class="text-center">
                        <p class="text-gray-600">Already have an account? <a href="login.php" class="primary-text hover:text-green-600 font-medium transition duration-300">Login here</a></p>
                    </div>
                </form>
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
                        <li><a href="../index.php" class="text-gray-400 hover:text-white transition duration-300">Home</a></li>
                        <li><a href="../services.php" class="text-gray-400 hover:text-white transition duration-300">Services</a></li>
                        <li><a href="../about.php" class="text-gray-400 hover:text-white transition duration-300">About Us</a></li>
                        <li><a href="login.php" class="text-gray-400 hover:text-white transition duration-300">Login</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact Us</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li>Email: hello@dulify.com</li>
                        <li>Phone: +91 79915 15802</li>
                        <li>Address: Pukhrayan, Kanpur Dehat, Uttar Pradesh 209111</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="../privacy.php" class="text-gray-400 hover:text-white transition duration-300">Privacy Policy</a></li>
                        <li><a href="../terms.php" class="text-gray-400 hover:text-white transition duration-300">Terms of Service</a></li>
                        <li><a href="../cookies.php" class="text-gray-400 hover:text-white transition duration-300">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; <?php echo date("Y"); ?> Dulify. All rights reserved.</p>
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

        // Create additional bubbles dynamically for a more lively background
        document.addEventListener('DOMContentLoaded', function() {
            const bubblesContainer = document.querySelector('.bubbles');
            
            // Create more bubbles
            for (let i = 0; i < 15; i++) {
                const bubble = document.createElement('div');
                bubble.classList.add('bubble');
                
                // Random size between 10px and 80px
                const size = Math.random() * 70 + 10;
                bubble.style.width = `${size}px`;
                bubble.style.height = `${size}px`;
                
                // Random position
                bubble.style.left = `${Math.random() * 100}%`;
                
                // Random animation duration between 5s and 15s
                const duration = Math.random() * 10 + 5;
                bubble.style.animationDuration = `${duration}s`;
                
                // Random delay
                bubble.style.animationDelay = `${Math.random() * 5}s`;
                
                // Random opacity
                bubble.style.opacity = Math.random() * 0.5 + 0.1;
                
                bubblesContainer.appendChild(bubble);
            }
        });
    </script>
</body>
</html>