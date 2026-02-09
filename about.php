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
    <title>About Dulify - Digital Solutions for Local Institutions</title>
    <meta name="description" content="Learn about Dulify's mission to provide affordable digital solutions for small schools and local businesses. Discover our story, values, and commitment to bridging the technology gap.">
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
        .card {
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .service-card:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: #4CAF50;
        }
        .mobile-menu {
            transition: all 0.3s ease;
        }
        .mission-item {
            position: relative;
            padding-left: 2rem;
        }
        .mission-item:before {
            content: "";
            position: absolute;
            left: 0;
            top: 0.5rem;
            width: 1.2rem;
            height: 1.2rem;
            background-color: #4CAF50;
            border-radius: 50%;
        }
        .timeline-item {
            position: relative;
            padding-left: 2.5rem;
            margin-bottom: 2rem;
        }
        .timeline-item:before {
            content: "";
            position: absolute;
            left: 0.65rem;
            top: 0;
            width: 2px;
            height: 100%;
            background-color: #4CAF50;
        }
        .timeline-dot {
            position: absolute;
            left: 0;
            top: 0;
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 50%;
            background-color: #4CAF50;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.7rem;
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
                    <a href="about.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium active">About</a>
                    <a href="index.php#features" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Features</a>
                    <a href="index.php#testimonials" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Testimonials</a>
                    <a href="index.php#contact" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Contact</a>
                    <?php if($loggedIn): ?>
                        <a href="dashboard.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-300">Dashboard</a>
                        <a href="php/logout.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition duration-300">Logout</a>
                    <?php else: ?>
                        <a href="php/login.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-300">Login</a>
                        <a href="php/register.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition duration-300">Sign Up</a>
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
                <a href="index.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Home</a>
                <a href="services.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Services</a>
                <a href="about.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 active">About</a>
                <a href="index.php#features" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Features</a>
                <a href="index.php#testimonials" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Testimonials</a>
                <a href="index.php#contact" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Contact</a>
                <?php if($loggedIn): ?>
                    <a href="dashboard.php" class="block px-3 py-2 rounded-md text-base font-medium text-white bg-green-500 hover:bg-green-600">Dashboard</a>
                    <a href="php/logout.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">Logout</a>
                <?php else: ?>
                    <a href="php/login.php" class="block px-3 py-2 rounded-md text-base font-medium text-white bg-green-500 hover:bg-green-600">Login</a>
                    <a href="php/register.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- About Hero Section -->
    <section class="pt-32 pb-20 bg-gradient-to-r from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Innovating for <span class="primary-text">Tomorrow's Challenges</span></h1>
            <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto">We're a passionate team of innovators building digital solutions that empower businesses to thrive in the digital age.</p>
        </div>
    </section>

    <!-- Our Story Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Our <span class="primary-text">Journey</span> Begins</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">From a bold idea to an emerging innovator in the digital solutions space</p>
                <div class="w-20 h-1 primary-bg mx-auto mt-4"></div>
            </div>
            
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="timeline-item">
                        <div class="timeline-dot"><i class="fas fa-lightbulb"></i></div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo date('Y')-1; ?> - The Spark</h3>
                        <p class="text-gray-600">Our founders identified a critical gap in the market: businesses needed more intuitive, flexible digital solutions that could grow with them without requiring enterprise-level budgets.</p>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-dot"><i class="fas fa-users"></i></div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo date('Y')-1; ?> - Team Formation</h3>
                        <p class="text-gray-600">We assembled our core team of developers, designers, and business strategists, united by a shared vision to democratize access to powerful digital tools.</p>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-dot"><i class="fas fa-code"></i></div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo date('Y'); ?> Q1 - MVP Development</h3>
                        <p class="text-gray-600">After months of research and planning, we began building our minimum viable product, focusing on core features that would deliver immediate value to our early adopters.</p>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-dot"><i class="fas fa-rocket"></i></div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo date('Y'); ?> Q2 - Official Launch</h3>
                        <p class="text-gray-600">We proudly launched the Dulify platform, offering a suite of integrated digital solutions designed specifically for growing businesses and organizations.</p>
                    </div>
                </div>
                
                <div>
                    <img src="img\journey.png" alt="Dulify Journey" class="w-full rounded-lg shadow-xl">
                    <div class="mt-6 bg-green-50 p-6 rounded-lg">
                        <h3 class="text-xl font-semibold text-gray-800 mb-3 primary-text">Where We're Headed</h3>
                        <p class="text-gray-600">We're just getting started! Our roadmap includes expanding our service offerings, building a community of users who can learn from each other, and developing AI-powered features to help businesses make smarter decisions with their data.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Our <span class="primary-text">Purpose</span> & Vision</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">The core principles driving our innovation and growth</p>
                <div class="w-20 h-1 primary-bg mx-auto mt-4"></div>
            </div>
            
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">We believe that:</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 primary-bg rounded-full flex items-center justify-center mt-1">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                            <p class="ml-3 text-gray-600"><span class="font-semibold">Technology should be accessible to all businesses.</span> Powerful digital tools shouldn't be limited to enterprises with massive budgets.</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 primary-bg rounded-full flex items-center justify-center mt-1">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                            <p class="ml-3 text-gray-600"><span class="font-semibold">Innovation thrives on simplicity.</span> Our solutions are designed to be powerful yet intuitive, eliminating unnecessary complexity.</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 primary-bg rounded-full flex items-center justify-center mt-1">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                            <p class="ml-3 text-gray-600"><span class="font-semibold">Growth should be data-driven.</span> We empower organizations to make informed decisions through actionable insights and analytics.</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 primary-bg rounded-full flex items-center justify-center mt-1">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                            <p class="ml-3 text-gray-600"><span class="font-semibold">Success is measured by our customers' success.</span> We're committed to being a true partner in our clients' growth journey, not just another vendor.</p>
                        </li>
                    </ul>
                </div>
                
                <div>
                    <img src="img\mission.png" alt="Mission and Vision" class="w-full rounded-lg shadow-xl">
                </div>
            </div>
        </div>
    </section>

    <!-- Our Approach Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Our <span class="primary-text">Methodology</span></h2>
                <p class="text-gray-600 max-w-2xl mx-auto">How we deliver exceptional value to our clients</p>
                <div class="w-20 h-1 primary-bg mx-auto mt-4"></div>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                    <div class="h-14 w-14 primary-bg rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-bolt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Agile Innovation</h3>
                    <p class="text-gray-600">We embrace rapid iteration and continuous improvement, allowing us to quickly adapt our solutions to evolving market needs and customer feedback.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                    <div class="h-14 w-14 primary-bg rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-chart-line text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Data-Driven Design</h3>
                    <p class="text-gray-600">Every feature we build is informed by real user data and behavior analytics, ensuring our solutions solve genuine problems and deliver measurable ROI.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                    <div class="h-14 w-14 primary-bg rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Customer Collaboration</h3>
                    <p class="text-gray-600">We believe the best solutions emerge from close partnership with our clients. Your team's insights directly influence our product roadmap and feature development.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Growth Metrics Section -->
    <section class="py-16 primary-bg text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Our <span class="text-yellow-300">Trajectory</span></h2>
                <p class="text-xl max-w-2xl mx-auto">Early milestones on our path to growth</p>
            </div>
            
            <div class="grid md:grid-cols-4 gap-8 text-center">
                <div class="bg-white bg-opacity-10 p-6 rounded-lg backdrop-filter backdrop-blur-sm">
                    <div class="text-4xl font-bold mb-2">15+</div>
                    <div class="text-lg">Beta Clients</div>
                </div>
                
                <div class="bg-white bg-opacity-10 p-6 rounded-lg backdrop-filter backdrop-blur-sm">
                    <div class="text-4xl font-bold mb-2">3</div>
                    <div class="text-lg">Industry Verticals</div>
                </div>
                
                <div class="bg-white bg-opacity-10 p-6 rounded-lg backdrop-filter backdrop-blur-sm">
                    <div class="text-4xl font-bold mb-2">92%</div>
                    <div class="text-lg">User Satisfaction</div>
                </div>
                
                <div class="bg-white bg-opacity-10 p-6 rounded-lg backdrop-filter backdrop-blur-sm">
                    <div class="text-4xl font-bold mb-2">500+</div>
                    <div class="text-lg">Active Users</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Our <span class="primary-text">Founding Team</span></h2>
                <p class="text-gray-600 max-w-2xl mx-auto">The visionaries bringing Dulify to life</p>
                <div class="w-20 h-1 primary-bg mx-auto mt-4"></div>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md text-center hover:shadow-lg transition-shadow">
                    <div class="w-32 h-32 rounded-full overflow-hidden mx-auto mb-4 border-4 border-green-100">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="CEO" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-1">Divyanshu Gupta</h3>
                    <p class="primary-text mb-3">CEO & Founder</p>
                    <p class="text-gray-600 mb-4">Full-stack developer and Student at KIET College. Passionate about democratizing technology access.</p>
                    <div class="flex justify-center space-x-3">
                        <a href="https://www.linkedin.com/in/divyanshu-gupta-871aa8327 " class="text-gray-400 hover:primary-text transition-colors"><i class="fab fa-linkedin"></i></a>
                        <a href="https://github.com/improperboy" class="text-gray-400 hover:primary-text transition-colors"><i class="fab fa-github"></i></a>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-md text-center hover:shadow-lg transition-shadow">
                    <div class="w-32 h-32 rounded-full overflow-hidden mx-auto mb-4 border-4 border-green-100">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="CTO" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-1">Omji Vaishya</h3>
                    <p class="primary-text mb-3">CTO & Co-Founder</p>
                    <p class="text-gray-600 mb-4">Full-stack developer and Student at IIIT Manipur. Specializes in building scalable, user-friendly platforms with cutting-edge technology.</p>
                    <div class="flex justify-center space-x-3">
                        <a href="https://www.linkedin.com/in/omji-vaishy-b73228326 " class="text-gray-400 hover:primary-text transition-colors"><i class="fab fa-linkedin"></i></a>
                        <a href="https://github.com/omjivaishy" class="text-gray-400 hover:primary-text transition-colors"><i class="fab fa-github"></i></a>
                    </div>
                </div>
                
                <!-- <div class="bg-white p-6 rounded-lg shadow-md text-center hover:shadow-lg transition-shadow">
                    <div class="w-32 h-32 rounded-full overflow-hidden mx-auto mb-4 border-4 border-green-100">
                        <img src="https://randomuser.me/api/portraits/men/67.jpg" alt="Design Lead" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-1">Marcus Chen</h3>
                    <p class="primary-text mb-3">Head of Design & UX</p>
                    <p class="text-gray-600 mb-4">Award-winning designer with a background in creating intuitive user experiences. Believes in design that empowers users and makes complex tasks feel simple.</p>
                    <div class="flex justify-center space-x-3">
                        <a href="#" class="text-gray-400 hover:primary-text transition-colors"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="text-gray-400 hover:primary-text transition-colors"><i class="fab fa-dribbble"></i></a>
                    </div>
                </div> -->
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="bg-gradient-to-r from-green-50 to-green-100 p-8 md:p-12 rounded-xl">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">Ready to Experience the Dulify Advantage?</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto mb-8">Join the growing community of small institutions transforming their operations with our tailored digital solutions.</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="services.php" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition duration-300">Explore Solutions</a>
                    <a href="php/register.php" class="border-2 border-green-500 hover:bg-green-500 hover:text-white text-green-500 px-6 py-3 rounded-lg font-medium transition duration-300">Start Free Trial</a>
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
                        <li><a href="index.php#testimonials" class="text-gray-400 hover:text-white transition duration-300">Success Stories</a></li>
                        <li><a href="index.php#contact" class="text-gray-400 hover:text-white transition duration-300">Contact</a></li>
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

    <script>
        // Mobile menu toggle
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
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
                }
            });
        });
    </script>
</body>
</html>