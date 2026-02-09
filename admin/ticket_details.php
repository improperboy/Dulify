<?php
// Include database configuration
require_once "../includes/config.php";

// Check if user is logged in and is an admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_type"] !== "admin") {
    header("location: ../php/login.php");
    exit;
}

// Check if ticket ID is provided
if(!isset($_GET["id"]) || empty($_GET["id"])) {
    header("location: support.php");
    exit;
}

$ticket_id = intval($_GET["id"]);
$success_message = $error_message = "";

// Fetch ticket details
$ticket = [];
$sql = "SELECT sm.*, u.username, u.email 
        FROM support_messages sm 
        JOIN users u ON sm.user_id = u.id 
        WHERE sm.id = ?";

if($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $ticket_id);
    if(mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if($ticket = mysqli_fetch_assoc($result)) {
            // Ticket found
        } else {
            // Ticket not found
            header("location: support.php");
            exit;
        }
    }
    mysqli_stmt_close($stmt);
} else {
    $error_message = "Database error. Please try again later.";
}

// Fetch ticket replies
$replies = [];
$sql = "SELECT sr.*, u.username, u.user_type 
        FROM support_replies sr 
        JOIN users u ON sr.user_id = u.id 
        WHERE sr.message_id = ? 
        ORDER BY sr.created_at ASC";

