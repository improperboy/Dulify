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
    <title>Terms of Service | Dulify</title>
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
        .terms-section {
            margin-bottom: 2.5rem;
        }
        .terms-section h3 {
            border-left: 4px solid #10B981;
            padding-left: 1rem;
        }
        .highlight-box {
            background-color: #F0FDF4;
            border-left: 4px solid #10B981;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 0 4px 4px 0;
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

    <!-- Terms of Service Content -->
    <section class="pt-32 pb-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Terms of Service</h1>
                <div class="w-20 h-1 bg-gradient-to-r from-green-400 to-green-600 mx-auto rounded-full"></div>
                <p class="text-gray-600 mt-4">Last updated: <?php echo date("F j, Y"); ?></p>
            </div>

            <div class="bg-white p-6 md:p-8 rounded-lg shadow-sm">
                <div class="terms-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">1. Acceptance of Terms</h3>
                    <p class="text-gray-600">By accessing or using Dulify's services, you agree to be bound by these Terms of Service. If you do not agree to all terms, you may not use our services.</p>
                </div>

                <div class="terms-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">2. Description of Services</h3>
                    <p class="text-gray-600">Dulify provides website development, hosting, and related digital services primarily for local schools and small businesses. Services may include but are not limited to website design, development, maintenance, and hosting solutions.</p>
                </div>

                <div class="terms-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">3. Refund Policy</h3>
                    <div class="highlight-box">
                        <h4 class="font-semibold text-gray-800 mb-2">Our refund policy is as follows:</h4>
                        <ul class="list-disc pl-6 text-gray-600 space-y-2">
                            <li><strong>First 7 days:</strong> Full refund if the website doesn't work and errors cannot be resolved by our developers</li>
                            <li><strong>Next 14 days (days 8-21):</strong> 85-90% refund under the same conditions</li>
                            <li><strong>Rest of the first month (days 22-30):</strong> 75% refund under the same conditions</li>
                            <li><strong>After 1 month:</strong> No refunds will be issued</li>
                        </ul>
                    </div>
                    <p class="text-gray-600 mt-4">To request a refund, please contact our support team with details of the issues encountered. Refunds will be processed within 14 business days.</p>
                </div>

                <div class="terms-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">4. Service Warranty</h3>
                    <div class="highlight-box">
                        <h4 class="font-semibold text-gray-800 mb-2">We guarantee:</h4>
                        <ul class="list-disc pl-6 text-gray-600 space-y-2">
                            <li>Free recovery for any errors or problems with the provided website for 1 year from the date of delivery</li>
                            <li>After 1 year, services will expire and must be renewed (no building charges will apply at renewal)</li>
                            <li>Response time for critical issues within 24 hours during business days</li>
                        </ul>
                    </div>
                </div>

                <div class="terms-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">5. Payment Terms</h3>
                    <p class="text-gray-600 mb-4">Payment for services is due as specified in your service agreement. We accept various payment methods including credit cards and bank transfers.</p>
                    <ul class="list-disc pl-6 text-gray-600 space-y-2">
                        <li>Recurring payments will be automatically charged unless canceled at least 7 days before the renewal date</li>
                        <li>Late payments may result in service suspension</li>
                        <li>All prices are in USD unless otherwise specified</li>
                    </ul>
                </div>

                <div class="terms-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">6. Client Responsibilities</h3>
                    <p class="text-gray-600 mb-4">As a client, you agree to:</p>
                    <ul class="list-disc pl-6 text-gray-600 space-y-2">
                        <li>Provide accurate and complete information required for service delivery</li>
                        <li>Make timely payments as agreed</li>
                        <li>Notify us promptly of any issues or required changes</li>
                        <li>Not use our services for any illegal or prohibited purposes</li>
                        <li>Maintain backups of your content (we are not responsible for data loss)</li>
                    </ul>
                </div>

                <div class="terms-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">7. Intellectual Property</h3>
                    <p class="text-gray-600 mb-4">All intellectual property rights for the websites we create will be transferred to you upon full payment. You retain ownership of all content you provide.</p>
                    <p class="text-gray-600">We retain the right to display your website in our portfolio unless otherwise agreed in writing.</p>
                </div>

                <div class="terms-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">8. Limitation of Liability</h3>
                    <p class="text-gray-600">Dulify shall not be liable for any indirect, incidental, special, consequential or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from:</p>
                    <ul class="list-disc pl-6 text-gray-600 space-y-2 mt-2">
                        <li>Your access to or use of or inability to access or use the services</li>
                        <li>Any conduct or content of any third party on the services</li>
                        <li>Any content obtained from the services</li>
                        <li>Unauthorized access, use or alteration of your transmissions or content</li>
                    </ul>
                </div>

                <div class="terms-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">9. Termination</h3>
                    <p class="text-gray-600 mb-4">We may terminate or suspend your access to our services immediately, without prior notice or liability, for any reason whatsoever, including without limitation if you breach these Terms.</p>
                    <p class="text-gray-600">Upon termination, your right to use our services will immediately cease. If you wish to terminate your account, you may simply discontinue using our services.</p>
                </div>

                <div class="terms-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">10. Changes to Terms</h3>
                    <p class="text-gray-600 mb-4">We reserve the right to modify these terms at any time. We will notify you of any changes by posting the new Terms of Service on this page and updating the "Last updated" date.</p>
                    <p class="text-gray-600">Your continued use of our services after any such changes constitutes your acceptance of the new Terms.</p>
                </div>

                <div class="terms-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">11. Governing Law</h3>
                    <p class="text-gray-600">These Terms shall be governed and construed in accordance with the laws of India, without regard to its conflict of law provisions.</p>
                </div>

                <div class="terms-section">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">12. Contact Us</h3>
                    <p class="text-gray-600">If you have any questions about these Terms, please contact us at:</p>
                    <p class="text-gray-600 mt-2">
                        Dulify Support Team<br>
                        Email: help@dulify.com<br>
                        Address: Pukhrayan, Kanpur Dehat, Uttar Pradesh 209111<br>
                        Phone: +91 79915 15802
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
                        <li><a href="index.php" class="text-gray-400 hover:text-white transition duration-300">Contact</a></li>
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
                        <li><a href="terms.php" class="text-white hover:text-green-300 transition duration-300">Terms of Service</a></li>
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