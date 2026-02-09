<?php
// Include database configuration
require_once "../includes/config.php";

// Check if user is logged in and is an admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_type"] !== "admin") {
    header("location: ../php/login.php");
    exit;
}
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

// Get admin information
$admin_id = $_SESSION["id"];
$username = $_SESSION["username"];

// Initialize variables
$purchases = [];
$success_message = $error_message = "";

// Count unread contact messages
$unread_messages = 0;
$sql = "SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0";
if($result = mysqli_query($conn, $sql)) {
    $row = mysqli_fetch_assoc($result);
    $unread_messages = $row["count"];
}

// Handle purchase status updates
if(isset($_GET['action']) && $_GET['action'] == 'update' && isset($_GET['id']) && isset($_GET['status'])) {
    $purchase_id = intval($_GET['id']);
    $new_status = $_GET['status'];
    
    if(in_array($new_status, ['active', 'expired', 'cancelled'])) {
        $update_sql = "UPDATE purchases SET status = ? WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $update_sql)) {
            mysqli_stmt_bind_param($stmt, "si", $new_status, $purchase_id);
            if(mysqli_stmt_execute($stmt)) {
                $success_message = "Purchase status updated successfully.";
            } else {
                $error_message = "Error updating purchase status.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Set default filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Fetch purchases with pagination
$limit = 10;  // Purchases per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Count total purchases for pagination
$count_sql = "SELECT COUNT(*) as total FROM purchases";
if($filter != 'all') {
    $count_sql .= " WHERE status = '$filter'";
}
$total_purchases = 0;

if($count_result = mysqli_query($conn, $count_sql)) {
    $count_row = mysqli_fetch_assoc($count_result);
    $total_purchases = $count_row['total'];
}

$total_pages = ceil($total_purchases / $limit);

// Fetch purchases
$sql = "SELECT p.*, u.username, s.name as service_name, s.price 
        FROM purchases p 
        JOIN users u ON p.user_id = u.id 
        JOIN services s ON p.service_id = s.id";
if($filter != 'all') {
    $sql .= " WHERE p.status = '$filter'";
}
$sql .= " ORDER BY p.purchase_date DESC LIMIT ? OFFSET ?";

if($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    while($row = mysqli_fetch_assoc($result)) {
        $purchases[] = $row;
    }
    
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Management - Dulify Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Admin Dashboard Specific Styles */
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        .admin-sidebar {
            width: 250px;
            background-color: #1a1a2e;
            color: white;
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        .admin-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            background-color: #f5f5f5;
            min-height: 100vh;
        }
        .admin-sidebar h2 {
            padding: 0 20px;
            margin-bottom: 30px;
            color: white;
        }
        .admin-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .admin-nav li a {
            display: block;
            padding: 15px 20px;
            color: #ddd;
            text-decoration: none;
            transition: all 0.3s;
        }
        .admin-nav li a:hover, .admin-nav li a.active {
            background-color: #16213e;
            color: white;
            border-left: 4px solid #4361ee;
        }
        .admin-nav li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        .admin-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .admin-card h2 {
            margin-top: 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            color: #1a1a2e;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
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
        .action-btn {
            padding: 8px 12px;
            border-radius: 4px;
            color: white;
            text-decoration: none;
            margin-right: 5px;
            font-size: 0.9rem;
        }
        .view-btn {
            background-color: #4361ee;
        }
        .status-btn {
            background-color: #0369a1;
        }
        .action-btn:hover {
            opacity: 0.9;
        }
        .filter-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .filter-tabs {
            display: flex;
            background-color: #f8f9fa;
            border-radius: 4px;
            overflow: hidden;
        }
        .filter-tabs a {
            padding: 10px 20px;
            text-decoration: none;
            color: #1a1a2e;
            transition: all 0.3s;
        }
        .filter-tabs a:hover {
            background-color: #e9ecef;
        }
        .filter-tabs a.active {
            background-color: #4361ee;
            color: white;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            list-style-type: none;
            padding: 0;
        }
        .pagination li {
            margin: 0 5px;
        }
        .pagination li a {
            display: block;
            padding: 8px 12px;
            background-color: white;
            border: 1px solid #ddd;
            color: #4361ee;
            text-decoration: none;
            border-radius: 4px;
        }
        .pagination li.active a {
            background-color: #4361ee;
            color: white;
            border-color: #4361ee;
        }
        .pagination li a:hover:not(.active) {
            background-color: #f5f5f5;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-success {
            color: #0f5132;
            background-color: #d1e7dd;
            border-color: #badbcc;
        }
        .alert-danger {
            color: #842029;
            background-color: #f8d7da;
            border-color: #f5c2c7;
        }
        .status-dropdown {
            display: inline-block;
            position: relative;
        }
        .status-dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            z-index: 1;
            border-radius: 4px;
            overflow: hidden;
        }
        .status-dropdown:hover .status-dropdown-content {
            display: block;
        }
        .status-dropdown-item {
            display: block;
            padding: 10px 15px;
            text-decoration: none;
            color: #333;
        }
        .status-dropdown-item:hover {
            background-color: #f5f5f5;
        }
        .price {
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Admin Sidebar -->
        <div class="admin-sidebar">
            <h2>Dulify Admin</h2>
            <ul class="admin-nav">
                <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="services.php"><i class="fas fa-cogs"></i> Services</a></li>
                <li><a href="purchases.php" class="active"><i class="fas fa-shopping-cart"></i> Purchases</a></li>
                   <li><a href="orders.php" class="<?php echo $current_page == 'orders.php' ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-check"></i> Orders
                    <?php if($pending_orders > 0): ?>
                        <span class="badge badge-pill badge-warning"><?php echo $pending_orders; ?></span>
                    <?php endif; ?>
                </a></li>
                <li><a href="support.php"><i class="fas fa-headset"></i> Support Tickets</a></li>
                <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages <?php if($unread_messages > 0): ?><span class="unread-badge"><?php echo $unread_messages; ?></span><?php endif; ?></a></li>
                <li><a href="testimonials.php"><i class="fas fa-star"></i> Testimonials</a></li>
                <li><a href="../php/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        
        <!-- Admin Content -->
        <div class="admin-content">
            <div class="admin-card">
                <h2>Purchase Management</h2>
                
                <!-- Success/Error Messages -->
                <?php if(!empty($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if(!empty($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <!-- Filter Tabs -->
                <div class="filter-container">
                    <div class="filter-tabs">
                        <a href="?filter=all" class="<?php echo $filter == 'all' ? 'active' : ''; ?>">All Purchases</a>
                        <a href="?filter=active" class="<?php echo $filter == 'active' ? 'active' : ''; ?>">Active</a>
                        <a href="?filter=expired" class="<?php echo $filter == 'expired' ? 'active' : ''; ?>">Expired</a>
                        <a href="?filter=cancelled" class="<?php echo $filter == 'cancelled' ? 'active' : ''; ?>">Cancelled</a>
                    </div>
                </div>
                
                <!-- Purchases Table -->
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Service</th>
                            <th>Price</th>
                            <th>Purchase Date</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($purchases)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center;">No purchases found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($purchases as $purchase): ?>
                                <tr>
                                    <td>#<?php echo $purchase['id']; ?></td>
                                    <td><?php echo htmlspecialchars($purchase['username']); ?></td>
                                    <td><?php echo htmlspecialchars($purchase['service_name']); ?></td>
                                    <td class="price">$<?php echo number_format($purchase['price'], 2); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($purchase['purchase_date'])); ?></td>
                                    <td>
                                        <?php 
                                        if($purchase['expiry_date']) {
                                            echo date('M d, Y', strtotime($purchase['expiry_date']));
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if($purchase['status'] == 'active'): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php elseif($purchase['status'] == 'expired'): ?>
                                            <span class="badge badge-danger">Expired</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Cancelled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="status-dropdown">
                                            <a href="#" class="action-btn status-btn"><i class="fas fa-cog"></i></a>
                                            <div class="status-dropdown-content">
                                                <a href="?action=update&id=<?php echo $purchase['id']; ?>&status=active&filter=<?php echo $filter; ?>" class="status-dropdown-item">Mark as Active</a>
                                                <a href="?action=update&id=<?php echo $purchase['id']; ?>&status=expired&filter=<?php echo $filter; ?>" class="status-dropdown-item">Mark as Expired</a>
                                                <a href="?action=update&id=<?php echo $purchase['id']; ?>&status=cancelled&filter=<?php echo $filter; ?>" class="status-dropdown-item">Mark as Cancelled</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <?php if($total_pages > 1): ?>
                <ul class="pagination">
                    <?php if($page > 1): ?>
                        <li><a href="?filter=<?php echo $filter; ?>&page=<?php echo $page-1; ?>"><i class="fas fa-chevron-left"></i></a></li>
                    <?php endif; ?>
                    
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li <?php if($i == $page) echo 'class="active"'; ?>>
                            <a href="?filter=<?php echo $filter; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if($page < $total_pages): ?>
                        <li><a href="?filter=<?php echo $filter; ?>&page=<?php echo $page+1; ?>"><i class="fas fa-chevron-right"></i></a></li>
                    <?php endif; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
