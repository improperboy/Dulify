<?php
// Include database configuration
require_once "../includes/config.php";

// Check if user is logged in and is an admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_type"] !== "admin") {
    header("location: ../php/login.php");
    exit;
}

// Get admin information
$admin_id = $_SESSION["id"];
$username = $_SESSION["username"];

// Default dashboard statistics
$total_users = 0;
$total_services = 0;
$total_purchases = 0;
$total_open_tickets = 0;

// Count users
$sql = "SELECT COUNT(*) as count FROM users WHERE user_type = 'user'";
if($result = mysqli_query($conn, $sql)) {
    $row = mysqli_fetch_assoc($result);
    $total_users = $row["count"];
}

// Count services
$sql = "SELECT COUNT(*) as count FROM services";
if($result = mysqli_query($conn, $sql)) {
    $row = mysqli_fetch_assoc($result);
    $total_services = $row["count"];
}

// Count purchases
$sql = "SELECT COUNT(*) as count FROM purchases";
if($result = mysqli_query($conn, $sql)) {
    $row = mysqli_fetch_assoc($result);
    $total_purchases = $row["count"];
}

// Count open support tickets
$sql = "SELECT COUNT(*) as count FROM support_messages WHERE status = 'open'";
if($result = mysqli_query($conn, $sql)) {
    $row = mysqli_fetch_assoc($result);
    $total_open_tickets = $row["count"];
}

// Count unread contact messages
$unread_messages = 0;

// Check if the contact_messages table exists
$table_exists = false;
$check_table_sql = "SHOW TABLES LIKE 'contact_messages'";
$check_result = mysqli_query($conn, $check_table_sql);

if($check_result && mysqli_num_rows($check_result) > 0) {
    $table_exists = true;
} else {
    // Create the table if it doesn't exist
    $create_table_sql = "CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY, 
        name VARCHAR(100) NOT NULL, 
        email VARCHAR(100) NOT NULL, 
        message TEXT NOT NULL, 
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_read BOOLEAN DEFAULT 0
    )";
    
    if(mysqli_query($conn, $create_table_sql)) {
        $table_exists = true;
    }
}

// Only query the table if it exists
if($table_exists) {
    $sql = "SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0";
    if($result = mysqli_query($conn, $sql)) {
        $row = mysqli_fetch_assoc($result);
        $unread_messages = $row["count"];
    }
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

// Count testimonials
$total_testimonials = 0;
$active_testimonials = 0;

// Check if testimonials table exists
$table_exists = false;
$tables_result = mysqli_query($conn, "SHOW TABLES LIKE 'testimonials'");
if($tables_result) {
    if(mysqli_num_rows($tables_result) > 0) {
        $table_exists = true;
    }
}

if($table_exists) {
    // Count total testimonials
    $sql = "SELECT COUNT(*) as count FROM testimonials";
    if($result = mysqli_query($conn, $sql)) {
        $row = mysqli_fetch_assoc($result);
        $total_testimonials = $row["count"];
    }
    
    // Count active testimonials
    $sql = "SELECT COUNT(*) as count FROM testimonials WHERE status = 'active'";
    if($result = mysqli_query($conn, $sql)) {
        $row = mysqli_fetch_assoc($result);
        $active_testimonials = $row["count"];
    }
}

// Get recent support tickets
$recent_tickets = [];
$sql = "SELECT sm.*, u.username FROM support_messages sm 
        JOIN users u ON sm.user_id = u.id 
        WHERE sm.status = 'open' 
        ORDER BY sm.created_at DESC LIMIT 5";
if($result = mysqli_query($conn, $sql)) {
    while($row = mysqli_fetch_assoc($result)) {
        $recent_tickets[] = $row;
    }
}

// Get recent purchases
$recent_purchases = [];
$sql = "SELECT p.*, u.username, s.name as service_name FROM purchases p 
        JOIN users u ON p.user_id = u.id 
        JOIN services s ON p.service_id = s.id 
        ORDER BY p.purchase_date DESC LIMIT 5";
if($result = mysqli_query($conn, $sql)) {
    while($row = mysqli_fetch_assoc($result)) {
        $recent_purchases[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Dulify</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .unread-badge {
            background-color: #FFC107;
            color: #fff;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75em;
            margin-left: 5px;
        }
    </style>
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
        .stats-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #4361ee;
        }
        .stat-card h3 {
            font-size: 1.8rem;
            margin: 10px 0;
            color: #1a1a2e;
        }
        .stat-card p {
            color: #666;
            margin: 0;
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
        .view-all {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #4361ee;
            text-decoration: none;
            font-weight: 500;
        }
        .view-all:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Admin Sidebar -->
        <div class="admin-sidebar">
            <h2>Dulify Admin</h2>
            <ul class="admin-nav">
                <li><a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="services.php"><i class="fas fa-cogs"></i> Services</a></li>
                <li><a href="purchases.php"><i class="fas fa-shopping-cart"></i> Purchases</a></li>
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
            <div class="greeting">
                <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
                <p>Here's an overview of your administration panel.</p>
            </div>
            
            <!-- Stats Cards -->
            <div class="stats-container">
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <h3><?php echo $total_users; ?></h3>
                    <p>Total Users</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-cogs"></i>
                    <h3><?php echo $total_services; ?></h3>
                    <p>Total Services</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-shopping-cart"></i>
                    <h3><?php echo $total_purchases; ?></h3>
                    <p>Total Purchases</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-ticket-alt"></i>
                    <h3><?php echo $total_open_tickets; ?></h3>
                    <p>Open Support Tickets</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-star"></i>
                    <h3><?php echo $total_testimonials; ?></h3>
                    <p>Testimonials<?php if($active_testimonials > 0): ?> (<?php echo $active_testimonials; ?> active)<?php endif; ?></p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-envelope"></i>
                    <h3><?php echo $unread_messages; ?></h3>
                    <p>Unread Messages</p>
                </div>
            </div>
            
            <!-- Recent Support Tickets -->
            <div class="admin-card">
                <h2>Recent Support Tickets</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($recent_tickets)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">No recent support tickets found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($recent_tickets as $ticket): ?>
                                <tr>
                                    <td>#<?php echo $ticket['id']; ?></td>
                                    <td><?php echo htmlspecialchars($ticket['username']); ?></td>
                                    <td><?php echo htmlspecialchars($ticket['subject']); ?></td>
                                    <td>
                                        <?php if($ticket['status'] == 'open'): ?>
                                            <span class="badge badge-danger">Open</span>
                                        <?php elseif($ticket['status'] == 'in_progress'): ?>
                                            <span class="badge badge-warning">In Progress</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">Closed</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($ticket['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <a href="support.php" class="view-all">View All Support Tickets <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <!-- Recent Purchases -->
            <div class="admin-card">
                <h2>Recent Purchases</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($recent_purchases)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">No recent purchases found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($recent_purchases as $purchase): ?>
                                <tr>
                                    <td>#<?php echo $purchase['id']; ?></td>
                                    <td><?php echo htmlspecialchars($purchase['username']); ?></td>
                                    <td><?php echo htmlspecialchars($purchase['service_name']); ?></td>
                                    <td>
                                        <?php if($purchase['status'] == 'active'): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php elseif($purchase['status'] == 'expired'): ?>
                                            <span class="badge badge-danger">Expired</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Cancelled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($purchase['purchase_date'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <a href="purchases.php" class="view-all">View All Purchases <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
    
    <script>
        // Any JavaScript functionality can be added here
    </script>
</body>
</html>
