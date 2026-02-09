<?php
// Include database configuration
require_once "../includes/config.php";

// Check if user is logged in, if not redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Check if service ID is provided
if(!isset($_GET["id"]) || empty($_GET["id"])){
    header("location: ../dashboard.php");
    exit;
}

$service_id = $_GET["id"];
$user_id = $_SESSION["id"];
$order_success = $order_error = "";

// Fetch service details
$sql = "SELECT * FROM services WHERE id = ?";
$service = null;

if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $service_id);
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        if(mysqli_num_rows($result) == 1){
            $service = mysqli_fetch_assoc($result);
        } else {
            // Service not found
            header("location: ../dashboard.php");
            exit;
        }
    }
    mysqli_stmt_close($stmt);
}

// Check if service is already purchased and active
$sql = "SELECT * FROM purchases WHERE user_id = ? AND service_id = ? AND status = 'active'";
$already_purchased = false;

if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $service_id);
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        if(mysqli_num_rows($result) > 0){
            $already_purchased = true;
        }
    }
    mysqli_stmt_close($stmt);
}

// Check if this service is already in a pending order
$sql = "SELECT * FROM orders WHERE user_id = ? AND service_id = ? AND status = 'pending'";
$pending_order = false;

if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $service_id);
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        if(mysqli_num_rows($result) > 0){
            $pending_order = true;
        }
    }
    mysqli_stmt_close($stmt);
}

// Process order when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["place_order"])){
    // Validate form data
    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $additional_notes = trim($_POST["additional_notes"]);
    
    $input_error = false;
    
    // Validate inputs
    if(empty($full_name)){
        $order_error = "Please enter your full name.";
        $input_error = true;
    } elseif(empty($email)){
        $order_error = "Please enter your email address.";
        $input_error = true;
    } elseif(empty($phone)){
        $order_error = "Please enter your phone number.";
        $input_error = true;
    } elseif(empty($address)){
        $order_error = "Please enter your address.";
        $input_error = true;
    }
    
    if(!$input_error){
        // Insert order record
        $sql = "INSERT INTO orders (user_id, service_id, full_name, email, phone, address, additional_notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "iisssss", $user_id, $service_id, $full_name, $email, $phone, $address, $additional_notes);
            
            if(mysqli_stmt_execute($stmt)){
                $order_success = "Your order has been placed successfully! We will review your order and notify you once it's approved.";
            } else{
                $order_error = "Something went wrong. Please try again later.";
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
    <title>Purchase Service - Dulify</title>
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
        .nav-link:hover {
            color: #4CAF50;
        }
        .btn {
            transition: all 0.3s ease;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        .btn-primary:hover {
            background-color: #3e8e41;
        }
        .btn-outline {
            border: 2px solid #4CAF50;
            color: #4CAF50;
            background: transparent;
        }
        .btn-outline:hover {
            background-color: #4CAF50;
            color: white;
        }
        .form-control {
            width: 100%;
            padding: 0.5rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.25);
            border-color: #4CAF50;
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
                <div class="hidden md:flex items-center space-x-4">
                    <a href="../index.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Home</a>
                    <a href="../services.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Services</a>
                    <a href="../about.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">About</a>
                    <a href="../dashboard.php" class="primary-bg hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-300">Dashboard</a>
                    <a href="logout.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition duration-300">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Purchase Section -->
    <section class="pt-24 pb-12 md:pt-32 md:pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php if($order_success): ?>
                <div class="max-w-md mx-auto text-center">
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        <?php echo $order_success; ?>
                    </div>
                    <a href="../dashboard.php" class="primary-bg hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium inline-block">Go to Dashboard</a>
                </div>
            <?php elseif($already_purchased): ?>
                <div class="max-w-md mx-auto text-center">
                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6">
                        You've already purchased this service and it's currently active. You can access it from your dashboard.
                    </div>
                    <a href="../dashboard.php" class="primary-bg hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium inline-block">Go to Dashboard</a>
                </div>
            <?php elseif($pending_order): ?>
                <div class="max-w-md mx-auto text-center">
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
                        You already have a pending order for this service. Please wait for admin approval.
                    </div>
                    <a href="../dashboard.php" class="primary-bg hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium inline-block">Go to Dashboard</a>
                </div>
            <?php elseif($service): ?>
                <div class="max-w-2xl mx-auto">
                    <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Order Service</h1>
                    
                    <?php if($order_error): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                            <?php echo $order_error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="p-6 md:p-8">
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4"><?php echo htmlspecialchars($service["name"]); ?></h2>
                            <!-- <div class="text-2xl primary-text font-semibold mb-6">$<?php echo $service["price"]; ?></div> -->
                            <p class="text-gray-600 mb-8"><?php echo htmlspecialchars($service["description"]); ?></p>
                            
                            <div class="mb-6">
                                <div class="font-medium text-gray-800 mb-1">Subscription Period:</div>
                                <div class="text-gray-600">1 year</div>
                            </div>
                            
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $service_id; ?>" class="mt-8">
                                <div class="space-y-6">
                                    <div>
                                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name*</label>
                                        <input type="text" name="full_name" id="full_name" required class="form-control" placeholder="Your full name">
                                    </div>
                                    
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address*</label>
                                        <input type="email" name="email" id="email" required class="form-control" placeholder="Your email address">
                                    </div>
                                    
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number*</label>
                                        <input type="tel" name="phone" id="phone" required class="form-control" placeholder="Your phone number">
                                    </div>
                                    
                                    <div>
                                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address*</label>
                                        <textarea name="address" id="address" required class="form-control" rows="3" placeholder="Your complete address"></textarea>
                                    </div>
                                    
                                    <div>
                                        <label for="additional_notes" class="block text-sm font-medium text-gray-700 mb-1">Additional Notes</label>
                                        <textarea name="additional_notes" id="additional_notes" class="form-control" rows="3" placeholder="Any additional information or special requirements"></textarea>
                                    </div>
                                    
                                    <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                                        <button type="submit" name="place_order" class="primary-bg hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium w-full sm:w-auto">Place Order</button>
                                        <a href="../dashboard.php" class="border-2 border-green-500 text-green-500 hover:bg-green-500 hover:text-white px-6 py-3 rounded-lg font-medium text-center w-full sm:w-auto">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center">
                    <p class="text-gray-600 mb-6">Service not found. Please try again later.</p>
                    <a href="../dashboard.php" class="primary-bg hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium inline-block">Return to Dashboard</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8">
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
                        <li><a href="../index.php" class="text-gray-400 hover:text-white transition duration-300">Home</a></li>
                        <li><a href="../services.php" class="text-gray-400 hover:text-white transition duration-300">Services</a></li>
                        <li><a href="../about.php" class="text-gray-400 hover:text-white transition duration-300">About Us</a></li>
                        <li><a href="../dashboard.php" class="text-gray-400 hover:text-white transition duration-300">Dashboard</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact Us</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li>Email: hello@dulify.com</li>
                        <li>Phone: +91 79915 15802</li>
                        <li>Address: Pukhrayan, Kanpur Dehat, Uttar Pradesh 209111</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; <?php echo date("Y"); ?> Dulify. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
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