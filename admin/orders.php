<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration
require_once "../includes/config.php";

// Check if user is logged in and is an admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_type"] !== "admin") {
    header("location: ../php/login.php");
    exit;
}

// Set page title
$page_title = "Order Management";

// Include header
include "includes/header.php";

// Custom CSS for orders page
?>
<style>
    /* Status tabs styling - Improved alignment */
    .nav-pills.card-header-pills {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 5px;
    }
    
    .nav-pills .nav-item {
        flex: 0 0 auto;
        white-space: nowrap;
    }
    
    .nav-pills .nav-link {
        transition: all 0.3s ease;
        border-radius: 20px;
        padding: 0.5rem 1rem;
        margin: 0 0.25rem;
        color: #555;
        display: inline-flex;
        align-items: center;
    }
    
    .nav-pills .nav-link:hover:not(.active) {
        background-color: #f8f9fc;
    }
    
    .nav-pills .nav-link.active {
        background-color: #4e73df;
        color: white;
        box-shadow: 0 4px 6px rgba(78, 115, 223, 0.25);
    }
    
    /* Status indicators */
    .status-indicator {
        width: 0.75rem;
        height: 0.75rem;
        border-radius: 50%;
        margin-right: 0.5rem;
        flex-shrink: 0;
    }
    
    .status-indicator.pending {
        background-color: #f6c23e;
    }
    
    .status-indicator.approved {
        background-color: #1cc88a;
    }
    
    .status-indicator.rejected {
        background-color: #e74a3b;
    }
    
    /* Status counters */
    .status-counter {
        margin-left: 0.5rem;
        font-size: 0.75rem;
        min-width: 1.5rem;
        height: 1.5rem;
        line-height: 1.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Keep existing styles for other components */
    .rounded-circle {
        border-radius: 50%;
    }
    
    .badge-pending {
        background-color: #f6c23e;
        color: #fff;
    }
    
    .badge-approved {
        background-color: #1cc88a;
        color: #fff;
    }
    
    .badge-rejected {
        background-color: #e74a3b;
        color: #fff;
    }
    
    .btn-success, .btn-danger, .btn-info {
        transition: transform 0.2s;
    }
    
    .btn-success:hover, .btn-danger:hover, .btn-info:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.9rem;
        }
        
        .btn-sm {
            padding: 0.25rem 0.4rem;
            font-size: 0.75rem;
        }
        
        .nav-pills .nav-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }
    }
</style>
<style>
    /* Tab styling */
    .nav-pills .nav-link {
        transition: all 0.3s ease;
        border-radius: 20px;
        padding: 0.5rem 1rem;
        margin: 0 0.25rem;
        color: #555;
    }
    .nav-pills .nav-link:hover:not(.active) {
        background-color: #f8f9fc;
    }
    .nav-pills .nav-link.active {
        background-color: #4e73df;
        color: white;
        box-shadow: 0 4px 6px rgba(78, 115, 223, 0.25);
    }
    
    /* Circle indicators */
    .w-3 {
        width: 0.75rem;
    }
    .h-3 {
        height: 0.75rem;
    }
    .rounded-circle {
        border-radius: 50%;
    }
    .d-inline-block {
        display: inline-block;
        vertical-align: middle;
    }
    
    /* Status badges */
    .badge-pending {
        background-color: #f6c23e;
        color: #fff;
    }
    .badge-approved {
        background-color: #1cc88a;
        color: #fff;
    }
    .badge-rejected {
        background-color: #e74a3b;
        color: #fff;
    }
    
    /* Action buttons hover effects */
    .btn-success, .btn-danger, .btn-info {
        transition: transform 0.2s;
    }
    .btn-success:hover, .btn-danger:hover, .btn-info:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.9rem;
        }
        .btn-sm {
            padding: 0.25rem 0.4rem;
            font-size: 0.75rem;
        }
    }
</style>
<?php

