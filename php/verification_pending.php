<?php
// Get the email from the URL parameter
$email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Pending - Dulify</title>
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

    <!-- Verification Pending Section -->
    <section class="pt-24 pb-12 md:pt-32 md:pb-20">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-lg shadow-md">
                <div class="text-center">
                    <div class="mb-6 text-yellow-500">
                        <i class="fas fa-envelope text-6xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Verification Email Sent</h2>
                    <p class="text-gray-600 mb-6">
                        We've sent a verification email to <strong><?php echo $email; ?></strong>. 
                        Please check your inbox and click the verification link to activate your account.
                    </p>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    If you don't see the email in your inbox, please check your spam or junk folder.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <a href="login.php" class="primary-bg hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition duration-300 inline-block">Go to Login</a>
                        <p class="text-gray-500 text-sm">You can login after verifying your email address.</p>
                    </div>
                </div>
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