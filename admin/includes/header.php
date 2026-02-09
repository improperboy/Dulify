<?php
// Include database configuration if not already included
if (!isset($conn)) {
    require_once "../includes/config.php";
}

// Check if user is logged in and is an admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_type"] !== "admin") {
    header("location: ../php/login.php");
    exit;
}

// Get admin information
$admin_id = $_SESSION["id"];
$username = $_SESSION["username"];

// Count unread contact messages
$unread_messages = 0;
$sql = "SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0";
if($result = mysqli_query($conn, $sql)) {
    $row = mysqli_fetch_assoc($result);
    $unread_messages = $row["count"];
}

// Count pending orders
$pending_orders = 0;
// Check if orders table exists
$table_exists = $conn->query("SHOW TABLES LIKE 'orders'");
if($table_exists->num_rows > 0) {
    $sql = "SELECT COUNT(*) as count FROM orders WHERE status = 'pending'";
    if($result = mysqli_query($conn, $sql)) {
        $row = mysqli_fetch_assoc($result);
        $pending_orders = $row["count"];
    }
}

// Get current page for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Admin Dashboard'; ?> - Dulify</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .admin-container {
            display: flex;
            min-height: 100vh;
            position: relative;
        }
        
        .admin-sidebar {
            width: 250px;
            background-color: #1a1a2e;
            color: white;
            padding: 20px;
            position: fixed;
            height: 100%;
            overflow-y: auto;
            transition: transform 0.3s ease;
            z-index: 1000;
        }
        
        /* Sidebar overlay for mobile */
        #sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        
        #sidebar-overlay.active {
            display: block;
        }
        
        /* Mobile toggle button */
        #sidebar-toggle {
            display: none;
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1001;
            background-color: #1a1a2e;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: left 0.3s ease;
        }
        
        #sidebar-toggle.open {
            left: 260px;
        }
        
        #sidebar-toggle i {
            font-size: 18px;
        }
        
        .admin-content {
            flex: 1;
            padding: 20px;
            background-color: #f5f5f5;
            margin-left: 250px;
            transition: margin-left 0.3s ease;
        }
        
        /* Responsive styles */
        @media (max-width: 991px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            
            .admin-sidebar.open {
                transform: translateX(0);
            }
            
            .admin-content {
                margin-left: 0;
                width: 100%;
            }
            
            #sidebar-toggle {
                display: block;
            }
        }
        
        .admin-sidebar h2 {
            padding-bottom: 20px;
            border-bottom: 1px solid #333;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .admin-nav {
            list-style: none;
            padding: 0;
        }
        
        .admin-nav li {
            margin-bottom: 5px;
        }
        
        .admin-nav a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px 15px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .admin-nav a:hover, .admin-nav a.active {
            background-color: #16213e;
        }
        
        .admin-nav a.active {
            border-left: 4px solid #4361ee;
        }
        
        .admin-nav .badge {
            float: right;
            margin-left: 5px;
            padding: 3px 7px;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 10px;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .admin-nav i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .greeting {
            margin-bottom: 30px;
        }
        
        .greeting h1 {
            color: #1a1a2e;
            margin-bottom: 5px;
        }
        
        .greeting p {
            color: #666;
        }
        
        .admin-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .admin-table th, .admin-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .admin-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #1a1a2e;
        }
        
        .admin-table tbody tr:hover {
            background-color: #f5f5f5;
        }
        
        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .badge-success {
            background-color: #d1fae5;
            color: #047857;
        }
        
        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .badge-danger {
            background-color: #fee2e2;
        
            color: #b91c1c;
        }
        
        .unread-badge {
            background-color: #ef4444;
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: 5px;
        }
        
        .action-btn {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin-right: 5px;
            transition: all 0.3s;
        }
        
        .btn-view {
            background-color: #e0f2fe;
            color: #0369a1;
        }
        
        .btn-edit {
            background-color: #fff7ed;
            color: #c2410c;
        }
        
        .btn-delete {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        
        .filter-tabs {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 20px;
            gap: 10px;
        }
        
        .filter-tab {
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 500;
            background-color: #e0e0e0;
            transition: all 0.3s;
        
            text-decoration: none;
            color: #333;
        }
        
        .filter-tab.active {
            background-color: #4cb4ff;
            color: white;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
            position: relative;
        }
        
        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                margin: 5% auto;
            }
            
            .filter-tabs {
                justify-content: center;
            }
            
            .admin-table {
                display: block;
                overflow-x: auto;
            }
        }
        
        .close {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
    <?php if (isset($additional_css)): ?>
    <style>
        <?php echo $additional_css; ?>
    </style>
    <?php endif; ?>
</head>
<body>
    <!-- Mobile Sidebar Toggle Button -->
    <button id="sidebar-toggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Sidebar Overlay (only visible on mobile when sidebar is open) -->
    <div id="sidebar-overlay"></div>
    
    <div class="admin-container">
        <!-- Admin Sidebar -->
        <div class="admin-sidebar">
            <h2>Dulify Admin</h2>
            <ul class="admin-nav">
                <li><a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a></li>
                <li><a href="users.php" class="<?php echo $current_page == 'users.php' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Users
                </a></li>
                <li><a href="services.php" class="<?php echo $current_page == 'services.php' ? 'active' : ''; ?>">
                    <i class="fas fa-cogs"></i> Services
                </a></li>
                <li><a href="purchases.php" class="<?php echo $current_page == 'purchases.php' ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart"></i> Purchases
                </a></li>
                <li><a href="orders.php" class="<?php echo $current_page == 'orders.php' ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-check"></i> Orders
                    <?php if($pending_orders > 0): ?>
                        <span class="badge badge-pill badge-warning"><?php echo $pending_orders; ?></span>
                    <?php endif; ?>
                </a></li>
                <li><a href="messages.php" class="<?php echo $current_page == 'messages.php' ? 'active' : ''; ?>">
                    <i class="fas fa-envelope"></i> Messages 
                    <?php if($unread_messages > 0): ?>
                        <span class="unread-badge"><?php echo $unread_messages; ?></span>
                    <?php endif; ?>
                </a></li>
                <li><a href="support.php" class="<?php echo $current_page == 'support.php' ? 'active' : ''; ?>">
                    <i class="fas fa-headset"></i> Support
                </a></li>
                <li><a href="testimonials.php" class="<?php echo $current_page == 'testimonials.php' ? 'active' : ''; ?>">
                    <i class="fas fa-star"></i> Testimonials
                </a></li>
                <li><a href="../php/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a></li>
            </ul>
        </div>
        
        <!-- Admin Content -->
        <div class="admin-content">
