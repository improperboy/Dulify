<?php
// Include database configuration
require_once "../includes/config.php";

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$user_id = $_SESSION["id"];
$support_success = $support_error = "";
$subject = $message = "";
$service_id = isset($_GET['service']) ? $_GET['service'] : null;

// Check if user has any purchased services
$has_purchases = false;
$sql = "SELECT COUNT(*) as count FROM purchases WHERE user_id = ? AND status = 'active'";
if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $has_purchases = ($row["count"] > 0);
    }
    mysqli_stmt_close($stmt);
}

// If user has no purchases, redirect to dashboard with error
if(!$has_purchases){
    header("location: ../dashboard.php?tab=support&error=no_purchases");
    exit;
}

// If service ID is provided, get service details
$service_name = "";
if($service_id) {
    $sql = "SELECT name FROM services WHERE id = ?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $service_id);
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($result) == 1){
                $row = mysqli_fetch_assoc($result);
                $service_name = $row["name"];
                $subject = "Support for " . $service_name;
            }
        }
        mysqli_stmt_close($stmt);
    }
}

// Process form submission
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate subject
    if(empty(trim($_POST["subject"]))){
        $support_error = "Please enter a subject for your support request.";
    } else{
        $subject = trim($_POST["subject"]);
    }
    
    // Validate message
    if(empty(trim($_POST["message"]))){
        $support_error = "Please enter your message.";
    } else{
        $message = trim($_POST["message"]);
    }
    
    // If no errors, insert the support message
    if(empty($support_error)){
        $sql = "INSERT INTO support_messages (user_id, subject, message, status) VALUES (?, ?, ?, 'open')";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "iss", $user_id, $subject, $message);
            
            if(mysqli_stmt_execute($stmt)){
                $support_success = "Your support request has been submitted. We'll get back to you as soon as possible.";
                $subject = $message = ""; // Clear form fields
            } else{
                $support_error = "Oops! Something went wrong. Please try again later.";
            }
            
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Support - Dulify</title>
    
    <!-- Preload critical assets -->
    <link rel="preload" href="https://cdn.tailwindcss.com" as="script">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" as="style">
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4CAF50;
            --primary-light: #81C784;
            --secondary: #FFC107;
            --dark: #1E3A8A;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            scroll-behavior: smooth;
            overflow-x: hidden;
        }
        
        .primary-bg {
            background-color: var(--primary);
        }
        .primary-text {
            color: var(--primary);
        }
        .secondary-bg {
            background-color: var(--secondary);
        }
        .secondary-text {
            color: var(--secondary);
        }
        .dark-bg {
            background-color: var(--dark);
        }
        
        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        /* Custom Animations */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Floating Elements */
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
        
        /* Custom Button */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(76, 175, 80, 0.2);
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-md fixed w-full z-50 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
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
                    <a href="../dashboard.php" class="btn-primary hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300 flex items-center">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                    </a>
                    <a href="logout.php" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg text-sm font-medium transition duration-300 flex items-center">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
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
                <a href="../index.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 transition duration-300">Home</a>
                <a href="../about.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 transition duration-300">About</a>
                <a href="../services.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 transition duration-300">Services</a>
                <a href="../dashboard.php" class="block px-3 py-2 rounded-md text-base font-medium text-white bg-green-500 hover:bg-green-600 transition duration-300">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
                <a href="logout.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100 transition duration-300">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Support Request Section -->
    <section class="pt-32 pb-16 md:pt-40 md:pb-24 relative overflow-hidden">
        <!-- Animated background elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
            <div class="absolute top-20 left-10 w-16 h-16 rounded-full bg-green-100 opacity-20 animate-pulse"></div>
            <div class="absolute top-1/4 right-20 w-24 h-24 rounded-full bg-yellow-100 opacity-20 animate-pulse animation-delay-2000"></div>
            <div class="absolute bottom-20 left-1/4 w-20 h-20 rounded-full bg-green-100 opacity-20 animate-pulse animation-delay-3000"></div>
            <div class="absolute bottom-1/3 right-1/4 w-32 h-32 rounded-full bg-yellow-100 opacity-20 animate-pulse animation-delay-1000"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex justify-start mb-8">
                <a href="../dashboard.php?tab=support" class="flex items-center text-green-600 hover:text-green-700 transition duration-300">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Support Dashboard
                </a>
            </div>
            
            <div class="md:flex items-start">
                <div class="md:w-2/3 lg:w-1/2 mb-10 md:mb-0" data-aos="fade-right" data-aos-duration="800">
                    <div class="bg-white rounded-xl shadow-lg p-8 transform transition duration-500 hover:shadow-xl">
                        <div class="mb-6">
                            <h2 class="text-3xl font-bold text-gray-800 mb-2">Contact Support</h2>
                            <p class="text-gray-600">Our team is here to help with any questions or issues you may have.</p>
                        </div>
                        
                        <?php if($support_success): ?>
                            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle mr-3 text-green-500"></i>
                                    <p><?php echo $support_success; ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($support_error): ?>
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
                                    <p><?php echo $support_error; ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . ($service_id ? "?service=" . $service_id : "")); ?>" method="post" class="space-y-6">
                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-tag text-gray-400"></i>
                                    </div>
                                    <input type="text" id="subject" name="subject" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition duration-300" value="<?php echo htmlspecialchars($subject); ?>" placeholder="Enter the subject of your request" required>
                                </div>
                            </div>
                            
                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                                <div class="relative">
                                    <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                                        <i class="fas fa-comment-alt text-gray-400"></i>
                                    </div>
                                    <textarea id="message" name="message" rows="6" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition duration-300" placeholder="Please describe your issue in detail" required><?php echo htmlspecialchars($message); ?></textarea>
                                </div>
                            </div>
                            
                            <div>
                                <button type="submit" class="btn-primary hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition duration-300 w-full flex items-center justify-center">
                                    <i class="fas fa-paper-plane mr-2"></i> Submit Support Request
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="md:w-1/3 lg:w-1/2 md:pl-12" data-aos="fade-left" data-aos-duration="800">
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                        <div class="flex items-center mb-4">
                            <div class="bg-green-100 p-3 rounded-full mr-4">
                                <i class="fas fa-info-circle text-green-500 text-xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800">Support Tips</h3>
                        </div>
                        <ul class="space-y-3 text-gray-600">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                <span>Be specific about the issue you're experiencing</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                <span>Include any error messages you've received</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                <span>Mention which service you need help with</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                <span>Our team typically responds within 24 hours</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center mb-4">
                            <div class="bg-white bg-opacity-20 p-3 rounded-full mr-4">
                                <i class="fas fa-headset text-white text-xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold">Need Urgent Help?</h3>
                        </div>
                        <p class="mb-4">For time-sensitive issues, you can reach our support team directly:</p>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <i class="fas fa-phone-alt mr-3"></i>
                                <span>+91 79915 15802 (Support)</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-envelope mr-3"></i>
                                <span>hello@dulify.com</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fab fa-whatsapp mr-3"></i>
                                <span>WhatsApp Support: +91 79915 15802</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Section -->  
    <footer class="bg-gray-900 text-white pt-16 pb-8 relative overflow-hidden">
        <!-- Custom Shape Divider Top -->  
        <div class="custom-shape-divider-top-footer">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
            </svg>
        </div>
        
        <!-- Animated Elements -->  
        <div class="absolute top-20 left-10 w-16 h-16 rounded-full bg-green-500 opacity-10 animate-pulse"></div>
        <div class="absolute bottom-40 right-20 w-24 h-24 rounded-full bg-yellow-500 opacity-10 animate-pulse animation-delay-2000"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                <!-- Company Info -->  
                <div class="col-span-1">
                    <div class="mb-6">
                        <a href="../index.php" class="inline-block">
                            <img src="../images/logo-white.png" alt="Dulify Logo" class="h-10">
                        </a>
                    </div>
                    <p class="text-gray-400 mb-6">Empowering businesses with innovative digital solutions that drive growth and success in the modern marketplace.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                            <i class="fab fa-facebook-f text-lg"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                            <i class="fab fa-twitter text-lg"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                            <i class="fab fa-instagram text-lg"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                            <i class="fab fa-linkedin-in text-lg"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->  
                <div class="col-span-1">
                    <h3 class="text-xl font-semibold mb-6 gradient-text">Quick Links</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="../index.php" class="text-gray-400 hover:text-white transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-green-500"></i> Home
                            </a>
                        </li>
                        <li>
                            <a href="../index.php#about" class="text-gray-400 hover:text-white transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-green-500"></i> About Us
                            </a>
                        </li>
                        <li>
                            <a href="../index.php#services" class="text-gray-400 hover:text-white transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-green-500"></i> Services
                            </a>
                        </li>
                        <li>
                            <a href="../index.php#features" class="text-gray-400 hover:text-white transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-green-500"></i> Features
                            </a>
                        </li>
                        <li>
                            <a href="../index.php#testimonials" class="text-gray-400 hover:text-white transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-green-500"></i> Success Stories
                            </a>
                        </li>
                        <li>
                            <a href="../index.php#contact" class="text-gray-400 hover:text-white transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-green-500"></i> Contact
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Solutions -->  
                <div class="col-span-1">
                    <h3 class="text-xl font-semibold mb-6 gradient-text">Solutions</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="../index.php#services" class="text-gray-400 hover:text-white transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-green-500"></i> Digital Marketing
                            </a>
                        </li>
                        <li>
                            <a href="../index.php#services" class="text-gray-400 hover:text-white transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-green-500"></i> Web Development
                            </a>
                        </li>
                        <li>
                            <a href="../index.php#services" class="text-gray-400 hover:text-white transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-green-500"></i> App Development
                            </a>
                        </li>
                        <li>
                            <a href="../index.php#services" class="text-gray-400 hover:text-white transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-green-500"></i> E-Commerce Solutions
                            </a>
                        </li>
                        <li>
                            <a href="../index.php#services" class="text-gray-400 hover:text-white transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-green-500"></i> SEO Optimization
                            </a>
                        </li>
                        <li>
                            <a href="../index.php#services" class="text-gray-400 hover:text-white transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-green-500"></i> Content Creation
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Contact Info -->  
                <div class="col-span-1">
                    <h3 class="text-xl font-semibold mb-6 gradient-text">Contact Us</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <div class="mt-1 mr-3 flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-green-500"></i>
                            </div>
                            <span class="text-gray-400">Pukhrayan, Kanpur Dehat, Uttar Pradesh 209111</span>
                        </li>
                        <li class="flex items-center">
                            <div class="mr-3 flex-shrink-0">
                                <i class="fas fa-phone-alt text-green-500"></i>
                            </div>
                            <span class="text-gray-400">+91 79915 15802</span>
                        </li>
                        <li class="flex items-center">
                            <div class="mr-3 flex-shrink-0">
                                <i class="fas fa-envelope text-green-500"></i>
                            </div>
                            <span class="text-gray-400">hello@dulify.com</span>
                        </li>
                        <li class="flex items-center">
                            <div class="mr-3 flex-shrink-0">
                                <i class="fab fa-whatsapp text-green-500"></i>
                            </div>
                            <span class="text-gray-400">WhatsApp: +91 79915 15802</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Divider -->  
            <div class="border-t border-gray-800 my-8"></div>
            
            <!-- Copyright -->  
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p class="text-gray-500">&copy; 2025 Dulify. All rights reserved.</p>
                </div>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-500 hover:text-gray-400 transition-colors duration-300 text-sm">Privacy Policy</a>
                    <a href="#" class="text-gray-500 hover:text-gray-400 transition-colors duration-300 text-sm">Terms of Service</a>
                    <a href="#" class="text-gray-500 hover:text-gray-400 transition-colors duration-300 text-sm">Cookie Policy</a>
                    <!-- <a href="#" class="text-gray-500 hover:text-gray-400 transition-colors duration-300 text-sm">Sitemap</a> -->
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->  
    <button id="back-to-top" class="fixed bottom-6 right-6 bg-green-600 text-white w-12 h-12 rounded-full flex items-center justify-center shadow-lg transform transition-all duration-300 hover:bg-green-700 hover:scale-110 opacity-0 invisible z-50">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- JavaScript -->  
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        // Initialize AOS animation
        AOS.init({
            duration: 800,
            once: true
        });
        
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
        
        // Back to top button
        const backToTopButton = document.getElementById('back-to-top');
        
        if (backToTopButton) {
            window.addEventListener('scroll', () => {
                if (window.pageYOffset > 300) {
                    backToTopButton.classList.remove('opacity-0', 'invisible');
                    backToTopButton.classList.add('opacity-100', 'visible');
                } else {
                    backToTopButton.classList.remove('opacity-100', 'visible');
                    backToTopButton.classList.add('opacity-0', 'invisible');
                }
            });
            
            backToTopButton.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
        
        // Navbar scroll effect
        const navbar = document.querySelector('nav');
        
        if (navbar) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 10) {
                    navbar.classList.add('shadow-md', 'bg-opacity-90');
                } else {
                    navbar.classList.remove('shadow-md', 'bg-opacity-90');
                }
            });
        }
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                e.preventDefault();
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    const offset = 80; // Offset for fixed header
                    const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - offset;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                    
                    // Close mobile menu if open
                    if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                        mobileMenu.classList.add('hidden');
                    }
                }
            });
        });
    </script>
</body>
</html>
