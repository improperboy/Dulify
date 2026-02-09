<?php
// Debugging disabled for production
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// Start output buffering to prevent partial content sending
ob_start();

// Include database configuration
require_once "includes/config.php";

// Prevent caching completely
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// Define a debug array to collect information
$debug = array();
$debug[] = "Session status: " . session_status();
$debug[] = "Session ID: " . session_id();

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    $debug[] = "User not logged in, redirecting to login page";
    // Clear any buffered output
    ob_end_clean();
    header("location: php/login.php");
    exit;
}

// Add user info to debug
$debug[] = "User ID: " . (isset($_SESSION["id"]) ? $_SESSION["id"] : 'not set');
$debug[] = "Username: " . (isset($_SESSION["username"]) ? $_SESSION["username"] : 'not set');

// Check if purchase ID is provided
if(!isset($_GET["id"]) || empty($_GET["id"]) || !is_numeric($_GET["id"])){
    $debug[] = "Invalid ID parameter: " . (isset($_GET["id"]) ? $_GET["id"] : 'not set');
    // Clear any buffered output
    ob_end_clean();
    header("location: dashboard.php?error=invalid_service&t=" . time());
    exit;
}

// Debugging disabled for production
$show_debug = false;

$purchase_id = $_GET["id"];
$user_id = $_SESSION["id"];

$debug[] = "Accessing purchase ID: " . $purchase_id . " for user ID: " . $user_id;

// Fetch the purchase details along with service info
$purchase = null;
$sql = "SELECT p.*, s.name, s.description, s.category, s.image 
        FROM purchases p 
        JOIN services s ON p.service_id = s.id 
        WHERE p.id = ? AND p.user_id = ? AND p.status = 'active'";

$debug[] = "SQL query: " . $sql;

// Track errors
$error_message = "";
$db_error = false;

// Try to prepare statement
if($stmt = mysqli_prepare($conn, $sql)){
    $debug[] = "SQL statement prepared successfully";
    
    // Bind parameters
    mysqli_stmt_bind_param($stmt, "ii", $purchase_id, $user_id);
    $debug[] = "Parameters bound: purchase_id=$purchase_id, user_id=$user_id";
    
    // Execute the statement
    if(mysqli_stmt_execute($stmt)){
        $debug[] = "SQL executed successfully";
        
        // Get the result
        $result = mysqli_stmt_get_result($stmt);
        $debug[] = "Result rows: " . mysqli_num_rows($result);
        
        if(mysqli_num_rows($result) == 1){
            // Found the purchase
            $purchase = mysqli_fetch_assoc($result);
            $debug[] = "Purchase found: Service name = " . (isset($purchase["name"]) ? $purchase["name"] : 'unknown');
        } else {
            // Purchase not found
            $debug[] = "Purchase not found or not active";
            $error_message = "The requested service was not found or is no longer active.";
            
            // Clear output buffer before redirect
            ob_end_clean();
            header("location: dashboard.php?error=service_not_found&t=" . time());
            exit;
        }
    } else {
        // Execute failed
        $db_error = true;
        $error_message = "Database query execution failed: " . mysqli_error($conn);
        $debug[] = $error_message;
    }
    
    // Close the statement
    mysqli_stmt_close($stmt);
    $debug[] = "Statement closed";
} else {
    // Prepare failed
    $db_error = true;
    $error_message = "Failed to prepare database query: " . mysqli_error($conn);
    $debug[] = $error_message;
}

// Get the service interface based on category
function getServiceInterface($category) {
    switch($category) {
        case 'website':
            return "website_builder.php";
        case 'attendance':
            return "attendance_system.php";
        case 'homework':
            return "homework_portal.php";
        case 'inventory':
            return "inventory_system.php";
        default:
            return "generic_service.php";
    }
}

// Only try to get service interface if we have valid purchase data
$serviceInterface = "";
if ($purchase && isset($purchase["category"])) {
    $serviceInterface = getServiceInterface($purchase["category"]);
    $debug[] = "Service interface: " . $serviceInterface;
} else {
    $debug[] = "No valid purchase data or category to determine service interface";
    if ($db_error) {
        $debug[] = "Database error occurred: " . $error_message;
    }
}

