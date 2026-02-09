<?php
// Set page title
$page_title = "Testimonials Management";

// Include database configuration
require_once "../includes/config.php";

// Create testimonials table if it doesn't exist
$create_table_sql = "CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (service_id) REFERENCES services(id)
)";
mysqli_query($conn, $create_table_sql);

// Process testimonial actions
if(isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $testimonial_id = $_GET['id'];
    
    if($action === 'toggle') {
        // Toggle testimonial status (active/inactive)
        $toggle_sql = "UPDATE testimonials SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $toggle_sql)) {
            mysqli_stmt_bind_param($stmt, "i", $testimonial_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            header("Location: testimonials.php");
            exit;
        }
    } elseif($action === 'delete') {
        // Delete testimonial
        $delete_sql = "DELETE FROM testimonials WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $delete_sql)) {
            mysqli_stmt_bind_param($stmt, "i", $testimonial_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            header("Location: testimonials.php");
            exit;
        }
    }
}

// Get filter parameter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Fetch testimonials with user and service information
$query = "SELECT t.*, u.username, s.name as service_name 
          FROM testimonials t 
          JOIN users u ON t.user_id = u.id 
          JOIN services s ON t.service_id = s.id";

// Apply filter
if($filter === 'active') {
    $query .= " WHERE t.status = 'active'";
} elseif($filter === 'inactive') {
    $query .= " WHERE t.status = 'inactive'";
}

$query .= " ORDER BY t.created_at DESC";
$result = mysqli_query($conn, $query);

$testimonials = [];
if($result) {
    while($row = mysqli_fetch_assoc($result)) {
        $testimonials[] = $row;
    }
}

// Custom CSS for this page
$additional_css = <<<CSS
.star-rating i {
    color: #FFD700;
    margin-right: 2px;
}

.star-empty {
    color: #ccc !important;
}

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
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.close {
    position: absolute;
    right: 20px;
    top: 15px;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.modal-header {
    border-bottom: 1px solid #e0e0e0;
    padding-bottom: 15px;
    margin-bottom: 15px;
}

.modal-title {
    margin: 0;
    font-size: 1.5rem;
}

.modal-body {
    margin-bottom: 20px;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    padding-top: 15px;
    border-top: 1px solid #e0e0e0;
}

.truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 200px;
    display: block;
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

@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        margin: 5% auto;
    }
    
    .filter-tabs {
        justify-content: center;
        gap: 5px;
    }
    
    .filter-tab {
        padding: 6px 12px;
        font-size: 0.9rem;
    }
    
    .admin-table {
        display: block;
        overflow-x: auto;
    }
    
    .truncate {
        max-width: 100px;
    }
}
CSS;

// Include the header (with responsive sidebar)
include "includes/header.php";
?>

