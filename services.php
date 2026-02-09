<?php
// Start session
session_start();

// Include database connection
require_once 'includes/config.php';

// Check if user is logged in
$loggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Check if service ID is provided in the URL
$serviceDetails = null;
if(isset($_GET['id']) && !empty($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT * FROM services WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) > 0) {
        $serviceDetails = mysqli_fetch_assoc($result);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $serviceDetails ? $serviceDetails["name"] . " - Dulify" : "Our Services - Dulify"; ?></title>
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
        .service-card:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: #4CAF50;
        }
        .active {
            color: #4CAF50;
            font-weight: 500;
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
                    <a href="services.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium active">Services</a>
                    <a href="index.php#features" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Features</a>
                    <a href="index.php#testimonials" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Sucess story</a>
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
                <a href="about.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">About</a>
                <a href="services.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 active">Services</a>
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

    <?php if($serviceDetails): ?>
    <!-- Single Service Details -->
    <section class="pt-32 pb-16 md:pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row gap-12">
                <div class="md:w-2/5">
                    <div class="bg-white p-4 rounded-xl shadow-lg overflow-hidden">
                        <?php if(!empty($serviceDetails["image"])): ?>
                            <img src="img/services/<?php echo $serviceDetails["image"]; ?>" alt="<?php echo $serviceDetails["name"]; ?>" class="w-full h-auto rounded-lg transition-transform duration-500 hover:scale-105">
                        <?php else: ?>
                            <img src="https://illustrations.popsy.co/amber/product-launch.svg" alt="Service" class="w-full h-auto rounded-lg transition-transform duration-500 hover:scale-105">
                        <?php endif; ?>
                    </div>
                    
                    <!-- Feature Highlights -->
                    <div class="mt-8 bg-gray-50 p-6 rounded-xl shadow-sm">
                        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-star text-yellow-400 mr-2"></i> Key Features
                        </h3>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                <span class="text-gray-700">Rapid implementation with minimal setup</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                <span class="text-gray-700">Seamless integration with existing systems</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                <span class="text-gray-700">Continuous updates and improvements</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                <span class="text-gray-700">Dedicated technical support</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="md:w-3/5">
                    <div class="flex items-center mb-4">
                        <span class="bg-green-500 text-white text-sm font-bold px-3 py-1 rounded-full mr-3">
                            <?php echo ucfirst(str_replace('_', ' ', $serviceDetails["category"])); ?>
                        </span>
                        <span class="bg-yellow-100 text-yellow-800 text-sm font-medium px-3 py-1 rounded-full flex items-center">
                            <i class="fas fa-bolt mr-1"></i> Early Access
                        </span>
                    </div>
                    
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4"><?php echo $serviceDetails["name"]; ?></h1>
                    
                    <div class="w-20 h-1 primary-bg mb-6"></div>
                    
                    <div class="prose max-w-none text-gray-600 mb-8">
                        <?php echo nl2br($serviceDetails["description"]); ?>
                    </div>
                    
                    <!-- Pricing Section -->
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-6 rounded-xl shadow-sm mb-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Early Adopter Pricing</h3>
                        <div class="flex items-baseline mb-4">
                            <span class="text-3xl font-bold primary-text">Special Offer</span>
                            <span class="text-gray-500 ml-2">for beta users</span>
                        </div>
                        <p class="text-gray-600 mb-4">Join our early access program and get premium features at a fraction of the future retail price.</p>
                        
                        <?php if($loggedIn): ?>
                            <a href="php/purchase.php?id=<?php echo $serviceDetails["id"]; ?>" class="primary-bg hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition duration-300 inline-flex items-center">
                                <i class="fas fa-rocket mr-2"></i> Get Early Access
                            </a>
                        <?php else: ?>
                            <div class="flex flex-col sm:flex-row gap-4">
                                <a href="php/login.php" class="primary-bg hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition duration-300 inline-flex items-center justify-center">
                                    <i class="fas fa-sign-in-alt mr-2"></i> Login to Purchase
                                </a>
                                <a href="php/register.php" class="bg-white border-2 border-green-500 text-green-500 hover:bg-green-50 px-6 py-3 rounded-lg font-medium transition duration-300 inline-flex items-center justify-center">
                                    <i class="fas fa-user-plus mr-2"></i> Create Account
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Testimonial -->
                    <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm">
                        <div class="flex items-center mb-4">
                            <div class="flex -space-x-2 mr-4">
                                <img class="w-8 h-8 rounded-full border-2 border-white" src="https://randomuser.me/api/portraits/men/32.jpg" alt="User">
                                <img class="w-8 h-8 rounded-full border-2 border-white" src="https://randomuser.me/api/portraits/women/44.jpg" alt="User">
                            </div>
                            <span class="text-sm text-gray-500">Trusted by early adopters</span>
                        </div>
                        <p class="text-gray-600 italic">"This solution has streamlined our workflow and increased productivity by 30%. The team behind it is responsive and constantly improving the platform."</p>
                        <div class="mt-4 flex items-center">
                            <div class="flex text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <span class="ml-2 text-sm font-medium text-gray-500">Beta Tester</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Related Solutions -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Related <span class="primary-text">Solutions</span></h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Explore other tools that complement this solution</p>
                <div class="w-20 h-1 primary-bg mx-auto mt-4"></div>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Placeholder related services -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                    <div class="p-4 bg-gray-50">
                        <img src="img/analytics.png" alt="Analytics" class="w-full h-40 object-contain">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Analytics Dashboard</h3>
                        <p class="text-gray-600 mb-4">Get real-time insights into your business performance with our analytics solution.</p>
                        <a href="services.php" class="primary-text hover:text-green-600 font-medium inline-flex items-center">Learn more <i class="fas fa-arrow-right ml-2"></i></a>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                    <div class="p-4 bg-gray-50">
                        <img src="img/mobile.png" alt="Mobile App" class="w-full h-40 object-contain">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Mobile Integration</h3>
                        <p class="text-gray-600 mb-4">Extend your solution to mobile devices with our seamless integration tools.</p>
                        <a href="services.php" class="primary-text hover:text-green-600 font-medium inline-flex items-center">Learn more <i class="fas fa-arrow-right ml-2"></i></a>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                    <div class="p-4 bg-gray-50">
                        <img src="https://illustrations.popsy.co/amber/customer-support.svg" alt="Support" class="w-full h-40 object-contain">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Premium Support</h3>
                        <p class="text-gray-600 mb-4">Get dedicated technical assistance and priority issue resolution for your business.</p>
                        <a href="services.php" class="primary-text hover:text-green-600 font-medium inline-flex items-center">Learn more <i class="fas fa-arrow-right ml-2"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php else: ?>
    <!-- Hero Section -->
    <section class="pt-32 pb-16 bg-gradient-to-r from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-6">Innovative <span class="primary-text">Solutions</span> for Modern Businesses</h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-8">Powerful, scalable digital tools designed to help your business grow without enterprise-level complexity or cost</p>
                <div class="w-24 h-1 primary-bg mx-auto mb-8"></div>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="#services-list" class="primary-bg hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition duration-300 inline-flex items-center">Explore Solutions <i class="fas fa-arrow-down ml-2"></i></a>
                    <?php if(!$loggedIn): ?>
                    <a href="php/register.php" class="bg-white border-2 border-green-500 text-green-500 hover:bg-green-50 px-6 py-3 rounded-lg font-medium transition duration-300">Join Early Access</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex justify-center">
                <img src="img\digital.png" alt="Digital Solutions" class="w-full max-w-2xl rounded-lg shadow-lg">
            </div>
        </div>
    </section>

    <!-- All Services List -->
    <section id="services-list" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Our <span class="primary-text">Solutions</span> Portfolio</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Cutting-edge digital tools designed to streamline operations, enhance productivity, and drive growth</p>
                <div class="w-20 h-1 primary-bg mx-auto mt-4"></div>
            </div>
            
            <!-- Services Categories Filter -->
            <div class="flex flex-wrap justify-center gap-3 mb-12">
                <a href="services.php" class="<?php echo !isset($_GET['category']) ? 'primary-bg text-white' : 'bg-gray-100 text-gray-800'; ?> px-5 py-2 rounded-full text-sm font-medium transition duration-300 shadow-sm hover:shadow">
                    <i class="fas fa-th-large mr-2"></i>All Solutions
                </a>
                <a href="services.php?category=website" class="<?php echo (isset($_GET['category']) && $_GET['category'] == 'website') ? 'primary-bg text-white' : 'bg-gray-100 text-gray-800'; ?> px-5 py-2 rounded-full text-sm font-medium transition duration-300 shadow-sm hover:shadow">
                    <i class="fas fa-globe mr-2"></i>Web Platforms
                </a>
                <a href="services.php?category=attendance" class="<?php echo (isset($_GET['category']) && $_GET['category'] == 'attendance') ? 'primary-bg text-white' : 'bg-gray-100 text-gray-800'; ?> px-5 py-2 rounded-full text-sm font-medium transition duration-300 shadow-sm hover:shadow">
                    <i class="fas fa-user-check mr-2"></i>Team Management
                </a>
                <a href="services.php?category=homework" class="<?php echo (isset($_GET['category']) && $_GET['category'] == 'homework') ? 'primary-bg text-white' : 'bg-gray-100 text-gray-800'; ?> px-5 py-2 rounded-full text-sm font-medium transition duration-300 shadow-sm hover:shadow">
                    <i class="fas fa-tasks mr-2"></i>Task Solutions
                </a>
                <a href="services.php?category=inventory" class="<?php echo (isset($_GET['category']) && $_GET['category'] == 'inventory') ? 'primary-bg text-white' : 'bg-gray-100 text-gray-800'; ?> px-5 py-2 rounded-full text-sm font-medium transition duration-300 shadow-sm hover:shadow">
                    <i class="fas fa-boxes mr-2"></i>Resource Management
                </a>
                <a href="services.php?category=other" class="<?php echo (isset($_GET['category']) && $_GET['category'] == 'other') ? 'primary-bg text-white' : 'bg-gray-100 text-gray-800'; ?> px-5 py-2 rounded-full text-sm font-medium transition duration-300 shadow-sm hover:shadow">
                    <i class="fas fa-plus-circle mr-2"></i>Custom Solutions
                </a>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                // Fetch services from the database
                $sql = "SELECT * FROM services";
                
                // Filter by category if set
                if(isset($_GET['category']) && !empty($_GET['category'])) {
                    $category = $_GET['category'];
                    $sql .= " WHERE category = '$category'";
                }
                
                $result = mysqli_query($conn, $sql);
                
                if(mysqli_num_rows($result) > 0) {
                    while($service = mysqli_fetch_assoc($result)) {
                        echo '<div class="service-card bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">';
                        echo '<div class="relative overflow-hidden">';
                        if(!empty($service["image"])) {
                            echo '<img src="img/services/' . $service["image"] . '" alt="' . $service["name"] . '" class="w-full h-52 object-cover transition-transform duration-500 hover:scale-105">';
                        } else {
                            echo '<img src="https://illustrations.popsy.co/amber/product-launch.svg" alt="Service" class="w-full h-52 object-cover transition-transform duration-500 hover:scale-105">';
                        }
                        echo '<div class="absolute top-4 right-4">';
                        echo '<span class="bg-green-500 bg-opacity-90 text-white text-xs font-bold px-3 py-1 rounded-full">' . ucfirst(str_replace('_', ' ', $service["category"])) . '</span>';
                        echo '</div>';
                        echo '</div>';
                        echo '<div class="p-6">';
                        echo '<h3 class="text-xl font-bold text-gray-800 mb-3">' . $service["name"] . '</h3>';
                        echo '<p class="text-gray-600 mb-4">' . substr($service["description"], 0, 120) . '...</p>';
                        echo '<div class="flex justify-between items-center">';
                        echo '<div class="flex items-center">';
                        echo '<i class="fas fa-star text-yellow-400 mr-1"></i>';
                        echo '<span class="text-gray-700 font-medium">New</span>';
                        echo '</div>';
                        echo '<a href="services.php?id=' . $service["id"] . '" class="primary-bg hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium transition duration-300 inline-flex items-center text-sm">View Details <i class="fas fa-arrow-right ml-2"></i></a>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="col-span-full text-center py-12">';
                    echo '<img src="https://illustrations.popsy.co/amber/work-in-progress.svg" alt="No Services" class="w-64 mx-auto mb-6">';
                    echo '<p class="text-gray-600 text-lg">We\'re currently developing solutions for this category.</p>';
                    echo '<p class="text-gray-500 mt-2">Check back soon or <a href="index.php#contact" class="primary-text hover:underline">contact us</a> for custom requirements.</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </section>
    
    <!-- Call to Action -->
    <section class="py-16 bg-gradient-to-r from-green-600 to-green-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="md:w-2/3">
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Ready to accelerate your growth?</h2>
                    <p class="text-xl text-green-100 mb-6">Join our early access program today and be among the first to experience our innovative solutions.</p>
                    <div class="flex flex-wrap gap-4">
                        <a href="services.php" class="bg-white text-green-600 hover:bg-green-50 px-6 py-3 rounded-lg font-medium transition duration-300 inline-flex items-center">
                            <i class="fas fa-th-large mr-2"></i> Explore All Solutions
                        </a>
                        <a href="index.php#contact" class="bg-transparent border-2 border-white text-white hover:bg-green-500 px-6 py-3 rounded-lg font-medium transition duration-300 inline-flex items-center">
                            <i class="fas fa-envelope mr-2"></i> Contact Our Team
                        </a>
                    </div>
                </div>
                <div class="md:w-1/3 flex justify-center">
                    <img src="img/growth.jpg" alt="Growth" class="w-64 h-64">
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

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
                            <span class="text-gray-400">+91 79915 15802</span>
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

    <!-- <footer class="bg-gray-800 text-white py-12"> -->
        <!-- <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
                        <li><a href="about.php" class="text-gray-400 hover:text-white transition duration-300">About Us</a></li>
                        <li><a href="services.php" class="text-gray-400 hover:text-white transition duration-300">Services</a></li>
                        <li><a href="#contact" class="text-gray-400 hover:text-white transition duration-300">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Services</h4>
                    <ul class="space-y-2">
                        <?php
                        // Display service links in footer
                        $sql = "SELECT id, name FROM services LIMIT 4";
                        $result = mysqli_query($conn, $sql);
                        
                        if(mysqli_num_rows($result) > 0) {
                            while($service = mysqli_fetch_assoc($result)) {
                                echo '<li><a href="services.php?id=' . $service['id'] . '" class="text-gray-400 hover:text-white transition duration-300">' . $service['name'] . '</a></li>';
                            }
                        }
                        ?>
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
                <p>&copy; <?php echo date("Y"); ?> Dulify. All rights reserved.</p>
            </div>
        </div>
    </footer> -->

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