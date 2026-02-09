<?php
// Include database configuration
require_once "includes/config.php";

// Check if user is logged in, if not redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: php/login.php");
    exit;
}

// Get user information
$user_id = $_SESSION["id"];
$username = $_SESSION["username"];

// Default tab
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'services';

// Process testimonial submission
$testimonial_success = false;
$testimonial_error = false;
$testimonial_message = "";

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_testimonial"])) {
    // Validate inputs
    $service_id = $_POST["service_id"];
    $rating = $_POST["rating"];
    $comment = trim($_POST["comment"]);
    
    // Basic validation
    if(empty($comment)) {
        $testimonial_error = true;
        $testimonial_message = "Please provide a comment for your review.";
    } elseif($rating < 1 || $rating > 5) {
        $testimonial_error = true;
        $testimonial_message = "Please select a rating between 1 and 5.";
    } else {
        // Check if user has already submitted a testimonial for this service
        $check_sql = "SELECT id FROM testimonials WHERE user_id = ? AND service_id = ?";
        if($stmt = mysqli_prepare($conn, $check_sql)) {
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $service_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            
            if(mysqli_stmt_num_rows($stmt) > 0) {
                // Update existing testimonial
                $update_sql = "UPDATE testimonials SET rating = ?, comment = ?, created_at = NOW() WHERE user_id = ? AND service_id = ?";
                if($update_stmt = mysqli_prepare($conn, $update_sql)) {
                    mysqli_stmt_bind_param($update_stmt, "isii", $rating, $comment, $user_id, $service_id);
                    if(mysqli_stmt_execute($update_stmt)) {
                        $testimonial_success = true;
                        $testimonial_message = "Your review has been updated successfully!";
                    } else {
                        $testimonial_error = true;
                        $testimonial_message = "Something went wrong. Please try again.";
                    }
                    mysqli_stmt_close($update_stmt);
                }
            } else {
                // Insert new testimonial
                $insert_sql = "INSERT INTO testimonials (user_id, service_id, rating, comment) VALUES (?, ?, ?, ?)";
                if($insert_stmt = mysqli_prepare($conn, $insert_sql)) {
                    mysqli_stmt_bind_param($insert_stmt, "iiis", $user_id, $service_id, $rating, $comment);
                    if(mysqli_stmt_execute($insert_stmt)) {
                        $testimonial_success = true;
                        $testimonial_message = "Thank you for your review!";
                    } else {
                        $testimonial_error = true;
                        $testimonial_message = "Something went wrong. Please try again.";
                    }
                    mysqli_stmt_close($insert_stmt);
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Fetch user's purchased services
$purchased_services = array();
$sql = "SELECT p.*, s.name, s.description, s.category, s.image,
        (SELECT COUNT(*) FROM testimonials t WHERE t.user_id = p.user_id AND t.service_id = s.id) as has_testimonial,
        (SELECT rating FROM testimonials t WHERE t.user_id = p.user_id AND t.service_id = s.id) as user_rating,
        (SELECT comment FROM testimonials t WHERE t.user_id = p.user_id AND t.service_id = s.id) as user_comment
        FROM purchases p 
        JOIN services s ON p.service_id = s.id 
        WHERE p.user_id = ? 
        ORDER BY p.purchase_date DESC";

if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        while($row = mysqli_fetch_assoc($result)){
            $purchased_services[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}

// Fetch user's pending orders
$pending_orders = array();
$sql = "SELECT o.*, s.name as service_name, s.image
        FROM orders o
        JOIN services s ON o.service_id = s.id
        WHERE o.user_id = ?
        ORDER BY o.order_date DESC";

if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        while($row = mysqli_fetch_assoc($result)){
            $pending_orders[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}

// Fetch user's support messages
$support_messages = array();
$sql = "SELECT * FROM support_messages WHERE user_id = ? ORDER BY created_at DESC";

if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        while($row = mysqli_fetch_assoc($result)){
            $support_messages[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}

// Fetch all available services (that the user hasn't purchased)
$available_services = array();
$sql = "SELECT s.* FROM services s 
        WHERE s.id NOT IN (
            SELECT p.service_id FROM purchases p WHERE p.user_id = ? AND p.status = 'active'
        )";

if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        while($row = mysqli_fetch_assoc($result)){
            $available_services[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}

// Handle support message submission
$message_success = $message_error = "";
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_support"])){
    $subject = trim($_POST["subject"]);
    $message = trim($_POST["message"]);
    
    // Validate input
    if(empty($subject)){
        $message_error = "Please enter a subject.";
    } elseif(empty($message)){
        $message_error = "Please enter your message.";
    } else {
        // Insert message into database
        $sql = "INSERT INTO support_messages (user_id, subject, message) VALUES (?, ?, ?)";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "iss", $user_id, $subject, $message);
            
            if(mysqli_stmt_execute($stmt)){
                $message_success = "Your message has been sent successfully. We'll respond as soon as possible.";
                // Redirect to avoid form resubmission
                header("location: dashboard.php?tab=support&success=1");
                exit;
            } else{
                $message_error = "Something went wrong. Please try again later.";
            }
            
            mysqli_stmt_close($stmt);
        }
    }
}

// Handle support message success notification
if(isset($_GET['success']) && $_GET['success'] == '1' && $active_tab == 'support'){
    $message_success = "Your message has been sent successfully. We'll respond as soon as possible.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Dulify</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    body {
        font-family: 'Poppins', sans-serif;
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
    .service-card {
        transition: all 0.3s ease;
    }
    .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .nav-link:hover {
        color: #4CAF50;
    }
    .dashboard-card {
        transition: all 0.3s ease;
    }
    .dashboard-card:hover {
        transform: scale(1.02);
    }
    .star-rating {
        display: inline-block;
        font-size: 0;
    }
    .star-rating input {
        display: none;
    }
    .star-rating label {
        font-size: 24px;
        color: #ddd;
        padding: 0 5px;
        cursor: pointer;
        display: inline-block;
    }
    .star-rating label:before {
        content: '★';
    }
    .star-rating input:checked ~ label {
        color: #FFC107;
    }
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: #FFC107;
    }
    .modal {
        display: none;
        position: fixed;
        z-index: 100;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
    }
    .modal-content {
        background-color: white;
        margin: 10% auto;
        padding: 30px;
        border-radius: 8px;
        max-width: 500px;
        position: relative;
    }
    .close {
        position: absolute;
        right: 20px;
        top: 15px;
        font-size: 24px;
        cursor: pointer;
    }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation - Matching main site -->
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
                    <a href="services.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Services</a>
                    <a href="dashboard.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
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
                <a href="about.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">About</a>
                <a href="services.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Services</a>
                <a href="dashboard.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Dashboard</a>
                <a href="php/logout.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Dashboard Section -->
    <section class="pt-24 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
            </div>
            
            <!-- Dashboard Navigation -->
            <ul class="flex border-b border-gray-200 text-gray-600 mb-6 flex-wrap">
                <li class="-mb-px mr-1">
                    <a href="?tab=services" class="inline-block py-2 px-4 <?php echo $active_tab == 'services' ? 'primary-text font-semibold border-b-2 border-green-500' : 'hover:text-gray-800'; ?>">My Services</a>
                </li>
                <li class="mr-1">
                    <a href="?tab=orders" class="inline-block py-2 px-4 <?php echo $active_tab == 'orders' ? 'primary-text font-semibold border-b-2 border-green-500' : 'hover:text-gray-800'; ?>">My Orders</a>
                </li>
                <li class="mr-1">
                    <a href="?tab=available" class="inline-block py-2 px-4 <?php echo $active_tab == 'available' ? 'primary-text font-semibold border-b-2 border-green-500' : 'hover:text-gray-800'; ?>">Available Services</a>
                </li>
                <li class="mr-1">
                    <a href="?tab=support" class="inline-block py-2 px-4 <?php echo $active_tab == 'support' ? 'primary-text font-semibold border-b-2 border-green-500' : 'hover:text-gray-800'; ?>">Customer Support</a>
                </li>
                <li class="mr-1">
                    <a href="?tab=profile" class="inline-block py-2 px-4 <?php echo $active_tab == 'profile' ? 'primary-text font-semibold border-b-2 border-green-500' : 'hover:text-gray-800'; ?>">My Profile</a>
                </li>
            </ul>
            
            <!-- Dashboard Content -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <?php 
                // Display error messages from service_access.php redirects
                if(isset($_GET['error'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">
                            <?php 
                            if($_GET['error'] == 'invalid_service') {
                                echo 'Invalid service ID. Please select a valid service.';
                            } else if($_GET['error'] == 'service_not_found') {
                                echo 'The requested service was not found or is no longer active.';
                            } else {
                                echo 'An error occurred while accessing the service.';
                            }
                            ?>
                        </span>
                    </div>
                <?php endif; ?>
                
                <?php if($active_tab == 'services'): ?>
                    <!-- My Services Tab -->
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">My Services</h2>
                    <?php if(empty($purchased_services)): ?>
                        <div class="text-center py-12">
                            <p class="text-gray-600 mb-4">You haven't purchased any services yet.</p>
                            <a href="dashboard.php?tab=available" class="primary-bg hover:bg-green-600 text-white px-6 py-2 rounded-lg font-medium transition duration-300 inline-block">Browse Available Services</a>
                        </div>
                    <?php else: ?>
                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach($purchased_services as $service): ?>
                                <div class="service-card bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 dashboard-card">
                                    <div class="h-48 bg-gray-100 flex items-center justify-center">
                                        <?php if(!empty($service["image"])): ?>
                                            <img src="img/services/<?php echo $service["image"]; ?>" alt="<?php echo $service["name"]; ?>" class="h-full w-full object-cover">
                                        <?php else: ?>
                                            <i class="fas fa-laptop-code text-5xl text-gray-400"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="p-6">
                                        <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($service["name"]); ?></h3>
                                        <p class="text-gray-600 mb-4"><?php echo substr(htmlspecialchars($service["description"]), 0, 100); ?>...</p>
                                        
                                        <div class="flex justify-between items-center mb-4">
                                            <span class="text-sm font-medium <?php echo $service["status"] == 'active' ? 'text-green-600' : 'text-red-600'; ?>">
                                                <?php echo ucfirst($service["status"]); ?>
                                            </span>
                                            <span class="text-sm text-gray-500">
                                                <?php echo date("M d, Y", strtotime($service["purchase_date"])); ?>
                                            </span>
                                        </div>
                                        
                                        <?php if($service["status"] == 'active'): ?>
                                            <a href="service_access.php?id=<?php echo $service["id"]; ?>" class="primary-bg hover:bg-green-600 text-white px-4 py-2 rounded-md font-medium transition duration-300 w-full block text-center">Access Service</a>
                                        <?php else: ?>
                                            <a href="php/purchase.php?id=<?php echo $service["service_id"]; ?>" class="border border-green-500 text-green-500 hover:bg-green-500 hover:text-white px-4 py-2 rounded-md font-medium transition duration-300 w-full block text-center">Renew Service</a>
                                        <?php endif; ?>
                                        
                                        <!-- Testimonial button/status -->
                                        <div class="mt-3">
                                            <?php if($service["has_testimonial"] > 0): ?>
                                                <button class="edit-testimonial text-sm text-green-600 hover:text-green-800 font-medium w-full text-center py-1"
                                                        data-service="<?php echo htmlspecialchars($service["name"]); ?>" 
                                                        data-id="<?php echo $service["service_id"]; ?>" 
                                                        data-rating="<?php echo $service["user_rating"]; ?>" 
                                                        data-comment="<?php echo htmlspecialchars($service["user_comment"]); ?>">
                                                    <i class="fas fa-edit mr-1"></i> Edit Your Review
                                                </button>
                                            <?php else: ?>
                                                <button class="add-testimonial text-sm text-yellow-600 hover:text-yellow-800 font-medium w-full text-center py-1"
                                                        data-service="<?php echo htmlspecialchars($service["name"]); ?>" 
                                                        data-id="<?php echo $service["service_id"]; ?>">
                                                    <i class="fas fa-star mr-1"></i> Leave a Review
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                
                <?php elseif($active_tab == 'orders'): ?>
                    <!-- My Orders Tab -->
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">My Orders</h2>
                    
                    <?php if(empty($pending_orders)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-clipboard-list text-5xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">You don't have any orders yet.</p>
                            <a href="services.php" class="primary-bg hover:bg-green-600 text-white px-6 py-2 rounded-lg font-medium inline-block mt-4">Browse Services</a>
                        </div>
                    <?php else: ?>
                        <div class="bg-white rounded-lg p-4 mb-8">
                            <h3 class="text-xl font-semibold mb-4">Order Status</h3>
                            <div class="flex flex-wrap gap-2 mb-4">
                                <div class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 text-sm flex items-center">
                                    <span class="w-2 h-2 rounded-full bg-yellow-500 mr-1"></span>
                                    Pending
                                </div>
                                <div class="px-3 py-1 rounded-full bg-green-100 text-green-800 text-sm flex items-center">
                                    <span class="w-2 h-2 rounded-full bg-green-500 mr-1"></span>
                                    Approved
                                </div>
                                <div class="px-3 py-1 rounded-full bg-red-100 text-red-800 text-sm flex items-center">
                                    <span class="w-2 h-2 rounded-full bg-red-500 mr-1"></span>
                                    Rejected
                                </div>
                            </div>
                            <p class="text-sm text-gray-600">Once your order is approved, the service will appear in your "My Services" tab.</p>
                        </div>
                        
                        <div class="grid md:grid-cols-1 gap-6">
                            <?php foreach($pending_orders as $order): ?>
                                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                                    <div class="p-6">
                                        <div class="flex flex-col md:flex-row md:items-center justify-between mb-4">
                                            <div>
                                                <h3 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($order['service_name']); ?></h3>
                                            </div>
                                            <div class="mt-2 md:mt-0">
                                                <?php if($order['status'] == 'pending'): ?>
                                                    <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 text-sm inline-flex items-center">
                                                        <span class="w-2 h-2 rounded-full bg-yellow-500 mr-1"></span>
                                                        Pending
                                                    </span>
                                                <?php elseif($order['status'] == 'approved'): ?>
                                                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-800 text-sm inline-flex items-center">
                                                        <span class="w-2 h-2 rounded-full bg-green-500 mr-1"></span>
                                                        Approved
                                                    </span>
                                                <?php elseif($order['status'] == 'rejected'): ?>
                                                    <span class="px-3 py-1 rounded-full bg-red-100 text-red-800 text-sm inline-flex items-center">
                                                        <span class="w-2 h-2 rounded-full bg-red-500 mr-1"></span>
                                                        Rejected
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <p class="text-sm text-gray-600">Order Date:</p>
                                                <p class="font-medium"><?php echo date('F j, Y', strtotime($order['order_date'])); ?></p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600">Order #:</p>
                                                <p class="font-medium"><?php echo $order['id']; ?></p>
                                            </div>
                                        </div>
                                        
                                        <?php if($order['status'] == 'rejected' && !empty($order['admin_notes'])): ?>
                                            <div class="p-4 bg-red-50 rounded-lg mb-4">
                                                <p class="text-sm font-medium text-red-800 mb-1">Rejection Reason:</p>
                                                <p class="text-sm text-red-700"><?php echo htmlspecialchars($order['admin_notes']); ?></p>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if($order['status'] == 'pending'): ?>
                                            <div class="text-sm text-gray-600 italic">
                                                Your order is being reviewed by our team. You'll be notified once it's approved.
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                <?php elseif($active_tab == 'available'): ?>
                    <!-- Available Services Tab -->
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Available Services</h2>
                    <?php if(empty($available_services)): ?>
                        <div class="text-center py-12">
                            <p class="text-gray-600 mb-4">You've purchased all available services!</p>
                            <p class="text-gray-600">Thank you for your support.</p>
                        </div>
                    <?php else: ?>
                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach($available_services as $service): ?>
                                <div class="service-card bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 dashboard-card">
                                    <div class="h-48 bg-gray-100 flex items-center justify-center">
                                        <?php if(!empty($service["image"])): ?>
                                            <img src="img/services/<?php echo $service["image"]; ?>" alt="<?php echo $service["name"]; ?>" class="h-full w-full object-cover">
                                        <?php else: ?>
                                            <i class="fas fa-laptop-code text-5xl text-gray-400"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="p-6">
                                        <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($service["name"]); ?></h3>
                                        <p class="text-gray-600 mb-4"><?php echo substr(htmlspecialchars($service["description"]), 0, 100); ?>...</p>
                                        
                                        <div class="flex justify-between items-center mb-4">
                                            <span class="text-lg font-semibold primary-text">Contact for pricing</span>
                                        </div>
                                        
                                        <div class="space-y-2">
                                            <a href="php/purchase.php?id=<?php echo $service["id"]; ?>" class="primary-bg hover:bg-green-600 text-white px-4 py-2 rounded-md font-medium transition duration-300 w-full block text-center">Purchase</a>
                                            <a href="services.php?id=<?php echo $service["id"]; ?>" class="border border-green-500 text-green-500 hover:bg-green-500 hover:text-white px-4 py-2 rounded-md font-medium transition duration-300 w-full block text-center">Learn More</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                
                <?php elseif($active_tab == 'support'): ?>
                    <!-- Customer Support Tab -->
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Customer Support</h2>
                    
                    <?php if($message_success): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                            <?php echo $message_success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($message_error): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                            <?php echo $message_error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- <div class="mb-10">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Send a New Message</h3>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?tab=support"); ?>" method="post" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                                <input type="text" name="subject" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition duration-300" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                                <textarea name="message" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition duration-300" required></textarea>
                            </div>
                            <div>
                                <button type="submit" name="submit_support" class="primary-bg hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition duration-300">Send Message</button>
                            </div>
                        </form>
                    </div> -->
                    
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">My Support Messages</h3>
                        <?php if(empty($support_messages)): ?>
                            <div class="text-center py-8">
                                <p class="text-gray-600">You haven't sent any support messages yet.</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach($support_messages as $msg): ?>
                                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                                        <div class="flex justify-between items-start mb-3">
                                            <h4 class="text-lg font-medium text-gray-800"><?php echo htmlspecialchars($msg["subject"]); ?></h4>
                                            <span class="px-3 py-1 rounded-full text-xs font-medium <?php 
                                                if($msg["status"] == 'open') echo 'bg-blue-100 text-blue-800';
                                                elseif($msg["status"] == 'in_progress') echo 'bg-yellow-100 text-yellow-800';
                                                else echo 'bg-green-100 text-green-800';
                                            ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $msg["status"])); ?>
                                            </span>
                                        </div>
                                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($msg["message"]); ?></p>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-500">
                                                <?php echo date("M d, Y \a\\t h:i A", strtotime($msg["created_at"])); ?>
                                            </span>
                                            <a href="support_details.php?id=<?php echo $msg["id"]; ?>" class="text-sm primary-text hover:text-green-600 font-medium">View Conversation</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                
                <?php elseif($active_tab == 'profile'): ?>
                    <!-- Profile Tab -->
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">My Profile</h2>
                    
                    <?php
                    // Fetch user data
                    $sql = "SELECT * FROM users WHERE id = ?";
                    $user_data = null;
                    
                    if($stmt = mysqli_prepare($conn, $sql)){
                        mysqli_stmt_bind_param($stmt, "i", $user_id);
                        if(mysqli_stmt_execute($stmt)){
                            $result = mysqli_stmt_get_result($stmt);
                            if(mysqli_num_rows($result) == 1){
                                $user_data = mysqli_fetch_assoc($result);
                            }
                        }
                        mysqli_stmt_close($stmt);
                    }
                    ?>
                    
                    <?php if($user_data): ?>
                        <div class="max-w-2xl mx-auto">
                            <div class="bg-gray-50 p-8 rounded-xl border border-gray-200">
                                <div class="grid md:grid-cols-2 gap-6 mb-8">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                        <div class="bg-white px-4 py-2 rounded-md border border-gray-300"><?php echo htmlspecialchars($user_data["username"]); ?></div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <div class="bg-white px-4 py-2 rounded-md border border-gray-300"><?php echo htmlspecialchars($user_data["email"]); ?></div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Business Name</label>
                                        <div class="bg-white px-4 py-2 rounded-md border border-gray-300"><?php echo htmlspecialchars($user_data["business_name"]); ?></div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Business Type</label>
                                        <div class="bg-white px-4 py-2 rounded-md border border-gray-300"><?php echo ucfirst(str_replace('_', ' ', $user_data["business_type"])); ?></div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                        <div class="bg-white px-4 py-2 rounded-md border border-gray-300"><?php echo htmlspecialchars($user_data["phone"] ?: 'Not provided'); ?></div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Member Since</label>
                                        <div class="bg-white px-4 py-2 rounded-md border border-gray-300"><?php echo date("F d, Y", strtotime($user_data["created_at"])); ?></div>
                                    </div>
                                </div>
                                <div class="flex flex-wrap justify-center gap-4">
                                    <a href="edit_profile.php" class="primary-bg hover:bg-green-600 text-white px-6 py-2 rounded-lg font-medium transition duration-300">Edit Profile</a>
                                    <a href="change_password.php" class="border border-green-500 text-green-500 hover:bg-green-500 hover:text-white px-6 py-2 rounded-lg font-medium transition duration-300">Change Password</a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12">
                            <p class="text-gray-600">Unable to load profile information. Please try again later.</p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Testimonial Modal -->
    <div id="testimonial-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="testimonial-title" class="text-2xl font-bold text-gray-800 mb-4">Leave a Review</h2>
            
            <?php if($testimonial_success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo $testimonial_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if($testimonial_error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $testimonial_message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="dashboard.php?tab=services" class="space-y-4">
                <input type="hidden" name="service_id" id="testimonial-service-id" value="">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Service</label>
                    <div id="testimonial-service-name" class="font-medium text-gray-800"></div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                    <div class="star-rating">
                        <input type="radio" id="star5" name="rating" value="5" /><label for="star5" title="5 stars"></label>
                        <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="4 stars"></label>
                        <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="3 stars"></label>
                        <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="2 stars"></label>
                        <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="1 star"></label>
                    </div>
                </div>
                
                <div>
                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">Your Review</label>
                    <textarea name="comment" id="testimonial-comment" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition duration-300" required></textarea>
                </div>
                
                <div>
                    <button type="submit" name="submit_testimonial" class="primary-bg hover:bg-green-600 text-white px-6 py-2 rounded-lg font-medium transition duration-300 w-full">Submit Review</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer - Matching main site -->
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

        // Modal functionality for testimonials
        const testimonialModal = document.getElementById("testimonial-modal");
        const addButtons = document.querySelectorAll(".add-testimonial");
        const editButtons = document.querySelectorAll(".edit-testimonial");
        const closeButton = document.querySelector(".close");
        const testimonialTitle = document.getElementById("testimonial-title");
        const testimonialServiceId = document.getElementById("testimonial-service-id");
        const testimonialServiceName = document.getElementById("testimonial-service-name");
        const testimonialComment = document.getElementById("testimonial-comment");
        const ratingInputs = document.querySelectorAll('input[name="rating"]');

        // Add testimonial buttons
        addButtons.forEach(button => {
            button.addEventListener("click", function() {
                const serviceId = this.getAttribute("data-id");
                const serviceName = this.getAttribute("data-service");
                
                testimonialTitle.textContent = "Leave a Review";
                testimonialServiceId.value = serviceId;
                testimonialServiceName.textContent = serviceName;
                testimonialComment.value = "";
                
                // Default rating to 5 stars
                document.getElementById("star5").checked = true;
                
                testimonialModal.style.display = "block";
            });
        });

        // Edit testimonial buttons
        editButtons.forEach(button => {
            button.addEventListener("click", function() {
                const serviceId = this.getAttribute("data-id");
                const serviceName = this.getAttribute("data-service");
                const rating = this.getAttribute("data-rating");
                const comment = this.getAttribute("data-comment");
                
                testimonialTitle.textContent = "Edit Your Review";
                testimonialServiceId.value = serviceId;
                testimonialServiceName.textContent = serviceName;
                testimonialComment.value = comment;
                
                // Set the rating
                document.getElementById("star" + rating).checked = true;
                
                testimonialModal.style.display = "block";
            });
        });

        // Close modal
        closeButton.addEventListener("click", function() {
            testimonialModal.style.display = "none";
        });

        // Close modal when clicking outside
        window.addEventListener("click", function(event) {
            if (event.target == testimonialModal) {
                testimonialModal.style.display = "none";
            }
        });
    </script>
</body>
</html>