if($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $ticket_id);
    if(mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while($row = mysqli_fetch_assoc($result)) {
            $replies[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}

// Handle reply submission
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reply"])) {
    $reply_content = trim($_POST["reply"]);
    
    if(empty($reply_content)) {
        $error_message = "Reply cannot be empty.";
    } else {
        // Insert reply
        $sql = "INSERT INTO support_replies (message_id, user_id, content) VALUES (?, ?, ?)";
        
        if($stmt = mysqli_prepare($conn, $sql)) {
            $admin_id = $_SESSION["id"];
            mysqli_stmt_bind_param($stmt, "iis", $ticket_id, $admin_id, $reply_content);
            
            if(mysqli_stmt_execute($stmt)) {
                // Update ticket status if needed
                if(isset($_POST["update_status"]) && $_POST["update_status"] != $ticket["status"]) {
                    $new_status = $_POST["update_status"];
                    
                    if(in_array($new_status, ['open', 'in_progress', 'closed'])) {
                        $update_sql = "UPDATE support_messages SET status = ? WHERE id = ?";
                        
                        if($stmt_update = mysqli_prepare($conn, $update_sql)) {
                            mysqli_stmt_bind_param($stmt_update, "si", $new_status, $ticket_id);
                            mysqli_stmt_execute($stmt_update);
                            mysqli_stmt_close($stmt_update);
                            
                            // Update status in the current ticket array
                            $ticket["status"] = $new_status;
                        }
                    }
                }
                
                // Add the new reply to the replies array
                $new_reply = [
                    "id" => mysqli_insert_id($conn),
                    "message_id" => $ticket_id,
                    "user_id" => $admin_id,
                    "content" => $reply_content,
                    "created_at" => date("Y-m-d H:i:s"),
                    "username" => $_SESSION["username"],
                    "user_type" => "admin"
                ];
                $replies[] = $new_reply;
                
                $success_message = "Your reply has been submitted.";
            } else {
                $error_message = "Something went wrong. Please try again later.";
            }
            
            mysqli_stmt_close($stmt);
        }
    }
}

// Get status badge class
function getStatusBadgeClass($status) {
    switch($status) {
        case 'open':
            return 'badge-warning';
        case 'in_progress':
            return 'badge-info';
        case 'closed':
            return 'badge-success';
        default:
            return 'badge-secondary';
    }
}

// Format date for display
function formatDate($date) {
    return date('M d, Y h:i A', strtotime($date));
}

// Count unread contact messages
$unread_messages = 0;

// Check if the contact_messages table exists
$table_exists = false;
$check_table_sql = "SHOW TABLES LIKE 'contact_messages'";
$check_result = mysqli_query($conn, $check_table_sql);

if($check_result && mysqli_num_rows($check_result) > 0) {
    $table_exists = true;
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Ticket #<?php echo $ticket_id; ?> - Dulify Admin</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .badge-danger {
            background-color: #fee2e2;
            color: #b91c1c;
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
        /* Ticket specific styles */
        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .ticket-meta {
            margin-top: 10px;
            color: #6b7280;
            font-size: 0.9rem;
        }
        .ticket-meta span {
            margin-right: 15px;
        }
        .ticket-meta i {
            margin-right: 5px;
            width: 16px;
            text-align: center;
        }
        .ticket-content {
            margin-bottom: 30px;
            background-color: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #4361ee;
        }
        .reply {
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 8px;
        }
        .user-reply {
            background-color: #f9fafb;
            border-left: 4px solid #4361ee;
        }
        .admin-reply {
            background-color: #f0f9ff;
            border-left: 4px solid #0ea5e9;
            margin-left: 20px;
        }
        .reply-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.9rem;
            color: #4b5563;
        }
        .reply-author {
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        .reply-author i {
            margin-right: 5px;
        }
        .reply-content {
            font-size: 0.95rem;
            line-height: 1.5;
            word-break: break-word;
        }
        .reply-form {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .form-control:focus {
            border-color: #4361ee;
            outline: none;
        }
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        .status-group {
            display: flex;
            align-items: center;
            margin-top: 15px;
        }
        .status-group label {
            margin-right: 15px;
            margin-bottom: 0;
        }
        .btn-primary {
            background-color: #4361ee;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #3651d4;
        }
        .btn-secondary {
            background-color: #e5e7eb;
            color: #4b5563;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-secondary:hover {
            background-color: #d1d5db;
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
                <li><a href="purchases.php"><i class="fas fa-shopping-cart"></i> Purchases</a></li>
                   <li><a href="orders.php" class="<?php echo $current_page == 'orders.php' ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-check"></i> Orders
                    <?php if($pending_orders > 0): ?>
                        <span class="badge badge-pill badge-warning"><?php echo $pending_orders; ?></span>
                    <?php endif; ?>
                </a></li>
                <li><a href="support.php" class="active"><i class="fas fa-headset"></i> Support Tickets</a></li>
                <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages <?php if($unread_messages > 0): ?><span class="unread-badge"><?php echo $unread_messages; ?></span><?php endif; ?></a></li>
                <li><a href="testimonials.php"><i class="fas fa-star"></i> Testimonials</a></li>
                <li><a href="../php/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        
        <!-- Admin Content -->
        <div class="admin-content">
            <div class="admin-card">
                <div class="ticket-header">
                    <div>
                        <h2>
                            Ticket #<?php echo $ticket_id; ?>: <?php echo htmlspecialchars($ticket['subject']); ?>
                            <span class="badge <?php echo getStatusBadgeClass($ticket['status']); ?>">
                                <?php echo ucfirst($ticket['status']); ?>
                            </span>
                        </h2>
                        <div class="ticket-meta">
                            <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($ticket['username']); ?></span>
                            <span><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($ticket['email']); ?></span>
                            <span><i class="fas fa-calendar"></i> <?php echo formatDate($ticket['created_at']); ?></span>
                        </div>
                    </div>
                    <a href="support.php" class="btn-secondary"><i class="fas fa-arrow-left"></i> Back to Tickets</a>
                </div>
                
                <!-- Success/Error Messages -->
                <?php if(!empty($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if(!empty($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <!-- Ticket Content -->
                <div class="ticket-content">
                    <p><?php echo nl2br(htmlspecialchars($ticket['message'])); ?></p>
                </div>
                
                <!-- Ticket Replies -->
                <h3>Replies</h3>
                <?php if(empty($replies)): ?>
                    <p>No replies yet.</p>
                <?php else: ?>
                    <?php foreach($replies as $reply): ?>
                        <div class="reply <?php echo $reply['user_type'] === 'admin' ? 'admin-reply' : 'user-reply'; ?>">
                            <div class="reply-header">
                                <div class="reply-author">
                                    <?php if($reply['user_type'] === 'admin'): ?>
                                        <i class="fas fa-user-shield"></i>
                                    <?php else: ?>
                                        <i class="fas fa-user"></i>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($reply['username']); ?>
                                    <?php if($reply['user_type'] === 'admin'): ?>
                                        <span style="margin-left: 5px; color: #4361ee;">(Admin)</span>
                                    <?php endif; ?>
                                </div>
                                <div><?php echo formatDate($reply['created_at']); ?></div>
                            </div>
                            <div class="reply-content">
                                <?php echo nl2br(htmlspecialchars($reply['content'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <!-- Reply Form -->
                <div class="reply-form">
                    <h3>Add Reply</h3>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $ticket_id; ?>" method="post">
                        <div class="form-group">
                            <label for="reply">Your Reply</label>
                            <textarea id="reply" name="reply" class="form-control" required></textarea>
                        </div>
                        
                        <div class="status-group">
                            <label for="update_status">Update Status:</label>
                            <select id="update_status" name="update_status" class="form-control" style="width: auto;">
                                <option value="<?php echo $ticket['status']; ?>">Keep Current (<?php echo ucfirst($ticket['status']); ?>)</option>
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn-primary" style="margin-top: 15px;">Submit Reply</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
