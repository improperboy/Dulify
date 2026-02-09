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
    <title>Cookies Policy | Dulify</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            scroll-behavior: smooth;
        }
        .primary-bg {
            background-color: #10B981;
        }
        .primary-text {
            color: #10B981;
        }
        .secondary-bg {
            background-color: #F59E0B;
        }
        .secondary-text {
            color: #F59E0B;
        }
        .nav-link {
            position: relative;
        }
        .nav-link:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #10B981;
            transition: width 0.3s ease;
        }
        .nav-link:hover:after {
            width: 100%;
        }
        .mobile-menu {
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, #10B981 0%, #34D399 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
        }
        .btn-outline {
            transition: all 0.3s ease;
        }
        .btn-outline:hover {
            background-color: #10B981;
            color: white;
            transform: translateY(-2px);
        }
        .policy-section {
            margin-bottom: 2.5rem;
        }
        .policy-section h3 {
            border-left: 4px solid #10B981;
            padding-left: 1rem;
        }
        .cookie-type {
            background-color: #f8fafc;
            border-left: 4px solid #10B981;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0 4px 4px 0;
        }
        .cookie-type h4 {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1e293b;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm fixed w-full z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
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
                    <a href="index.php#features" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Features</a>
                    <a href="index.php#testimonials" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Testimonials</a>
                    <a href="index.php#contact" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Contact</a>
                    <?php if($loggedIn): ?>
                        <a href="dashboard.php" class="btn-primary text-white px-4 py-2 rounded-md text-sm font-medium">Dashboard</a>
                        <a href="php/logout.php" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition duration-300">Logout</a>
                    <?php else: ?>
                        <a href="php/login.php" class="btn-primary text-white px-4 py-2 rounded-md text-sm font-medium">Login</a>
                        <a href="php/register.php" class="btn-outline border border-green-500 text-green-500 px-4 py-2 rounded-md text-sm font-medium">Sign Up</a>
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
                <a href="services.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Services</a>
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

    <!-- Cookies Policy Content -->
    <section class="pt-32 pb-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Cookies Policy</h1>
                <div class="w-20 h-1 bg-gradient-to-r from-green-400 to-green-600 mx-auto rounded-full"></div>
                <p class="text-gray-600 mt-4">Last updated: <?php echo date("F j, Y"); ?></p>
            </div>

            <div class="bg-white p-6 md:p-8 rounded-lg shadow-sm">
                <div class="policy-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">1. Introduction</h3>
                    <p class="text-gray-600 mb-4">This Cookies Policy explains how Dulify ("we", "us", or "our") uses cookies and similar tracking technologies when you visit our website or use our services.</p>
                    <p class="text-gray-600">By using our website, you consent to the use of cookies in accordance with this policy. If you do not agree to our use of cookies, you should adjust your browser settings accordingly or refrain from using our website.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">2. What Are Cookies?</h3>
                    <p class="text-gray-600 mb-4">Cookies are small text files that are placed on your computer, smartphone, or other device when you visit a website. They are widely used to make websites work more efficiently, as well as to provide information to the website owners.</p>
                    <p class="text-gray-600">Cookies may be either "persistent" cookies or "session" cookies. Persistent cookies remain on your device when you go offline, while session cookies are deleted as soon as you close your web browser.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">3. How We Use Cookies</h3>
                    <p class="text-gray-600 mb-4">We use cookies for the following purposes:</p>
                    
                    <div class="cookie-type">
                        <h4 class="text-lg font-medium">Essential Cookies</h4>
                        <p class="text-gray-600">These cookies are necessary for the website to function and cannot be switched off in our systems. They are usually only set in response to actions made by you such as logging in or filling in forms.</p>
                    </div>
                    
                    <div class="cookie-type">
                        <h4 class="text-lg font-medium">Performance Cookies</h4>
                        <p class="text-gray-600">These cookies allow us to count visits and traffic sources so we can measure and improve the performance of our site. They help us know which pages are the most and least popular and see how visitors move around the site.</p>
                    </div>
                    
                    <div class="cookie-type">
                        <h4 class="text-lg font-medium">Functional Cookies</h4>
                        <p class="text-gray-600">These cookies enable the website to provide enhanced functionality and personalization. They may be set by us or by third-party providers whose services we have added to our pages.</p>
                    </div>
                    
                    <div class="cookie-type">
                        <h4 class="text-lg font-medium">Targeting Cookies</h4>
                        <p class="text-gray-600">These cookies may be set through our site by our advertising partners. They may be used by those companies to build a profile of your interests and show you relevant ads on other sites.</p>
                    </div>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">4. Third-Party Cookies</h3>
                    <p class="text-gray-600 mb-4">In addition to our own cookies, we may also use various third-party cookies to report usage statistics of the service, deliver advertisements on and through the service, and so on.</p>
                    <p class="text-gray-600">Some of the third-party services we use include:</p>
                    <ul class="list-disc pl-6 text-gray-600 space-y-2 mb-4">
                        <li><strong>Google Analytics:</strong> For analyzing website traffic and user behavior.</li>
                        <li><strong>Facebook Pixel:</strong> For measuring the effectiveness of our advertising.</li>
                        <li><strong>Hotjar:</strong> For understanding user experience through heatmaps and session recordings.</li>
                    </ul>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">5. Managing Cookies</h3>
                    <p class="text-gray-600 mb-4">You can control and/or delete cookies as you wish. You can delete all cookies that are already on your computer and you can set most browsers to prevent them from being placed. However, if you do this, you may have to manually adjust some preferences every time you visit a site and some services and functionalities may not work.</p>
                    <p class="text-gray-600 mb-4">Most web browsers allow some control of most cookies through the browser settings. To find out more about cookies, including how to see what cookies have been set and how to manage and delete them, visit <a href="https://www.aboutcookies.org/" class="primary-text hover:underline">www.aboutcookies.org</a> or <a href="https://www.allaboutcookies.org/" class="primary-text hover:underline">www.allaboutcookies.org</a>.</p>
                    <p class="text-gray-600">To opt out of being tracked by Google Analytics across all websites, visit <a href="https://tools.google.com/dlpage/gaoptout" class="primary-text hover:underline">https://tools.google.com/dlpage/gaoptout</a>.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">6. Changes to This Policy</h3>
                    <p class="text-gray-600 mb-4">We may update this Cookies Policy from time to time to reflect changes in our practices or for other operational, legal, or regulatory reasons. We will notify you of any changes by posting the new Cookies Policy on this page with a new "Last updated" date.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">7. Contact Us</h3>
                    <p class="text-gray-600">If you have any questions about our use of cookies, please contact us at:</p>
                    <p class="text-gray-600 mt-2">
                        Dulify Privacy Team<br>
                        Email: privacy@dulify.com<br>
                        Address: 123 Tech Street, Digital City, DC 10001
                    </p>
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
                        <li><a href="about.php" class="text-gray-400 hover:text-white transition duration-300">About Us</a></li>
                        <li><a href="services.php" class="text-gray-400 hover:text-white transition duration-300">Services</a></li>
                        <li><a href="index.php#contact" class="text-gray-400 hover:text-white transition duration-300">Contact</a></li>

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
                                echo '<li><a href="services.php?id=' . htmlspecialchars($service['id']) . '" class="text-gray-400 hover:text-white transition duration-300">' . htmlspecialchars($service['name']) . '</a></li>';
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