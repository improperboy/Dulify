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

// Create contact_messages table if it doesn't exist
$create_table_sql = "CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    name VARCHAR(100) NOT NULL, 
    email VARCHAR(100) NOT NULL, 
    message TEXT NOT NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read BOOLEAN DEFAULT 0
)";

if (!mysqli_query($conn, $create_table_sql)) {
    die("Error creating table: " . mysqli_error($conn));
}

// Count unread contact messages
$unread_messages = 0;
$sql = "SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0";
if($result = mysqli_query($conn, $sql)) {
    $row = mysqli_fetch_assoc($result);
    $unread_messages = $row["count"];
}

// Process message actions (mark as read/unread, delete)
if(isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    if($_GET['action'] == 'read') {
        $sql = "UPDATE contact_messages SET is_read = 1 WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    } elseif($_GET['action'] == 'unread') {
        $sql = "UPDATE contact_messages SET is_read = 0 WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    } elseif($_GET['action'] == 'delete') {
        $sql = "DELETE FROM contact_messages WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
    
    // Redirect to avoid resubmission
    header("location: messages.php");
    exit;
}

// Apply filters if set
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Get messages from the database with filtering
$messages = [];
$sql = "SELECT * FROM contact_messages";

if($filter == 'unread') {
    $sql .= " WHERE is_read = 0";
} else if($filter == 'read') {
    $sql .= " WHERE is_read = 1";
}

$sql .= " ORDER BY created_at DESC";

if($result = mysqli_query($conn, $sql)) {
    while($row = mysqli_fetch_assoc($result)) {
        $messages[] = $row;
    }
}

// Count unread messages
$unread_count = 0;
$sql = "SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0";
if($result = mysqli_query($conn, $sql)) {
    $row = mysqli_fetch_assoc($result);
    $unread_count = $row["count"];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Dulify Admin</title>
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
        .edit-btn {
            background-color: #047857;
        }
        .delete-btn {
            background-color: #b91c1c;
        }
        .unread-badge {
            background-color: #FFC107;
            color: #fff;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75em;
            margin-left: 5px;
        }
        .message-content {
            white-space: pre-line;
            padding: 10px 0;
        }
        .filter-options {
            margin-bottom: 20px;
        }
        .filter-options a {
            display: inline-block;
            margin-right: 10px;
            padding: 8px 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
            color: #333;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .filter-options a:hover, .filter-options a.active {
            background-color: #4361ee;
            color: white;
        }
        .greeting {
            margin-bottom: 20px;
        }
        .greeting h1 {
            font-size: 1.8rem;
            color: #1a1a2e;
            margin-bottom: 5px;
        }
        .greeting p {
            color: #666;
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
                <li><a href="support.php"><i class="fas fa-headset"></i> Support Tickets</a></li>
                <li><a href="messages.php" class="active"><i class="fas fa-envelope"></i> Messages <?php if($unread_messages > 0): ?><span class="unread-badge"><?php echo $unread_messages; ?></span><?php endif; ?></a></li>
                <li><a href="testimonials.php"><i class="fas fa-star"></i> Testimonials</a></li>
                <li><a href="../php/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        
        <!-- Admin Content -->
        <div class="admin-content">
            <div class="greeting">
                <h1>Contact Messages</h1>
                <p>Manage messages from website visitors.</p>
            </div>
            
            <?php if(isset($_SESSION["reply_success"])): ?>
                <div style="background-color: #d1fae5; border: 1px solid #047857; color: #047857; padding: 10px 15px; border-radius: 4px; margin-bottom: 20px;">
                    <?php echo $_SESSION["reply_success"]; ?>
                    <?php unset($_SESSION["reply_success"]); ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION["reply_error"])): ?>
                <div style="background-color: #fee2e2; border: 1px solid #b91c1c; color: #b91c1c; padding: 10px 15px; border-radius: 4px; margin-bottom: 20px;">
                    <?php echo $_SESSION["reply_error"]; ?>
                    <?php unset($_SESSION["reply_error"]); ?>
                </div>
            <?php endif; ?>
            
            <div class="admin-card">
                <h2>Contact Messages</h2>
                
                <div class="filter-options">
                    <a href="messages.php" class="<?php echo !isset($_GET['filter']) || $_GET['filter'] == 'all' ? 'active' : ''; ?>">All Messages</a>
                    <a href="messages.php?filter=unread" class="<?php echo isset($_GET['filter']) && $_GET['filter'] == 'unread' ? 'active' : ''; ?>">Unread</a>
                    <a href="messages.php?filter=read" class="<?php echo isset($_GET['filter']) && $_GET['filter'] == 'read' ? 'active' : ''; ?>">Read</a>
                </div>
                
                <?php if(count($messages) > 0): ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($messages as $message): ?>
                                <tr>
                                    <td>
                                        <?php if($message['is_read']): ?>
                                            <span class="badge badge-success">Read</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Unread</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($message['name']); ?></td>
                                    <td><?php echo htmlspecialchars($message['email']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($message['message'], 0, 50)) . (strlen($message['message']) > 50 ? '...' : ''); ?></td>
                                    <td><?php echo date('M j, Y, g:i a', strtotime($message['created_at'])); ?></td>
                                    <td>
                                        <a href="#" class="action-btn view-btn" onclick="viewMessage(<?php echo $message['id']; ?>)"><i class="fas fa-eye"></i></a>
                                        <?php if($message['is_read']): ?>
                                            <a href="messages.php?action=unread&id=<?php echo $message['id']; ?>" class="action-btn edit-btn" title="Mark as Unread"><i class="fas fa-envelope"></i></a>
                                        <?php else: ?>
                                            <a href="messages.php?action=read&id=<?php echo $message['id']; ?>" class="action-btn edit-btn" title="Mark as Read"><i class="fas fa-envelope-open"></i></a>
                                        <?php endif; ?>
                                        <a href="messages.php?action=delete&id=<?php echo $message['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this message?');" title="Delete"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px;">
                        <i class="fas fa-inbox fa-3x" style="color: #ddd; margin-bottom: 15px;"></i>
                        <h3>No Messages Found</h3>
                        <p>When visitors send messages through the contact form, they will appear here.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Message Detail Modal -->
            <div id="messageModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
                <div style="background-color: #fefefe; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px; border-radius: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h2 id="modalTitle">Message Details</h2>
                        <span style="cursor: pointer; font-size: 28px;" onclick="closeModal()">&times;</span>
                    </div>
                    <div id="modalContent"></div>
                    <div id="replyForm" style="display: none; margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px;">
                        <h3>Reply to this message</h3>
                        <form id="emailReplyForm" method="post" action="send_reply.php">
                            <input type="hidden" id="reply_to_email" name="reply_to_email">
                            <input type="hidden" id="reply_to_name" name="reply_to_name">
                            <input type="hidden" id="message_id" name="message_id">
                            
                            <div style="margin-bottom: 15px;">
                                <label for="reply_subject" style="display: block; margin-bottom: 5px;">Subject:</label>
                                <input type="text" id="reply_subject" name="reply_subject" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
                            </div>
                            
                            <div style="margin-bottom: 15px;">
                                <label for="reply_message" style="display: block; margin-bottom: 5px;">Message:</label>
                                <textarea id="reply_message" name="reply_message" rows="6" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required></textarea>
                            </div>
                            
                            <div style="text-align: right;">
                                <button type="button" onclick="toggleReplyForm()" style="padding: 8px 16px; background-color: #f3f4f6; border: none; border-radius: 4px; margin-right: 10px; cursor: pointer;">Cancel</button>
                                <button type="submit" style="padding: 8px 16px; background-color: #4361ee; color: white; border: none; border-radius: 4px; cursor: pointer;">Send Reply</button>
                            </div>
                        </form>
                    </div>
                    <div style="margin-top: 20px; text-align: right;">
                        <button id="replyButton" onclick="toggleReplyForm()" style="padding: 8px 16px; background-color: #4361ee; color: white; border: none; border-radius: 4px; margin-right: 10px; cursor: pointer;">Reply</button>
                        <button onclick="closeModal()" style="padding: 8px 16px; background-color: #f3f4f6; border: none; border-radius: 4px; cursor: pointer;">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Function to display message details in modal
        function viewMessage(messageId) {
            // Loop through messages array to find the message
            <?php echo "const messages = " . json_encode($messages) . ";\n"; ?>
            const message = messages.find(msg => msg.id == messageId);
            
            if (message) {
                document.getElementById('modalTitle').innerText = 'Message from ' + message.name;
                
                let content = `
                    <div style="margin-bottom: 15px;">
                        <p><strong>From:</strong> ${message.name} &lt;${message.email}&gt;</p>
                        <p><strong>Date:</strong> ${new Date(message.created_at).toLocaleString()}</p>
                    </div>
                    <div style="border-top: 1px solid #eee; padding-top: 15px;">
                        <p style="white-space: pre-line;">${message.message}</p>
                    </div>
                `;
                
                document.getElementById('modalContent').innerHTML = content;
                document.getElementById('messageModal').style.display = 'block';
                
                // Set hidden form fields for reply
                document.getElementById('reply_to_email').value = message.email;
                document.getElementById('reply_to_name').value = message.name;
                document.getElementById('message_id').value = message.id;
                document.getElementById('reply_subject').value = 'RE: Contact Form Inquiry';
                
                // If message was unread, mark as read
                if (!message.is_read) {
                    window.location.href = `messages.php?action=read&id=${messageId}&return=modal`;
                }
            }
        }
        
        function toggleReplyForm() {
            const replyForm = document.getElementById('replyForm');
            const replyButton = document.getElementById('replyButton');
            
            if (replyForm.style.display === 'none') {
                replyForm.style.display = 'block';
                replyButton.innerText = 'Cancel Reply';
            } else {
                replyForm.style.display = 'none';
                replyButton.innerText = 'Reply';
            }
        }
        
        function closeModal() {
            document.getElementById('messageModal').style.display = 'none';
            // Reset reply form
            document.getElementById('replyForm').style.display = 'none';
            document.getElementById('replyButton').innerText = 'Reply';
        }
        
        // Close the modal if user clicks outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('messageModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