// Process order approval/rejection
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["approve_order"]) && isset($_POST["order_id"])) {
        $order_id = $_POST["order_id"];
        
        // Get order details
        $sql = "SELECT * FROM orders WHERE id = ?";
        $order = null;
        
        if($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $order_id);
            if(mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                if(mysqli_num_rows($result) == 1) {
                    $order = mysqli_fetch_assoc($result);
                }
            }
            mysqli_stmt_close($stmt);
        }
        
        if ($order) {
            // Start transaction
            mysqli_begin_transaction($conn);
            
            try {
                // Update order status to approved
                $update_sql = "UPDATE orders SET status = 'approved' WHERE id = ?";
                $update_stmt = mysqli_prepare($conn, $update_sql);
                mysqli_stmt_bind_param($update_stmt, "i", $order_id);
                mysqli_stmt_execute($update_stmt);
                mysqli_stmt_close($update_stmt);
                
                // Calculate expiry date (1 year from now)
                $expiry_date = date('Y-m-d H:i:s', strtotime('+1 year'));
                
                // Create purchase record
                $purchase_sql = "INSERT INTO purchases (user_id, service_id, expiry_date, status) VALUES (?, ?, ?, 'active')";
                $purchase_stmt = mysqli_prepare($conn, $purchase_sql);
                mysqli_stmt_bind_param($purchase_stmt, "iis", $order["user_id"], $order["service_id"], $expiry_date);
                
                if(!mysqli_stmt_execute($purchase_stmt)) {
                    throw new Exception("Failed to create purchase record: " . mysqli_stmt_error($purchase_stmt));
                }
                mysqli_stmt_close($purchase_stmt);
                
                // Commit transaction
                mysqli_commit($conn);
                
                $success_message = "Order #" . $order_id . " has been approved successfully.";
            } catch (Exception $e) {
                // Rollback transaction on error
                mysqli_rollback($conn);
                $error_message = "Error approving order: " . $e->getMessage();
            }
        }
    } elseif (isset($_POST["reject_order"]) && isset($_POST["order_id"])) {
        $order_id = $_POST["order_id"];
        $admin_notes = isset($_POST["admin_notes"]) ? trim($_POST["admin_notes"]) : "";
        
        // Update order status to rejected
        $sql = "UPDATE orders SET status = 'rejected', admin_notes = ? WHERE id = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $admin_notes, $order_id);
            
            if(mysqli_stmt_execute($stmt)) {
                $success_message = "Order #" . $order_id . " has been rejected.";
            } else {
                $error_message = "Error rejecting order.";
            }
            
            mysqli_stmt_close($stmt);
        }
    }
}

// Get active tab
$active_tab = isset($_GET["tab"]) ? $_GET["tab"] : "pending";

// Get counts for each order status
$pending_count = $approved_count = $rejected_count = 0;

// Get count of pending orders
$count_sql = "SELECT COUNT(*) as count FROM orders WHERE status = 'pending'";
$count_result = $conn->query($count_sql);
if ($count_result && $row = $count_result->fetch_assoc()) {
    $pending_count = $row['count'];
}

// Get count of approved orders
$count_sql = "SELECT COUNT(*) as count FROM orders WHERE status = 'approved'";
$count_result = $conn->query($count_sql);
if ($count_result && $row = $count_result->fetch_assoc()) {
    $approved_count = $row['count'];
}

// Get count of rejected orders
$count_sql = "SELECT COUNT(*) as count FROM orders WHERE status = 'rejected'";
$count_result = $conn->query($count_sql);
if ($count_result && $row = $count_result->fetch_assoc()) {
    $rejected_count = $row['count'];
}

// Fetch orders based on active tab
$orders = array();
$sql = "";

if ($active_tab == "pending") {
    $sql = "SELECT o.*, u.username, s.name as service_name, s.price 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            JOIN services s ON o.service_id = s.id 
            WHERE o.status = 'pending' 
            ORDER BY o.order_date DESC";
} elseif ($active_tab == "approved") {
    $sql = "SELECT o.*, u.username, s.name as service_name, s.price 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            JOIN services s ON o.service_id = s.id 
            WHERE o.status = 'approved' 
            ORDER BY o.order_date DESC";
} elseif ($active_tab == "rejected") {
    $sql = "SELECT o.*, u.username, s.name as service_name, s.price 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            JOIN services s ON o.service_id = s.id 
            WHERE o.status = 'rejected' 
            ORDER BY o.order_date DESC";
}