<div class="content-wrapper">
    <div class="content-header">
        <h1><?php echo $page_title; ?></h1>
        <p>View and manage client testimonials for your services.</p>
    </div>

    <div class="content-body">
        <div class="card">
            <div class="filter-tabs">
                <a href="testimonials.php" class="filter-tab <?php echo $filter === 'all' ? 'active' : ''; ?>">All Testimonials</a>
                <a href="testimonials.php?filter=active" class="filter-tab <?php echo $filter === 'active' ? 'active' : ''; ?>">Active</a>
                <a href="testimonials.php?filter=inactive" class="filter-tab <?php echo $filter === 'inactive' ? 'active' : ''; ?>">Inactive</a>
            </div>
            
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Service</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($testimonials)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 20px;">No testimonials found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($testimonials as $testimonial): ?>
                                <tr>
                                    <td><?php echo $testimonial['id']; ?></td>
                                    <td><?php echo htmlspecialchars($testimonial['username']); ?></td>
                                    <td><?php echo htmlspecialchars($testimonial['service_name']); ?></td>
                                    <td class="rating">
                                        <?php
                                        for($i = 1; $i <= 5; $i++) {
                                            if($i <= $testimonial['rating']) {
                                                echo '<i class="fas fa-star"></i>';
                                            } elseif($i - 0.5 == $testimonial['rating']) {
                                                echo '<i class="fas fa-star-half-alt"></i>';
                                            } else {
                                                echo '<i class="far fa-star"></i>';
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td class="truncate"><?php echo htmlspecialchars($testimonial['comment']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($testimonial['created_at'])); ?></td>
                                    <td>
                                        <?php if($testimonial['status'] === 'active'): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="action-btn btn-view view-testimonial" 
                                           data-id="<?php echo $testimonial['id']; ?>"
                                           data-username="<?php echo htmlspecialchars($testimonial['username']); ?>"
                                           data-service="<?php echo htmlspecialchars($testimonial['service_name']); ?>"
                                           data-rating="<?php echo $testimonial['rating']; ?>"
                                           data-comment="<?php echo htmlspecialchars($testimonial['comment']); ?>"
                                           data-date="<?php echo date('M d, Y', strtotime($testimonial['created_at'])); ?>"
                                           data-status="<?php echo $testimonial['status']; ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="testimonials.php?action=toggle&id=<?php echo $testimonial['id']; ?>" class="action-btn btn-edit toggle-status">
                                            <?php if($testimonial['status'] === 'active'): ?>
                                                <i class="fas fa-toggle-off"></i>
                                            <?php else: ?>
                                                <i class="fas fa-toggle-on"></i>
                                            <?php endif; ?>
                                        </a>
                                        <a href="testimonials.php?action=delete&id=<?php echo $testimonial['id']; ?>" class="action-btn btn-delete delete-testimonial">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Testimonial View Modal -->
<div id="testimonial-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="modal-header">
            <h2 class="modal-title">Testimonial Details</h2>
        </div>
        <div class="modal-body">
            <p><strong>User:</strong> <span id="modal-username"></span></p>
            <p><strong>Service:</strong> <span id="modal-service"></span></p>
            <p><strong>Rating:</strong> <span id="modal-stars"></span></p>
            <p><strong>Comment:</strong></p>
            <div id="modal-comment" style="background-color: #f9f9f9; padding: 10px; border-radius: 5px;"></div>
            <p><strong>Date:</strong> <span id="modal-date"></span></p>
            <p><strong>Status:</strong> <span id="modal-status" class="badge"></span></p>
        </div>
        <div class="modal-footer">
            <a id="modal-toggle-link" href="#" class="btn btn-primary">Change Status</a>
            <a id="modal-delete-link" href="#" class="btn btn-danger">Delete</a>
        </div>
    </div>
</div>

<?php
// Custom JavaScript for this page
$additional_js = <<<JS
// Testimonial Modal functionality
const modal = document.getElementById("testimonial-modal");

// When user clicks on a view button, open the modal
document.querySelectorAll(".view-testimonial").forEach(button => {
    button.addEventListener("click", function() {
        const id = this.getAttribute("data-id");
        const username = this.getAttribute("data-username");
        const service = this.getAttribute("data-service");
        const rating = this.getAttribute("data-rating");
        const comment = this.getAttribute("data-comment");
        const date = this.getAttribute("data-date");
        const status = this.getAttribute("data-status");
        
        // Set modal content
        document.getElementById("modal-username").textContent = username;
        document.getElementById("modal-service").textContent = service;
        
        // Set rating stars
        const starsContainer = document.getElementById("modal-stars");
        starsContainer.innerHTML = "";
        for (let i = 1; i <= 5; i++) {
            const star = document.createElement("i");
            star.className = i <= rating ? "fas fa-star" : "far fa-star";
            star.style.color = i <= rating ? "#FFD700" : "#ccc";
            starsContainer.appendChild(star);
        }
        
        document.getElementById("modal-comment").textContent = comment;
        document.getElementById("modal-date").textContent = date;
        
        // Set status badge
        const statusBadge = document.getElementById("modal-status");
        statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        statusBadge.className = status === "active" ? "badge badge-success" : "badge badge-danger";
        
        // Set action links
        document.getElementById("modal-toggle-link").href = `testimonials.php?action=toggle&id=\${id}`;
        document.getElementById("modal-delete-link").href = `testimonials.php?action=delete&id=\${id}`;
        
        // Show modal
        modal.style.display = "block";
    });
});

// Close modal when clicking the X
document.querySelector(".close").addEventListener("click", function() {
    modal.style.display = "none";
});

// Close modal when clicking outside of it
window.addEventListener("click", function(event) {
    if (event.target === modal) {
        modal.style.display = "none";
    }
});

// Toggle Status confirmation
document.querySelectorAll(".toggle-status, #modal-toggle-link").forEach(button => {
    button.addEventListener("click", function(e) {
        if (!confirm("Are you sure you want to change the status of this testimonial?")) {
            e.preventDefault();
        }
    });
});

// Delete confirmation
document.querySelectorAll(".delete-testimonial, #modal-delete-link").forEach(button => {
    button.addEventListener("click", function(e) {
        if (!confirm("Are you sure you want to delete this testimonial? This action cannot be undone.")) {
            e.preventDefault();
        }
    });
});
JS;

// Include the footer
include "includes/footer.php";
?>
