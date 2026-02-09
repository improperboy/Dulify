<?php
// Set page title
$page_title = "Add New Service";

// Include database configuration
require_once "../includes/config.php";

// Initialize variables
$name = $description = $image = "";
$price = 0;
$category = "website";
$success_message = $error_message = "";

// Process form submission
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input
    if(empty(trim($_POST["name"]))) {
        $error_message = "Please enter a service name.";
    } elseif(empty(trim($_POST["description"]))) {
        $error_message = "Please enter a service description.";
    } elseif(!is_numeric($_POST["price"]) || floatval($_POST["price"]) <= 0) {
        $error_message = "Please enter a valid price.";
    } else {
        $name = trim($_POST["name"]);
        $description = trim($_POST["description"]);
        $price = floatval($_POST["price"]);
        $category = $_POST["category"];
        $image = !empty($_POST["image"]) ? trim($_POST["image"]) : NULL;
        
        // Insert into database
        $sql = "INSERT INTO services (name, description, price, category, image) VALUES (?, ?, ?, ?, ?)";
        
        if($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssdss", $name, $description, $price, $category, $image);
            
            if(mysqli_stmt_execute($stmt)) {
                $success_message = "Service added successfully!";
                // Clear form
                $name = $description = $image = "";
                $price = 0;
                $category = "website";
            } else {
                $error_message = "Something went wrong. Please try again later.";
            }
            
            mysqli_stmt_close($stmt);
        }
    }
}



// Custom CSS for this page
$additional_css = <<<CSS
.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
}

input[type="text"],
input[type="number"],
textarea,
select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
    font-family: inherit;
    font-size: 14px;
}

textarea {
    min-height: 120px;
    resize: vertical;
}

button[type="submit"] {
    background-color: #4a6cf7;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
}

button[type="submit"]:hover {
    background-color: #3a5bd9;
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

@media (max-width: 768px) {
    .form-group {
        margin-bottom: 15px;
    }
    
    button[type="submit"] {
        width: 100%;
        padding: 10px;
    }
}
CSS;

// Include the header (with responsive sidebar)
include "includes/header.php";
?>

<div class="content-wrapper">
    <div class="content-header">
        <h1>Add New Service</h1>
        <p>Create a new service for users to purchase.</p>
    </div>

    <div class="content-body">
        <div class="card">
            <!-- Success/Error Messages -->
            <?php if(!empty($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if(!empty($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <!-- Service Form -->
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" required><?php echo htmlspecialchars($description); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="price">Price ($)</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" class="form-control" value="<?php echo $price; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" class="form-control">
                        <option value="website" <?php echo $category == 'website' ? 'selected' : ''; ?>>Website</option>
                        <option value="attendance" <?php echo $category == 'attendance' ? 'selected' : ''; ?>>Attendance System</option>
                        <option value="homework" <?php echo $category == 'homework' ? 'selected' : ''; ?>>Homework System</option>
                        <option value="inventory" <?php echo $category == 'inventory' ? 'selected' : ''; ?>>Inventory Management</option>
                        <option value="other" <?php echo $category == 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="image">Image URL (optional)</label>
                    <input type="text" id="image" name="image" class="form-control" value="<?php echo htmlspecialchars($image ?? ''); ?>" placeholder="e.g. /img/services/service1.jpg">
                    <small>Leave blank to use default category image</small>
                </div>
                
                <div class="btn-container">
                    <button type="submit" class="btn-primary">Add Service</button>
                    <a href="services.php" class="btn-secondary">Cancel</a>
                </div>
            </form>
        
                    
                
                
            </div>
        </div>
    </div>
</body>
</html>
