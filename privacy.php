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
    <title>Privacy Policy | Dulify</title>
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

    <!-- Privacy Policy Content -->
    <section class="pt-32 pb-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Privacy Policy</h1>
                <div class="w-20 h-1 bg-gradient-to-r from-green-400 to-green-600 mx-auto rounded-full"></div>
                <p class="text-gray-600 mt-4">Last updated: <?php echo date("F j, Y"); ?></p>
            </div>

            <div class="bg-white p-6 md:p-8 rounded-lg shadow-sm">
                <div class="policy-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">1. Introduction</h3>
                    <p class="text-gray-600 mb-4">Welcome to Dulify ("we," "our," or "us"). We are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our services.</p>
                    <p class="text-gray-600">By accessing or using our services, you agree to the terms of this Privacy Policy. If you do not agree with our policies and practices, please do not use our services.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">2. Information We Collect</h3>
                    <p class="text-gray-600 mb-4">We may collect the following types of information:</p>
                    <ul class="list-disc pl-6 text-gray-600 space-y-2 mb-4">
                        <li><strong>Personal Information:</strong> Name, email address, phone number, school or business details, and other contact information when you register for our services.</li>
                        <li><strong>Usage Data:</strong> Information about how you interact with our website and services, including IP address, browser type, pages visited, and time spent on pages.</li>
                        <li><strong>Payment Information:</strong> When you purchase our services, we collect payment details, though we do not store credit card information directly.</li>
                        <li><strong>Cookies and Tracking:</strong> We use cookies and similar technologies to enhance your experience and analyze usage patterns.</li>
                    </ul>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">3. How We Use Your Information</h3>
                    <p class="text-gray-600 mb-4">We use the information we collect for various purposes, including:</p>
                    <ul class="list-disc pl-6 text-gray-600 space-y-2 mb-4">
                        <li>To provide, maintain, and improve our services</li>
                        <li>To process transactions and send related information</li>
                        <li>To respond to your inquiries and provide customer support</li>
                        <li>To send administrative information, updates, and security alerts</li>
                        <li>To personalize your experience and deliver content relevant to your interests</li>
                        <li>For analytics and service improvement</li>
                        <li>To comply with legal obligations and protect our rights</li>
                    </ul>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">4. Information Sharing and Disclosure</h3>
                    <p class="text-gray-600 mb-4">We do not sell or rent your personal information to third parties. We may share information in the following circumstances:</p>
                    <ul class="list-disc pl-6 text-gray-600 space-y-2 mb-4">
                        <li><strong>Service Providers:</strong> With vendors and service providers who perform services on our behalf.</li>
                        <li><strong>Legal Requirements:</strong> When required by law or to protect our rights and safety.</li>
                        <li><strong>Business Transfers:</strong> In connection with a merger, acquisition, or sale of assets.</li>
                        <li><strong>With Your Consent:</strong> When you give us explicit permission to share your information.</li>
                    </ul>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">5. Data Security</h3>
                    <p class="text-gray-600 mb-4">We implement appropriate technical and organizational measures to protect your personal information. However, no method of transmission over the Internet or electronic storage is 100% secure, and we cannot guarantee absolute security.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">6. Your Rights and Choices</h3>
                    <p class="text-gray-600 mb-4">Depending on your location, you may have certain rights regarding your personal information:</p>
                    <ul class="list-disc pl-6 text-gray-600 space-y-2 mb-4">
                        <li>Access and update your account information</li>
                        <li>Request deletion of your personal data</li>
                        <li>Opt-out of marketing communications</li>
                        <li>Disable cookies through your browser settings</li>
                        <li>Withdraw consent where processing is based on consent</li>
                    </ul>
                    <p class="text-gray-600">To exercise these rights, please contact us at privacy@dulify.com.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">7. Children's Privacy</h3>
                    <p class="text-gray-600 mb-4">Our services are not directed to children under 13. We do not knowingly collect personal information from children under 13. If we become aware that we have collected personal information from a child under 13, we will take steps to delete such information.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">8. Changes to This Policy</h3>
                    <p class="text-gray-600 mb-4">We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last updated" date. You are advised to review this Privacy Policy periodically for any changes.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">9. Contact Us</h3>
                    <p class="text-gray-600">If you have any questions about this Privacy Policy, please contact us at:</p>
                    <p class="text-gray-600 mt-2">
                        Dulify Privacy Team<br>
                        Email: hello@dulify.com<br>
                        Address: Pukhrayan, Kanpur Dehat, Uttar Pradesh 209111
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