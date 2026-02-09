
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

// Initialize variables
$users = [];
$success_message = $error_message = "";

// Handle user deletion
if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    
    // Check if user exists and is not an admin
    $check_sql = "SELECT user_type FROM users WHERE id = ?";
    if($stmt = mysqli_prepare($conn, $check_sql)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $check_result = mysqli_stmt_get_result($stmt);
        $user_data = mysqli_fetch_assoc($check_result);
        
        if($user_data && $user_data['user_type'] != 'admin') {
            // First delete related records from other tables
            $tables = ['support_replies', 'support_messages', 'purchases'];
            
            foreach($tables as $table) {
                $delete_related = "DELETE FROM $table WHERE user_id = ?";
                if($del_stmt = mysqli_prepare($conn, $delete_related)) {
                    mysqli_stmt_bind_param($del_stmt, "i", $user_id);
                    mysqli_stmt_execute($del_stmt);
                    mysqli_stmt_close($del_stmt);
                }
            }
            
            // Now delete the user
            $delete_user = "DELETE FROM users WHERE id = ?";
            if($del_user_stmt = mysqli_prepare($conn, $delete_user)) {
                mysqli_stmt_bind_param($del_user_stmt, "i", $user_id);
                if(mysqli_stmt_execute($del_user_stmt)) {
                    $success_message = "User deleted successfully.";
                } else {
                    $error_message = "Error deleting user.";
                }
                mysqli_stmt_close($del_user_stmt);
            }
        } else {
            $error_message = "Cannot delete admin users or user doesn't exist.";
        }
        
        mysqli_stmt_close($stmt);
    }
}

// Handle search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch users with pagination
$limit = 10;  // Users per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Count total users for pagination
$count_sql = "SELECT COUNT(*) as total FROM users";
if(!empty($search)) {
    $count_sql .= " WHERE username LIKE ? OR email LIKE ? OR business_name LIKE ?";
}

$total_users = 0;
if($count_stmt = mysqli_prepare($conn, $count_sql)) {
    if(!empty($search)) {
        $search_param = "%$search%";
        mysqli_stmt_bind_param($count_stmt, "sss", $search_param, $search_param, $search_param);
    }
    
    mysqli_stmt_execute($count_stmt);
    $count_result = mysqli_stmt_get_result($count_stmt);
    $count_row = mysqli_fetch_assoc($count_result);
    $total_users = $count_row['total'];
    
    mysqli_stmt_close($count_stmt);
}

$total_pages = ceil($total_users / $limit);

// Fetch users
$sql = "SELECT * FROM users";
if(!empty($search)) {
    $sql .= " WHERE username LIKE ? OR email LIKE ? OR business_name LIKE ?";
}
$sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";

if($stmt = mysqli_prepare($conn, $sql)) {
    if(!empty($search)) {
        $search_param = "%$search%";
        mysqli_stmt_bind_param($stmt, "sssii", $search_param, $search_param, $search_param, $limit, $offset);
    } else {
        mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    while($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    
    mysqli_stmt_close($stmt);
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Dulify Admin</title>
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
        .edit-btn {
            background-color: #4361ee;
        }
        .delete-btn {
            background-color: #e11d48;
        }
        .action-btn:hover {
            opacity: 0.9;
        }
        .search-form {
            display: flex;
            margin-bottom: 20px;
            gap: 10px;
        }
        .search-form input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .search-form button {
            padding: 10px 20px;
            background-color: #4361ee;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
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
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Admin Sidebar -->
        <div class="admin-sidebar">
            <h2>Dulify Admin</h2>
            <ul class="admin-nav">
                <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="users.php" class="active"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="services.php"><i class="fas fa-cogs"></i> Services</a></li>
                <li><a href="purchases.php"><i class="fas fa-shopping-cart"></i> Purchases</a></li>
                   <li><a href="orders.php" class="<?php echo $current_page == 'orders.php' ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-check"></i> Orders
                    <?php if($pending_orders > 0): ?>
                        <span class="badge badge-pill badge-warning"><?php echo $pending_orders; ?></span>
                    <?php endif; ?>
                </a></li>
                <li><a href="support.php"><i class="fas fa-headset"></i> Support Tickets</a></li>

                <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages <?php if(isset($unread_messages) && $unread_messages > 0): ?><span class="unread-badge"><?php echo $unread_messages; ?></span><?php endif; ?></a></li>
                <li><a href="testimonials.php"><i class="fas fa-star"></i> Testimonials</a></li>
                <li><a href="../php/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        
        <!-- Admin Content -->
        <div class="admin-content">
            <div class="admin-card">
                <h2>User Management</h2>
                
                <!-- Success/Error Messages -->
                <?php if(!empty($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if(!empty($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <!-- Search Form -->
                <form class="search-form" method="GET" action="users.php">
                    <input type="text" name="search" placeholder="Search users..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit"><i class="fas fa-search"></i> Search</button>
                </form>
                
                <!-- Users Table -->
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Business</th>
                            <th>Type</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($users)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">No users found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($users as $user): ?>
                                <tr>
                                    <td>#<?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['business_name'] ?: 'N/A'); ?></td>
                                    <td>
                                        <?php if($user['user_type'] == 'admin'): ?>
                                            <span class="badge badge-warning">Admin</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">User</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="action-btn edit-btn"><i class="fas fa-edit"></i></a>
                                        <?php if($user['user_type'] != 'admin'): ?>
                                            <a href="users.php?action=delete&id=<?php echo $user['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this user? This will also delete all related data.')"><i class="fas fa-trash"></i></a>
                                        <?php endif; ?>
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
                        <li><a href="?page=<?php echo $page-1; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?>"><i class="fas fa-chevron-left"></i></a></li>
                    <?php endif; ?>
                    
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li <?php if($i == $page) echo 'class="active"'; ?>>
                            <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if($page < $total_pages): ?>
                        <li><a href="?page=<?php echo $page+1; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?>"><i class="fas fa-chevron-right"></i></a></li>
                    <?php endif; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