if($stmt = mysqli_prepare($conn, $sql)) {
    if(mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Manage Orders</h1>
    
    <?php if(isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <?php if(isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
        <ul class="nav nav-pills card-header-pills">
    <li class="nav-item mx-1">
        <a class="nav-link <?php echo $active_tab == 'pending' ? 'active' : 'bg-light'; ?>" href="?tab=pending">
            <span class="status-indicator pending"></span>
            <i class="fas fa-clock mr-1"></i> Pending
            <?php if($pending_count > 0): ?>
                <span class="status-counter badge-pending"><?php echo $pending_count; ?></span>
            <?php endif; ?>
        </a>
    </li>
    <li class="nav-item mx-1">
        <a class="nav-link <?php echo $active_tab == 'approved' ? 'active' : 'bg-light'; ?>" href="?tab=approved">
            <span class="status-indicator approved"></span>
            <i class="fas fa-check-circle mr-1"></i> Approved
            <?php if($approved_count > 0): ?>
                <span class="status-counter badge-approved"><?php echo $approved_count; ?></span>
            <?php endif; ?>
        </a>
    </li>
    <li class="nav-item mx-1">
        <a class="nav-link <?php echo $active_tab == 'rejected' ? 'active' : 'bg-light'; ?>" href="?tab=rejected">
            <span class="status-indicator rejected"></span>
            <i  class="fas fa-times-circle mr-1"></i> Rejected
            <?php if($rejected_count > 0): ?>
                <span class="status-counter badge-rejected"><?php echo $rejected_count; ?></span>
            <?php endif; ?>
        </a>
    </li>
</ul>
        </div>
        <div class="card-body">
        <?php
        // Check if orders table exists
        $table_exists = $conn->query("SHOW TABLES LIKE 'orders'");
        if($table_exists->num_rows == 0) {
            // Table doesn't exist, show setup message
            ?>
            <div class="admin-card mb-4 text-center">
                <div class="py-5">
                    <i class="fas fa-exclamation-triangle text-warning fa-4x mb-3"></i>
                    <h3>Orders Table Not Found</h3>
                    <p class="text-muted">The orders table hasn't been created in your database yet.</p>
                    <a href="../create_orders_table_now.php" class="btn btn-primary mt-3">
                        <i class="fas fa-database mr-2"></i>Create Orders Table Now
                    </a>
                </div>
            </div>
            <?php
        } else {
            // Table exists, show orders content
            ?>
            <div class="tab-content" id="orders-tab-content">
                <div class="tab-pane fade show active" id="<?php echo $active_tab; ?>-orders" role="tabpanel">
                    <?php if(empty($orders)): ?>
                        <div class="text-center p-4">
                            <p class="text-muted">No <?php echo $active_tab; ?> orders found.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Service</th>
                                        <th>Price</th>
                                        <th>Order Date</th>
                                        <th>Details</th>
                                        <?php if($active_tab == 'pending'): ?>
                                            <th>Actions</th>
                                        <?php elseif($active_tab == 'rejected'): ?>
                                            <th>Reason</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($orders as $order): ?>
                                        <tr>
                                            <td><?php echo $order['id']; ?></td>
                                            <td><?php echo htmlspecialchars($order['username']); ?></td>
                                            <td><?php echo htmlspecialchars($order['service_name']); ?></td>
                                            <td>$<?php echo number_format($order['price'], 2); ?></td>
                                            <td><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#orderDetailsModal<?php echo $order['id']; ?>">
                                                    <i class="fas fa-eye"></i> View Details
                                                </button>
                                            </td>
                                            <?php if($active_tab == 'pending'): ?>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <form method="post" style="display: inline;">
                                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                            <button type="submit" name="approve_order" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to approve this order?');">
                                                                <i class="fas fa-check"></i> Approve
                                                            </button>
                                                        </form>
                                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal<?php echo $order['id']; ?>">
                                                            <i class="fas fa-times"></i> Reject
                                                        </button>
                                                    </div>
                                                </td>
                                            <?php elseif($active_tab == 'rejected'): ?>
                                                <td><?php echo !empty($order['admin_notes']) ? htmlspecialchars($order['admin_notes']) : 'No reason provided'; ?></td>
                                            <?php endif; ?>
                                        </tr>
                                        
                                        <!-- Order Details Modal -->
                                        <div class="modal fade" id="orderDetailsModal<?php echo $order['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="orderDetailsModalLabel">Order #<?php echo $order['id']; ?> Details</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                             <div class="col-md-6">
                                                                <p><strong>User ID:</strong> <?php echo htmlspecialchars($order['user_id']); ?></p>
                                                                <p><strong>Username:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
                                                                <?php if(isset($order['phone'])): ?>
                                                                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p><strong>Order Date:</strong> <?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></p>
                                                                <p><strong>Status:</strong> 
                                                                    <span class="badge badge-<?php echo ($order['status'] == 'pending') ? 'warning' : (($order['status'] == 'approved') ? 'success' : 'danger'); ?>">
                                                                        <?php echo ucfirst($order['status']); ?>
                                                                    </span>
                                                                </p>
                                                                <p><strong>Service:</strong> <?php echo htmlspecialchars($order['service_name']); ?></p>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-3">
                                                            <div class="col-12">
                                                                <?php if(isset($order['address'])): ?>
                                                                    <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                                                                <?php endif; ?>
                                                                <?php if(isset($order['additional_notes'])): ?>
                                                                    <p><strong>Additional Notes:</strong> <?php echo htmlspecialchars($order['additional_notes']); ?></p>
                                                                <?php endif; ?>
                                                                <?php if($order['status'] == 'rejected' && !empty($order['admin_notes'])): ?>
                                                                    <p><strong>Rejection Reason:</strong> <?php echo htmlspecialchars($order['admin_notes']); ?></p>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Reject Order Modal -->
                                        <?php if($active_tab == 'pending'): ?>
                                            <div class="modal fade" id="rejectModal<?php echo $order['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="rejectModalLabel">Reject Order #<?php echo $order['id']; ?></h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form method="post">
                                                            <div class="modal-body">
                                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                                <div class="form-group">
                                                                    <label for="admin_notes">Reason for Rejection (Optional):</label>
                                                                    <textarea class="form-control" name="admin_notes" id="admin_notes" rows="3" placeholder="Provide a reason for rejection (will be visible to the customer)"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                                <button type="submit" name="reject_order" class="btn btn-danger">Reject Order</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php } ?>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>