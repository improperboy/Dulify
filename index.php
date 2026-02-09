<?php
// Include database configuration
require_once "includes/config.php";

// Check if user is already logged in
$loggedIn = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dulify - Digital Solutions for Schools & Small Businesses</title>
    <meta name="description" content="Affordable digital tools for Indian schools and local shops. Manage attendance, homework, inventory, and more with our easy-to-use platform.">
    
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
        
        @keyframes wave {
            0% { transform: rotate(0deg); }
            10% { transform: rotate(14deg); }
            20% { transform: rotate(-8deg); }
            30% { transform: rotate(14deg); }
            40% { transform: rotate(-4deg); }
            50% { transform: rotate(10deg); }
            60% { transform: rotate(0deg); }
            100% { transform: rotate(0deg); }
        }
        
        .hero-image {
            animation: float 6s ease-in-out infinite;
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        .wave-animation {
            animation: wave 2s infinite;
            transform-origin: 70% 70%;
            display: inline-block;
        }
        
        /* Glow Effect */
        .glow-effect {
            box-shadow: 0 0 15px rgba(76, 175, 80, 0.5);
        }
        
        /* Custom Card */
        .feature-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
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
        
        /* Section Background Patterns */
        .school-pattern {
            background-image: radial-gradient(rgba(76, 175, 80, 0.1) 2px, transparent 2px);
            background-size: 40px 40px;
        }
        
        .business-pattern {
            background-image: radial-gradient(rgba(255, 193, 7, 0.1) 2px, transparent 2px);
            background-size: 40px 40px;
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
        
        /* Custom Shape Divider */
        .custom-shape-divider-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
            transform: rotate(180deg);
        }
        
        .custom-shape-divider-bottom svg {
            position: relative;
            display: block;
            width: calc(100% + 1.3px);
            height: 100px;
        }
        
        .custom-shape-divider-bottom .shape-fill {
            fill: #FFFFFF;
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
                    <a href="index.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Home</a>
                    <a href="about.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition duration-300">About</a>
                    <a href="services.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Services</a>
                    <a href="#features" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Features</a>
                    <a href="#testimonials" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Success Stories</a>
                    <a href="#contact" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Contact</a>
                    <?php if($loggedIn): ?>
                        <a href="dashboard.php" class="btn-primary hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300 flex items-center">
                            <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                        </a>
                        <a href="php/logout.php" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg text-sm font-medium transition duration-300 flex items-center">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    <?php else: ?>
                        <a href="php/login.php" class="btn-primary hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300 flex items-center">
                            <i class="fas fa-sign-in-alt mr-2"></i> Login
                        </a>
                        <a href="php/register.php" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg text-sm font-medium transition duration-300 flex items-center">
                            <i class="fas fa-user-plus mr-2"></i> Sign Up
                        </a>
                    <?php endif; ?>
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
                <a href="index.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 transition duration-300">Home</a>
                <a href="about.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 transition duration-300">About</a>
                <a href="services.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 transition duration-300">Services</a>
                <a href="#features" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 transition duration-300">Features</a>
                <a href="#testimonials" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 transition duration-300">Success Stories</a>
                <a href="#pricing" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 transition duration-300">Pricing</a>
                <a href="#contact" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 transition duration-300">Contact</a>
                <?php if($loggedIn): ?>
                    <a href="dashboard.php" class="block px-3 py-2 rounded-md text-base font-medium text-white bg-green-500 hover:bg-green-600 transition duration-300">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                    </a>
                    <a href="php/logout.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100 transition duration-300">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                <?php else: ?>
                    <a href="php/login.php" class="block px-3 py-2 rounded-md text-base font-medium text-white bg-green-500 hover:bg-green-600 transition duration-300">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login
                    </a>
                    <a href="php/register.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100 transition duration-300">
                        <i class="fas fa-user-plus mr-2"></i> Sign Up
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="pt-32 pb-16 md:pt-40 md:pb-24 relative overflow-hidden">
        <!-- Animated background elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
            <div class="absolute top-20 left-10 w-16 h-16 rounded-full bg-green-100 opacity-20 animate-pulse"></div>
            <div class="absolute top-1/4 right-20 w-24 h-24 rounded-full bg-yellow-100 opacity-20 animate-pulse animation-delay-2000"></div>
            <div class="absolute bottom-20 left-1/4 w-20 h-20 rounded-full bg-green-100 opacity-20 animate-pulse animation-delay-3000"></div>
            <div class="absolute bottom-1/3 right-1/4 w-32 h-32 rounded-full bg-yellow-100 opacity-20 animate-pulse animation-delay-1000"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="md:flex items-center">
                <div class="md:w-1/2 mb-10 md:mb-0" data-aos="fade-right" data-aos-duration="800">
                    <div class="inline-block bg-green-100 text-green-800 px-4 py-1 rounded-full text-sm font-medium mb-4">
                        <i class="fas fa-bolt mr-1"></i>Building Trust in schools & shops
                    </div>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-800 leading-tight mb-4">
                        Digital Tools for <span class="primary-text"> Schools</span> & <span class="secondary-text">Local Shops</span>
                    </h1>
                    <p class="text-lg md:text-xl text-gray-600 mb-8">
                        Affordable, bilingual tech solutions for educators and entrepreneurs. 
                        No IT skills needed—just <span class="font-semibold">simple, powerful tools</span> to grow your community.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="services.php" class="btn-primary hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition duration-300 flex items-center">
                            <i class="fas fa-rocket mr-2"></i> Explore Solutions
                        </a>
                        <a href="#demo" class="border-2 border-green-500 text-green-500 hover:bg-green-500 hover:text-white px-6 py-3 rounded-lg font-medium transition duration-300 flex items-center">
                            <i class="fas fa-play-circle mr-2"></i> Watch Demo
                        </a>
                    </div>
                    
                    <div class="mt-8 flex items-center">
                        <div class="flex -space-x-2">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" class="w-10 h-10 rounded-full border-2 border-white" alt="Happy customer">
                            <img src="https://randomuser.me/api/portraits/men/32.jpg" class="w-10 h-10 rounded-full border-2 border-white" alt="Happy customer">
                            <img src="https://randomuser.me/api/portraits/women/68.jpg" class="w-10 h-10 rounded-full border-2 border-white" alt="Happy customer">
                        </div>
                        <div class="ml-4">
                            <div class="flex items-center">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <span class="text-gray-700 ml-1 font-medium">4.9/5</span>
                            </div>
                            <p class="text-sm text-gray-600">Building Trust in educators & shop owners</p>
                        </div>
                    </div>
                </div>
                <div class="md:w-1/2 flex justify-center" data-aos="fade-left" data-aos-duration="800">
                    <div class="relative">
                        <img src="https://illustrations.popsy.co/amber/digital-nomad.svg" alt="Hero Illustration" class="hero-image w-full max-w-lg">
                        <div class="absolute -bottom-6 -left-6 bg-white p-4 rounded-xl shadow-lg" data-aos="fade-up" data-aos-delay="300">
                            <div class="flex items-center">
                                <div class="bg-green-100 p-3 rounded-full mr-3">
                                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">Easy Setup</p>
                                    <p class="text-sm text-gray-600">Ready in short time</p>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -top-6 -right-6 bg-white p-4 rounded-xl shadow-lg" data-aos="fade-up" data-aos-delay="500">
                            <div class="flex items-center">
                                <div class="bg-blue-100 p-3 rounded-full mr-3">
                                    <i class="fas fa-headset text-blue-500 text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">24/7 Support</p>
                                    <p class="text-sm text-gray-600">We're here to help</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trusted By Section -->
    <!-- <section class="py-8 bg-gray-50 border-t border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 text-sm mb-6">TRUSTED BY SCHOOLS & BUSINESSES ACROSS INDIA</p>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-8 items-center">
                <img src="https://via.placeholder.com/150x60?text=School+1" alt="Client Logo" class="h-12 w-auto mx-auto opacity-60 hover:opacity-100 transition duration-300">
                <img src="https://via.placeholder.com/150x60?text=Store+2" alt="Client Logo" class="h-12 w-auto mx-auto opacity-60 hover:opacity-100 transition duration-300">
                <img src="https://via.placeholder.com/150x60?text=School+3" alt="Client Logo" class="h-12 w-auto mx-auto opacity-60 hover:opacity-100 transition duration-300">
                <img src="https://via.placeholder.com/150x60?text=Store+4" alt="Client Logo" class="h-12 w-auto mx-auto opacity-60 hover:opacity-100 transition duration-300">
                <img src="https://via.placeholder.com/150x60?text=School+5" alt="Client Logo" class="h-12 w-auto mx-auto opacity-60 hover:opacity-100 transition duration-300">
            </div>
        </div>
    </section> -->

    <!-- Video Demo Section -->
    <section id="demo" class="py-16 bg-white">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">See Dulify <span class="primary-text">in Action</span></h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">Watch our 1-minute demo to see how Dulify can transform your school or business operations</p>
        </div>
        
        <div class="relative overflow-hidden shadow-2xl w-full" data-aos="fade-up" data-aos-delay="200">
            <!-- 16:9 Aspect Ratio Container -->
            <div class="relative pb-[56.25%] bg-gray-200">
                <!-- Vimeo iframe embed -->
                <iframe 
                    src="https://player.vimeo.com/video/1092995456?h=2d6ad56e66&title=0&byline=0&portrait=0&badge=0&autopause=0&player_id=0&app_id=58479" 
                    class="absolute top-0 left-0 w-full h-full"
                    frameborder="0" 
                    allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share" 
                    title="Dulify"
                    allowfullscreen>
                </iframe>
            </div>
        </div>
    </div>
</section>

<!-- Load Vimeo player API -->
<script src="https://player.vimeo.com/api/player.js"></script>


                <div class="absolute -bottom-4 -right-4 bg-white p-2 rounded-lg shadow-md">
                    <div class="bg-green-500 text-white px-3 py-1 rounded-md text-sm font-medium">
                        <i class="fas fa-bolt mr-1"></i> NEW
                    </div>
                </div>
            </div>
            
            <div class="mt-12 grid md:grid-cols-3 gap-8">
                <div class="bg-gray-50 p-6 rounded-xl" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-green-500 text-3xl mb-4">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Save 10+ Hours Weekly</h3>
                    <p class="text-gray-600">Automate attendance, homework tracking, and inventory management to focus on what matters.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-xl" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-green-500 text-3xl mb-4">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Affordable for All</h3>
                    <p class="text-gray-600">Starting at Low cost. Special discounts for rural institutions.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-xl" data-aos="fade-up" data-aos-delay="300">
                    <div class="text-green-500 text-3xl mb-4">
                        <i class="fas fa-language"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Bilingual Support</h3>
                    <p class="text-gray-600">Full Hindi/English interface with regional language support coming soon.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Solutions for Schools Section -->
    <section class="py-20 school-pattern relative overflow-hidden">
        <div class="custom-shape-divider-top">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
            </svg>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="md:flex items-center">
                <div class="md:w-1/2 mb-10 md:mb-0" data-aos="fade-right">
                    <div class="bg-white bg-opacity-90 p-8 rounded-xl shadow-lg">
                        <div class="inline-block bg-green-100 text-green-800 px-4 py-1 rounded-full text-sm font-medium mb-4">
                            <i class="fas fa-school mr-1"></i> For Educators
                        </div>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">Modern Solutions for <span class="primary-text">Small Schools</span></h2>
                        <p class="text-gray-600 mb-6">We understand the challenges of running a small school with limited resources. Our tools are designed specifically for Indian schools with:</p>
                        
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start">
                                <div class="bg-green-100 p-1 rounded-full mr-3 mt-1">
                                    <i class="fas fa-check text-green-500 text-xs"></i>
                                </div>
                                <span class="text-gray-700">No computer lab required - works on any smartphone</span>
                            </li>
                            <li class="flex items-start">
                                <div class="bg-green-100 p-1 rounded-full mr-3 mt-1">
                                    <i class="fas fa-check text-green-500 text-xs"></i>
                                </div>
                                <span class="text-gray-700">Automated attendance with SMS alerts to parents</span>
                            </li>
                            <li class="flex items-start">
                                <div class="bg-green-100 p-1 rounded-full mr-3 mt-1">
                                    <i class="fas fa-check text-green-500 text-xs"></i>
                                </div>
                                <span class="text-gray-700">Digital homework submission with plagiarism check</span>
                            </li>
                            <li class="flex items-start">
                                <div class="bg-green-100 p-1 rounded-full mr-3 mt-1">
                                    <i class="fas fa-check text-green-500 text-xs"></i>
                                </div>
                                <span class="text-gray-700">Exam result generation with automatic report cards</span>
                            </li>
                        </ul>
                        
                        <a href="services.php?type=school" class="btn-primary hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition duration-300 inline-flex items-center">
                            <i class="fas fa-chalkboard-teacher mr-2"></i> School Solutions
                        </a>
                    </div>
                </div>
                <div class="md:w-1/2 md:pl-12" data-aos="fade-left">
                    <div class="relative">
                        <img src="img\6144340.jpg" alt="School Solutions" class="w-full max-w-md mx-auto rounded-xl shadow-lg floating">
                        
                        <div class="absolute -bottom-8 -right-8 bg-gradient-to-br from-white to-green-50 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-transform duration-300" data-aos="fade-up" data-aos-delay="300">
                            <div class="flex items-center">
                                <div class="bg-blue-100 p-3 rounded-full mr-4 shadow-inner">
                                    <i class="fas fa-chart-line text-blue-500 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-green-600">85%</p>
                                    <p class="text-sm text-gray-600 font-medium">Schools see improvement in parent engagement</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="absolute -top-19 -left-8 bg-gradient-to-br from-white to-green-50 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-transform duration-300" data-aos="fade-up" data-aos-delay="500">
                            <div class="flex items-center">
                                <div class="bg-yellow-100 p-3 rounded-full mr-4 shadow-inner">
                                    <i class="fas fa-clock text-yellow-500 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-green-600">12 hrs</p>
                                    <p class="text-sm text-gray-600 font-medium">Average weekly time saved by teachers</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Solutions for Businesses Section -->
    <section class="py-20 business-pattern relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="md:flex items-center flex-row-reverse">
                <div class="md:w-1/2 mb-10 md:mb-0" data-aos="fade-left">
                    <div class="bg-white bg-opacity-90 p-8 rounded-xl shadow-lg">
                        <div class="inline-block bg-yellow-100 text-yellow-800 px-4 py-1 rounded-full text-sm font-medium mb-4">
                            <i class="fas fa-store mr-1"></i> For Shop Owners
                        </div>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">Smart Tools for <span class="secondary-text">Local Businesses</span></h2>
                        <p class="text-gray-600 mb-6">Whether you run a kirana store, boutique, or small restaurant, Dulify helps you compete with modern retailers:</p>
                        
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start">
                                <div class="bg-yellow-100 p-1 rounded-full mr-3 mt-1">
                                    <i class="fas fa-check text-yellow-500 text-xs"></i>
                                </div>
                                <span class="text-gray-700">Simple inventory management with low-stock alerts</span>
                            </li>
                            <li class="flex items-start">
                                <div class="bg-yellow-100 p-1 rounded-full mr-3 mt-1">
                                    <i class="fas fa-check text-yellow-500 text-xs"></i>
                                </div>
                                <span class="text-gray-700">Customer loyalty programs with digital punch cards</span>
                            </li>
                            <li class="flex items-start">
                                <div class="bg-yellow-100 p-1 rounded-full mr-3 mt-1">
                                    <i class="fas fa-check text-yellow-500 text-xs"></i>
                                </div>
                                <span class="text-gray-700">WhatsApp integration for order notifications</span>
                            </li>
                            <li class="flex items-start">
                                <div class="bg-yellow-100 p-1 rounded-full mr-3 mt-1">
                                    <i class="fas fa-check text-yellow-500 text-xs"></i>
                                </div>
                                <span class="text-gray-700">Daily sales reports with profit/loss calculations</span>
                            </li>
                        </ul>
                        
                        <a href="services.php?type=business" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg font-medium transition duration-300 inline-flex items-center">
                            <i class="fas fa-store-alt mr-2"></i> Business Solutions
                        </a>
                    </div>
                </div>
                <div class="md:w-1/2 md:pr-12" data-aos="fade-right">
                    <div class="relative">
                        <img src="https://illustrations.popsy.co/amber/sales.svg" alt="Business Solutions" class="w-full max-w-md mx-auto rounded-xl shadow-lg floating">
                        
                        <div class="absolute -bottom-8 -left-8 bg-white p-6 rounded-xl shadow-lg" data-aos="fade-up" data-aos-delay="300">
                            <div class="flex items-center">
                                <div class="bg-green-100 p-3 rounded-full mr-4">
                                    <i class="fas fa-rupee-sign text-green-500 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-800">30%</p>
                                    <p class="text-sm text-gray-600">Average increase in repeat customers</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="absolute -top-8 -right-8 bg-white p-6 rounded-xl shadow-lg" data-aos="fade-up" data-aos-delay="500">
                            <div class="flex items-center">
                                <div class="bg-purple-100 p-3 rounded-full mr-4">
                                    <i class="fas fa-boxes text-purple-500 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-800">90%</p>
                                    <p class="text-sm text-gray-600">Reduction in inventory errors</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="custom-shape-divider-bottom">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
            </svg>
        </div>
    </section>

    <!-- Key Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Why <span class="primary-text">Dulify</span> Stands Out</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">Designed specifically schools and businesses with limited tech experience</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card p-8" data-aos="fade-up" data-aos-delay="100">
                    <div class="primary-text text-4xl mb-6">
                        <!-- <i class="fas fa-mobile-alt"></i> -->
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Mobile-First Design</h3>
                    <p class="text-gray-600 mb-4">Works perfectly on any smartphone—no computer required. Our interface is optimized for low-end devices and slow networks.</p>
                    <div class="flex items-center text-sm text-green-500 font-medium">
                        <!-- <span>Learn more</span> -->
                        <!-- <i class="fas fa-arrow-right ml-2"></i> -->
                    </div>
                </div>
                
                <!-- Feature 2 -->
                <div class="feature-card p-8" data-aos="fade-up" data-aos-delay="200">
                    <div class="primary-text text-4xl mb-6">
                        <i class="fas fa-language"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Bilingual Interface</h3>
                    <p class="text-gray-600 mb-4">Switch between English and Hindi with one click. We're adding more regional languages based on customer feedback.</p>
                    <div class="flex items-center text-sm text-green-500 font-medium">
                        <!-- <span>Learn more</span> -->
                        <!-- <i class="fas fa-arrow-right ml-2"></i> -->
                    </div>
                </div>
                
                <!-- Feature 3 -->
                <div class="feature-card p-8" data-aos="fade-up" data-aos-delay="300">
                    <div class="primary-text text-4xl mb-6">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Affordable Pricing</h3>
                    <p class="text-gray-600 mb-4">Starting with a less amount. Special discounts for rural schools and women entrepreneurs.</p>
                    <div class="flex items-center text-sm text-green-500 font-medium">
                        <!-- <span>See pricing</span> -->
                        <!-- <i class="fas fa-arrow-right ml-2"></i> -->
                    </div>
                </div>
                
                <!-- Feature 4 -->
                <div class="feature-card p-8" data-aos="fade-up" data-aos-delay="100">
                    <div class="primary-text text-4xl mb-6">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Local Support</h3>
                    <p class="text-gray-600 mb-4">Get help in Hindi or English via WhatsApp, phone, or email. Our support team understands local challenges.</p>
                    <div class="flex items-center text-sm text-green-500 font-medium">
                        <!-- <span>Contact support</span> -->
                        <!-- <i class="fas fa-arrow-right ml-2"></i> -->
                    </div>
                </div>
                
                <!-- Feature 5 -->
                <div class="feature-card p-8" data-aos="fade-up" data-aos-delay="200">
                    <div class="primary-text text-4xl mb-6">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Quick Setup</h3>
                    <p class="text-gray-600 mb-4">Get started in 15 minutes with our guided onboarding. We'll even help migrate your existing data for free.</p>
                    <div class="flex items-center text-sm text-green-500 font-medium">
                        <!-- <span>See how it works</span> -->
                        <!-- <i class="fas fa-arrow-right ml-2"></i> -->
                    </div>
                </div>
                
                <!-- Feature 6 -->
                <div class="feature-card p-8" data-aos="fade-up" data-aos-delay="300">
                    <div class="primary-text text-4xl mb-6">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Data Security</h3>
                    <p class="text-gray-600 mb-4">Your data is encrypted and stored securely in India. Regular backups ensure you never lose important information.</p>
                    <div class="flex items-center text-sm text-green-500 font-medium">
                        <!-- <span>Security details</span> -->
                        <!-- <i class="fas fa-arrow-right ml-2"></i> -->
                    </div>
                </div>
            </div>
            
            <!-- <div class="mt-16 text-center" data-aos="fade-up">
                <a href="features.php" class="btn-primary hover:bg-green-600 text-white px-8 py-4 rounded-lg font-medium transition duration-300 inline-flex items-center">
                    <i class="fas fa-list-ul mr-2"></i> See All Features
                </a>
            </div> -->
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Getting Started is <span class="primary-text">Easy</span></h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">Just follow these simple steps to transform your school or business</p>
            </div>
            
            <div class="grid md:grid-cols-4 gap-8 relative">
                <!-- Timeline connector -->
                <div class="hidden md:block absolute top-16 left-1/4 right-1/4 h-1 bg-green-200 z-0"></div>
                <div class="hidden md:block absolute top-16 left-3/4 right-1/4 h-1 bg-green-200 z-0"></div>
                
                <!-- Step 1 -->
                <div class="relative z-10" data-aos="fade-up" data-aos-delay="100">
                    <div class="bg-white w-20 h-20 rounded-full flex items-center justify-center shadow-md mx-auto mb-4 primary-bg">
                        <span class="text-white text-2xl font-bold">1</span>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-md text-center h-full">
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">Sign Up</h3>
                        <p class="text-gray-600">Create your free account in 2 minutes. Order your custom website.</p>
                    </div>
                </div>
                
                <!-- Step 2 -->
                <div class="relative z-10" data-aos="fade-up" data-aos-delay="200">
                    <div class="bg-white w-20 h-20 rounded-full flex items-center justify-center shadow-md mx-auto mb-4 primary-bg">
                        <span class="text-white text-2xl font-bold">2</span>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-md text-center h-full">
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">Customize</h3>
                        <p class="text-gray-600">Set up your profile and preferences with our guided wizard.</p>
                    </div>
                </div>
                
                <!-- Step 3 -->
                <div class="relative z-10" data-aos="fade-up" data-aos-delay="300">
                    <div class="bg-white w-20 h-20 rounded-full flex items-center justify-center shadow-md mx-auto mb-4 primary-bg">
                        <span class="text-white text-2xl font-bold">3</span>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-md text-center h-full">
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">Import Data</h3>
                        <p class="text-gray-600">Upload your student lists or inventory with our simple templates.</p>
                    </div>
                </div>
                
                <!-- Step 4 -->
                <div class="relative z-10" data-aos="fade-up" data-aos-delay="400">
                    <div class="bg-white w-20 h-20 rounded-full flex items-center justify-center shadow-md mx-auto mb-4 primary-bg">
                        <span class="text-white text-2xl font-bold">4</span>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-md text-center h-full">
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">Go Live!</h3>
                        <p class="text-gray-600">Start using Dulify with your team. We'll guide you every step.</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-20 text-center" data-aos="fade-up">
                <a href="php/register.php" class="btn-primary hover:bg-green-600 text-white px-8 py-4 rounded-lg font-medium transition duration-400 inline-flex items-center">
                    <i class="fas fa-play-circle mr-2"></i> Getting Started
                </a>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->

            
            <div class="mt-12 text-center" data-aos="fade-up">
                <p class="text-gray-600 mb-4">Want an Custom Websiet for you?</p>
                <a href="#contact" class="text-green-500 hover:text-green-600 font-medium inline-flex items-center">
                    <i class="fas fa-headset mr-2"></i> Talk to our team
                </a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Success <span class="primary-text">Stories</span></h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">Don't just take our word for it - hear from our happy customers</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <?php
                // Fetch active testimonials
                $testimonials = array();
                $sql = "SELECT t.*, u.username, u.business_name, u.business_type, s.name as service_name 
                        FROM testimonials t 
                        JOIN users u ON t.user_id = u.id 
                        JOIN services s ON t.service_id = s.id 
                        WHERE t.status = 'active' 
                        ORDER BY t.created_at DESC LIMIT 3";
                $result = mysqli_query($conn, $sql);
                
                if(mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $business_title = $row['business_name'] ?: ucfirst($row['business_type']);
                        
                        // Generate stars
                        $stars = str_repeat('<i class="fas fa-star text-yellow-400"></i>', $row['rating']);
                        if($row['rating'] < 5) {
                            $stars .= str_repeat('<i class="far fa-star text-yellow-400"></i>', 5 - $row['rating']);
                        }
                        ?>
                        <div class="bg-white p-8 rounded-xl shadow-md" data-aos="fade-up" data-aos-delay="<?php echo ($row['id'] % 3) * 100; ?>">
                            <div class="flex items-center mb-6">
                                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-user text-green-500 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($row['username']); ?></h4>
                                    <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($business_title); ?></p>
                                </div>
                            </div>
                            <p class="text-gray-600 italic mb-6">"<?php echo htmlspecialchars($row['comment']); ?>"</p>
                            <div class="flex items-center justify-between">
                                <div class="text-yellow-400">
                                    <?php echo $stars; ?>
                                </div>
                                <div class="text-xs text-gray-400">
                                    <?php echo htmlspecialchars($row['service_name']); ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    // Default testimonials if none in database
                    $default_testimonials = [
                        [
                            'username' => 'Priya Sharma',
                            'business' => 'Sharma Kirana Store',
                            'comment' => 'Dulify helped us reduce inventory errors by 90% and increased our repeat customers. The WhatsApp order notifications are a game-changer!',
                            'rating' => 5,
                            'service' => 'Inventory Management'
                        ],
                        [
                            'username' => 'Rajesh Patel',
                            'business' => 'Patel Public School',
                            'comment' => 'Our teachers save 15+ hours weekly with automated attendance and homework tracking. Parents love the real-time updates.',
                            'rating' => 5,
                            'service' => 'School Portal'
                        ],
                        [
                            'username' => 'Ananya Gupta',
                            'business' => 'Gupta Boutique',
                            'comment' => 'The customer loyalty program increased our sales by 30%. Easy to use even with limited tech knowledge.',
                            'rating' => 4,
                            'service' => 'Business Solutions'
                        ]
                    ];
                    
                    foreach ($default_testimonials as $i => $testimonial) {
                        ?>
                        <div class="bg-white p-8 rounded-xl shadow-md" data-aos="fade-up" data-aos-delay="<?php echo ($i + 1) * 100; ?>">
                            <div class="flex items-center mb-6">
                                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-user text-green-500 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-semibold text-gray-800"><?php echo $testimonial['username']; ?></h4>
                                    <p class="text-gray-500 text-sm"><?php echo $testimonial['business']; ?></p>
                                </div>
                            </div>
                            <p class="text-gray-600 italic mb-6">"<?php echo $testimonial['comment']; ?>"</p>
                            <div class="flex items-center justify-between">
                                <div class="text-yellow-400">
                                    <?php echo str_repeat('<i class="fas fa-star"></i>', $testimonial['rating']); ?>
                                    <?php if($testimonial['rating'] < 5) echo str_repeat('<i class="far fa-star"></i>', 5 - $testimonial['rating']); ?>
                                </div>
                                <div class="text-xs text-gray-400">
                                    <?php echo $testimonial['service']; ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            
            <!-- <div class="mt-16 text-center" data-aos="fade-up">
                <a href="testimonials.php" class="btn-primary hover:bg-green-600 text-white px-8 py-4 rounded-lg font-medium transition duration-300 inline-flex items-center">
                    <i class="fas fa-book-open mr-2"></i> Read More Success Stories
                </a>
            </div> -->
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-green-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 text-center">
                <div data-aos="fade-up" data-aos-delay="100">
                    <div class="text-4xl md:text-5xl font-bold mb-2">5+</div>
                    <div class="text-lg opacity-90">Services Available</div>
                </div>
                <div data-aos="fade-up" data-aos-delay="200">
                    <div class="text-4xl md:text-5xl font-bold mb-2">95%</div>
                    <div class="text-lg opacity-90">Retention Rate</div>
                </div>
                <div data-aos="fade-up" data-aos-delay="300">
                    <div class="text-4xl md:text-5xl font-bold mb-2">10K+</div>
                    <div class="text-lg opacity-90">Students Managed</div>
                </div>
                <div data-aos="fade-up" data-aos-delay="400">
                    <div class="text-4xl md:text-5xl font-bold mb-2">24/7</div>
                    <div class="text-lg opacity-90">Support Available</div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Frequently Asked <span class="primary-text">Questions</span></h2>
                <p class="text-lg text-gray-600">Can't find what you're looking for? <a href="#contact" class="primary-text hover:underline">Contact our team</a></p>
            </div>
            
            <div class="space-y-4">
                <!-- FAQ Item 1 -->
                <div class="border border-gray-200 rounded-xl overflow-hidden" data-aos="fade-up" data-aos-delay="100">
                    <button class="faq-toggle w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 transition duration-300">
                        <h3 class="text-lg font-medium text-gray-800">Is Dulify suitable for schools with no computer lab?</h3>
                        <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-6 pt-0">
                        <p class="text-gray-600">Absolutely! Dulify is designed specifically for schools with limited tech infrastructure. Our mobile-first platform works perfectly on any smartphone, so teachers and administrators can manage everything from their phones. We even provide training videos in Hindi and English to help staff get comfortable with the system.</p>
                    </div>
                </div>
                
                <!-- FAQ Item 2 -->
                <div class="border border-gray-200 rounded-xl overflow-hidden" data-aos="fade-up" data-aos-delay="200">
                    <button class="faq-toggle w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 transition duration-300">
                        <h3 class="text-lg font-medium text-gray-800">How does the free trial work?</h3>
                        <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-6 pt-0">
                        <p class="text-gray-600">Our 7-day free trial gives you full access to all features of the Growth plan. At the end of the trial period, you can choose a websiet which you want to buy and ypu will be fully refunded if siet stop working. We'll send you reminders before your trial ends so there are no surprises.</p>
                    </div>
                </div>
                
                <!-- FAQ Item 3 -->
                <div class="border border-gray-200 rounded-xl overflow-hidden" data-aos="fade-up" data-aos-delay="300">
                    <button class="faq-toggle w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 transition duration-300">
                        <h3 class="text-lg font-medium text-gray-800">Can I use Dulify for both my school and shop?</h3>
                        <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-6 pt-0">
                        <p class="text-gray-600">Yes! Many of our customers manage both their educational institution and related business (like a school uniform shop or bookstore) with Dulify. We offer special bundled pricing for customers who need both our school and business solutions. Contact our sales team to discuss your specific needs.</p>
                    </div>
                </div>
                
                <!-- FAQ Item 4 -->
                <div class="border border-gray-200 rounded-xl overflow-hidden" data-aos="fade-up" data-aos-delay="400">
                    <button class="faq-toggle w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 transition duration-300">
                        <h3 class="text-lg font-medium text-gray-800">What happens to my data if I cancel my subscription?</h3>
                        <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-6 pt-0">
                        <p class="text-gray-600">We never hold your data hostage. If you choose to cancel, we'll keep your data for 90 days in case you want to reactivate your account. You can also export all your data at any time in common formats (Excel, CSV) for your records. After 90 days, all data is permanently deleted from our servers.</p>
                    </div>
                </div>
                
                <!-- FAQ Item 5 -->
                <div class="border border-gray-200 rounded-xl overflow-hidden" data-aos="fade-up" data-aos-delay="500">
                    <button class="faq-toggle w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 transition duration-300">
                        <h3 class="text-lg font-medium text-gray-800">Do you offer discounts for rural schools or NGOs?</h3>
                        <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-6 pt-0">
                        <p class="text-gray-600">Yes! We're committed to making our solutions accessible to all. We offer special pricing for rural schools, women entrepreneurs, and registered NGOs. Discounts typically range from 20-40% depending on your circumstances. Please contact us with details about your organization to learn more about these programs.</p>
                    </div>
                </div>
            </div>
            
            <!-- <div class="mt-12 text-center" data-aos="fade-up">
                <a href="faq.php" class="text-green-500 hover:text-green-600 font-medium inline-flex items-center">
                    <i class="fas fa-question-circle mr-2"></i> View All FAQs
                </a>
            </div> -->
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-green-500 to-green-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Transform Your School or Business?</h2>
                <p class="text-xl opacity-90 max-w-3xl mx-auto mb-8">Join  educators and entrepreneurs who are saving time and growing their communities with Dulify</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="php/register.php" class="bg-white hover:bg-gray-100 text-green-600 px-8 py-4 rounded-lg font-bold transition duration-300 inline-flex items-center">
                        <i class="fas fa-play mr-2"></i> Start Now
                    </a>
                    <a href="#contact" class="border-2 border-white hover:bg-white hover:bg-opacity-10 px-8 py-4 rounded-lg font-bold transition duration-300 inline-flex items-center">
                        <i class="fas fa-headset mr-2"></i> Talk to Sales
                    </a>
                </div>
                <p class="mt-6 text-sm opacity-80">Join Now • 7-day free trial • Full Refund</p>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="md:flex">
                <div class="md:w-1/2 mb-10 md:mb-0" data-aos="fade-right">
                    <div class="bg-gray-50 p-8 rounded-xl h-full">
                        <h2 class="text-3xl font-bold text-gray-800 mb-6">Let's Get in Touch</h2>
                        <p class="text-gray-600 mb-8">Have questions or want to see a personalized demo? Fill out the form or contact us directly.</p>
                        
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <div class="bg-white p-3 rounded-full shadow-md mr-4">
                                    <i class="fas fa-map-marker-alt text-green-500"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800">Our Office</h4>
                                    <p class="text-gray-600">Pukhrayan, Kanpur Dehat, Uttar Pradesh 209111</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="bg-white p-3 rounded-full shadow-md mr-4">
                                    <i class="fas fa-envelope text-green-500"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800">Email Us</h4>
                                    <p class="text-gray-600">hello@dulify.com</p>
                                    <p class="text-gray-600">support@dulify.com</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="bg-white p-3 rounded-full shadow-md mr-4">
                                    <i class="fas fa-phone-alt text-green-500"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800">Call Us</h4>
                                    <p class="text-gray-600">+91 79915 15802 (Sales)</p>
                                    <p class="text-gray-600">+91 91702 68811 (Support)</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="bg-white p-3 rounded-full shadow-md mr-4">
                                    <i class="fab fa-whatsapp text-green-500"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800">WhatsApp</h4>
                                    <p class="text-gray-600">+91 7991515802</p>
                                    <p class="text-sm text-gray-500">(Typically reply within 1 hour)</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- <div class="mt-8">
                            <h4 class="font-medium text-gray-800 mb-4">Follow Us</h4>
                            <div class="flex space-x-4">
                                <a href="#" class="bg-white hover:bg-green-500 hover:text-white w-10 h-10 rounded-full flex items-center justify-center shadow-md transition duration-300">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="bg-white hover:bg-green-500 hover:text-white w-10 h-10 rounded-full flex items-center justify-center shadow-md transition duration-300">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="bg-white hover:bg-green-500 hover:text-white w-10 h-10 rounded-full flex items-center justify-center shadow-md transition duration-300">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="bg-white hover:bg-green-500 hover:text-white w-10 h-10 rounded-full flex items-center justify-center shadow-md transition duration-300">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="#" class="bg-white hover:bg-green-500 hover:text-white w-10 h-10 rounded-full flex items-center justify-center shadow-md transition duration-300">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            </div>
                        </div> -->
                    </div>
                </div>
                <div class="md:w-1/2 md:pl-12" data-aos="fade-left">
                    <div class="bg-white p-8 rounded-xl shadow-md">
                        <h3 class="text-2xl font-semibold text-gray-800 mb-6">Send Us a Message</h3>
                        
                        <?php if(isset($_GET['contact_success'])): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
                            <strong class="font-bold">Success!</strong>
                            <span class="block sm:inline">Your message has been sent successfully. We'll get back to you soon!</span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(isset($_GET['contact_error']) && !empty($_GET['contact_error'])): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline"><?php echo htmlspecialchars($_GET['contact_error']); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <form class="space-y-6" action="php/contact.php" method="POST">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Your Name *</label>
                                <input type="text" id="name" name="name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition duration-300" placeholder="Enter your name" required>
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                    <input type="email" id="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition duration-300" placeholder="your@email.com" required>
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                    <input type="tel" id="phone" name="phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition duration-300" placeholder="+91 98765 43210">
                                </div>
                            </div>
                            
                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                                <select id="subject" name="subject" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition duration-300" required>
                                    <option value="" disabled selected>Select a subject</option>
                                    <option value="Sales Inquiry">Sales Inquiry</option>
                                    <option value="Support Request">Support Request</option>
                                    <option value="Partnership">Partnership</option>
                                    <option value="Feedback">Feedback</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                                <textarea id="message" name="message" rows="5" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition duration-300" placeholder="How can we help you?" required></textarea>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="consent" name="consent" class="h-4 w-4 text-green-500 focus:ring-green-500 border-gray-300 rounded" required>
                                <label for="consent" class="ml-2 block text-sm text-gray-700">
                                    I agree to Dulify's <a href="privacy.php" class="text-green-500 hover:underline">Privacy Policy</a> and <a href="terms.php" class="text-green-500 hover:underline">Terms of Service</a>
                                </label>
                            </div>
                            
                            <button type="submit" class="w-full btn-primary hover:bg-green-600 text-white px-6 py-4 rounded-lg font-bold transition duration-300 flex items-center justify-center">
                                <i class="fas fa-paper-plane mr-2"></i> Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <!-- <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-5 gap-8 mb-12">
                <div class="md:col-span-2">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-school text-green-400 text-3xl mr-2"></i>
                        <span class="text-xl font-bold">Dulify</span>
                    </div>
                    <p class="text-gray-400 mb-6">Empowering local schools and small businesses with affordable digital solutions.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="bg-gray-800 hover:bg-gray-700 w-10 h-10 rounded-full flex items-center justify-center transition duration-300">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="bg-gray-800 hover:bg-gray-700 w-10 h-10 rounded-full flex items-center justify-center transition duration-300">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="bg-gray-800 hover:bg-gray-700 w-10 h-10 rounded-full flex items-center justify-center transition duration-300">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="bg-gray-800 hover:bg-gray-700 w-10 h-10 rounded-full flex items-center justify-center transition duration-300">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="bg-gray-800 hover:bg-gray-700 w-10 h-10 rounded-full flex items-center justify-center transition duration-300">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">For Schools</h4>
                    <ul class="space-y-2">
                        <li><a href="services.php?type=school" class="text-gray-400 hover:text-white transition duration-300">School Portal</a></li>
                        <li><a href="features.php#attendance" class="text-gray-400 hover:text-white transition duration-300">Attendance System</a></li>
                        <li><a href="features.php#homework" class="text-gray-400 hover:text-white transition duration-300">Homework Tracking</a></li>
                        <li><a href="features.php#exams" class="text-gray-400 hover:text-white transition duration-300">Exam Management</a></li>
                        <li><a href="pricing.php?type=school" class="text-gray-400 hover:text-white transition duration-300">School Pricing</a></li>
                    </ul>
                </div>
                
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">For Businesses</h4>
                    <ul class="space-y-2">
                        <li><a href="services.php?type=business" class="text-gray-400 hover:text-white transition duration-300">Inventory Management</a></li>
                        <li><a href="features.php#sales" class="text-gray-400 hover:text-white transition duration-300">Sales Tracking</a></li>
                        <li><a href="features.php#customers" class="text-gray-400 hover:text-white transition duration-300">Customer Loyalty</a></li>
                        <li><a href="features.php#reports" class="text-gray-400 hover:text-white transition duration-300">Business Reports</a></li>
                        <li><a href="pricing.php?type=business" class="text-gray-400 hover:text-white transition duration-300">Business Pricing</a></li>
                    </ul>
                </div> -->
                
                <!-- <div>
                    <h4 class="text-lg font-semibold mb-4">Company</h4>
                    <ul class="space-y-2">
                        <li><a href="about.php" class="text-gray-400 hover:text-white transition duration-300">About Us</a></li>
                        <li><a href="blog.php" class="text-gray-400 hover:text-white transition duration-300">Blog</a></li>
                        <li><a href="careers.php" class="text-gray-400 hover:text-white transition duration-300">Careers</a></li>
                        <li><a href="press.php" class="text-gray-400 hover:text-white transition duration-300">Press</a></li>
                        <li><a href="partners.php" class="text-gray-400 hover:text-white transition duration-300">Partners</a></li>
                    </ul>
                </div>
            </div>
             -->
            <!-- <div class="border-t border-gray-800 pt-8">
                <div class="md:flex md:items-center md:justify-between">
                    <div class="text-center md:text-left mb-4 md:mb-0">
                        <p class="text-sm text-gray-400">&copy; 
                            <?php echo date("Y"); ?> 
                            Dulify Technologies Pvt Ltd. All rights reserved.</p>
                    </div>
                    <div class="flex justify-center md:justify-end space-x-6">
                        <a href="privacy.php" class="text-sm text-gray-400 hover:text-white transition duration-300">Privacy Policy</a>
                        <a href="terms.php" class="text-sm text-gray-400 hover:text-white transition duration-300">Terms of Service</a>
                        <a href="cookies.php" class="text-sm text-gray-400 hover:text-white transition duration-300">Cookie Policy</a>
                        <a href="sitemap.php" class="text-sm text-gray-400 hover:text-white transition duration-300">Sitemap</a>
                    </div>
                </div>
            </div>
        </div>
    </footer> -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-school text-green-400 text-3xl mr-2"></i>
                        <span class="text-xl font-bold">Dulify</span>
                    </div>
                    <p class="text-gray-400">Empowering local schools and small businesses with affordable digital solutions.</p>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-400 hover:text-white transition duration-300">Home</a></li>
                        <li><a href="services.php" class="text-gray-400 hover:text-white transition duration-300">Services</a></li>
                        <li><a href="about.php" class="text-gray-400 hover:text-white transition duration-300">About Us</a></li>
                        <li><a href="#testimonials" class="text-gray-400 hover:text-white transition duration-300">Success Stories</a></li>
                        <li><a href="#contact" class="text-gray-400 hover:text-white transition duration-300">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Solutions</h4>
                    <ul class="space-y-2">
                        <li><a href="services.php" class="text-gray-400 hover:text-white transition duration-300">For Small Schools</a></li>
                        <li><a href="services.php" class="text-gray-400 hover:text-white transition duration-300">For Local Businesses</a></li>
                        <li><a href="services.php" class="text-gray-400 hover:text-white transition duration-300">For Community Groups</a></li>
                        <li><a href="services.php" class="text-gray-400 hover:text-white transition duration-300">Pricing Plans</a></li>
                        <li><a href="services.php" class="text-gray-400 hover:text-white transition duration-300">Implementation Process</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact Us</h4>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt text-green-400 mt-1 mr-2"></i>
                            <span class="text-gray-400">Pukhrayan, Kanpur Dehat, Uttar Pradesh 209111</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt text-green-400 mr-2"></i>
                            <span class="text-gray-400">+91 7991515802</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope text-green-400 mr-2"></i>
                            <span class="text-gray-400">hello@dulify.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; <?php echo date("Y"); ?> Dulify. All rights reserved. <a href="privacy.php" class="hover:text-white">Privacy Policy</a> | <a href="terms.php" class="hover:text-white">Terms of Service</a></p>
            </div>
        </div>
    </footer>
    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-8 right-8 bg-green-500 hover:bg-green-600 text-white w-12 h-12 rounded-full flex items-center justify-center shadow-lg transition duration-300 opacity-0 invisible">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS animation library
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });

        // Mobile menu toggle
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            menuBtn.innerHTML = mobileMenu.classList.contains('hidden') ? 
                '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>' :
                '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
        });

        // FAQ toggle functionality
        document.querySelectorAll('.faq-toggle').forEach(button => {
            button.addEventListener('click', () => {
                const content = button.nextElementSibling;
                const icon = button.querySelector('i');
                
                // Toggle content
                content.classList.toggle('hidden');
                
                // Rotate icon
                if (content.classList.contains('hidden')) {
                    icon.classList.remove('transform', 'rotate-180');
                } else {
                    icon.classList.add('transform', 'rotate-180');
                }
            });
        });

        // Back to top button
        const backToTopButton = document.getElementById('back-to-top');
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

        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 50) {
                navbar.classList.add('shadow-lg', 'bg-white', 'bg-opacity-95');
                navbar.classList.remove('bg-opacity-100');
            } else {
                navbar.classList.remove('shadow-lg', 'bg-opacity-95');
                navbar.classList.add('bg-opacity-100');
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                if (this.getAttribute('onclick') || this.getAttribute('href').startsWith('#!')) return;
                
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                    
                    // Close mobile menu if open
                    if (!mobileMenu.classList.contains('hidden')) {
                        mobileMenu.classList.add('hidden');
                        menuBtn.innerHTML = '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>';
                    }
                }
            });
        });

        // Animation for elements when they come into view
        const animateOnScroll = () => {
            const elements = document.querySelectorAll('[data-aos]');
            
            elements.forEach(element => {
                const elementPosition = element.getBoundingClientRect().top;
                const screenPosition = window.innerHeight / 1.2;
                
                if (elementPosition < screenPosition) {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }
            });
        };

        window.addEventListener('scroll', animateOnScroll);
        animateOnScroll(); // Run once on page load

        // Video modal
        const videoModal = document.getElementById('video-modal');
        const videoModalClose = document.getElementById('video-modal-close');
        const videoModalIframe = document.getElementById('video-modal-iframe');
        
        document.querySelectorAll('[data-video]').forEach(button => {
            button.addEventListener('click', () => {
                const videoUrl = button.getAttribute('data-video');
                videoModalIframe.src = videoUrl;
                videoModal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            });
        });
        
        videoModalClose.addEventListener('click', () => {
            videoModal.classList.add('hidden');
            videoModalIframe.src = '';
            document.body.classList.remove('overflow-hidden');
        });
        
        videoModal.addEventListener('click', (e) => {
            if (e.target === videoModal) {
                videoModal.classList.add('hidden');
                videoModalIframe.src = '';
                document.body.classList.remove('overflow-hidden');
            }
        });
        </script>
        <script>
    // Custom play button functionality
    document.getElementById('video-play-button').addEventListener('click', function() {
        const video = document.getElementById('demo-video');
        video.play();
        this.style.display = 'none'; // Hide play button after click
        
        // Show button again when video ends
        video.addEventListener('ended', function() {
            document.getElementById('video-play-button').style.display = 'flex';
        });
    });
</script>

</body>
</html>