// End output buffering and send content if we haven't redirected yet
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($purchase["name"]) ? htmlspecialchars($purchase["name"]) : 'Service'; ?> - Dulify</title>
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
        .secondary-bg {
            background-color: #FFC107;
        }
        .secondary-text {
            color: #FFC107;
        }
        .nav-link:hover {
            color: #4CAF50;
        }
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        .btn-primary:hover {
            background-color: #3d8b40;
        }
        .btn-outline {
            border: 1px solid #4CAF50;
            color: #4CAF50;
        }
        .btn-outline:hover {
            background-color: #4CAF50;
            color: white;
        }
        .service-card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
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
                    <a href="about.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">About</a>
                    <a href="dashboard.php" class="primary-bg hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-300">Dashboard</a>
                    <a href="php/logout.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition duration-300">Logout</a>
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
                <a href="about.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">About</a>
                <a href="dashboard.php" class="block px-3 py-2 rounded-md text-base font-medium text-white bg-green-500 hover:bg-green-600">Dashboard</a>
                <a href="php/logout.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Service Access Section -->
    <section class="pt-24 pb-12 md:pt-32 md:pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php if($db_error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                    <strong class="font-bold">Error Accessing Service</strong>
                    <span class="block sm:inline"><?php echo htmlspecialchars($error_message); ?></span>
                    <div class="mt-4">
                        <a href="dashboard.php" class="btn btn-primary">Return to Dashboard</a>
                    </div>
                </div>
                
                <?php if($show_debug): ?>
                <div class="bg-gray-100 border border-gray-300 text-gray-800 px-4 py-3 rounded mb-6">
                    <h4 class="font-bold mb-2">Debug Information</h4>
                    <pre class="bg-white p-2 rounded"><?php print_r($debug); ?></pre>
                </div>
                <?php endif; ?>
            <?php else: ?>
            <?php if($purchase): ?>
                <div class="max-w-4xl mx-auto">
                    <div class="mb-6">
                        <a href="dashboard.php" class="btn btn-outline inline-flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                        </a>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-md mb-8 flex flex-col md:flex-row justify-between items-start md:items-center">
                        <div class="mb-4 md:mb-0">
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($purchase["name"]); ?></h1>
                            <div class="text-gray-500 text-sm">
                                Subscription active until: <?php echo date("F d, Y", strtotime($purchase["expiry_date"])); ?>
                            </div>
                        </div>
                        <a href="php/support.php?service=<?php echo $purchase["service_id"]; ?>" class="btn btn-primary">
                            <i class="fas fa-headset mr-2"></i> Get Support
                        </a>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <div class="text-center py-12">
                            <div class="mx-auto w-32 h-32 bg-green-100 rounded-full flex items-center justify-center mb-6">
                                <i class="fas fa-cog primary-text text-4xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 mb-4">Welcome to <?php echo htmlspecialchars($purchase["name"]); ?></h3>
                            <p class="text-gray-600 max-w-lg mx-auto mb-8">
                                This is a demo version of the service interface. In a production environment, this would be replaced with the actual service functionality.
                            </p>
                            <!-- <div class="flex flex-col sm:flex-row justify-center gap-4">
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-rocket mr-2"></i> Launch Service
                                </a> -->
                                <a href="dashboard.php?tab=support" class="btn btn-outline">
                                    <i class="fas fa-question-circle mr-2"></i> Need Help?
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <div class="mx-auto w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-exclamation-circle text-gray-500 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Service Not Available</h3>
                    <p class="text-gray-600 mb-6">The requested service could not be found or your subscription has expired.</p>
                    <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
                </div>
            <?php endif; ?>
            
            <?php endif; // end of else for $db_error check ?>
            
            <?php if($show_debug && !$db_error): ?>
            <div class="bg-gray-100 border border-gray-300 text-gray-800 px-4 py-3 rounded mt-8">
                <h4 class="font-bold mb-2">Debug Information</h4>
                <pre class="bg-white p-2 rounded"><?php print_r($debug); ?></pre>
            </div>
            <?php endif; ?>
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
                        <li><a href="services.php" class="text-gray-400 hover:text-white transition duration-300">Services</a></li>
                        <li><a href="about.php" class="text-gray-400 hover:text-white transition duration-300">About Us</a></li>
                        <li><a href="dashboard.php" class="text-gray-400 hover:text-white transition duration-300">Dashboard</a></li>
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