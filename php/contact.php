<?php
// Include database configuration
require_once "../includes/config.php";

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

// Initialize variables
$name = $email = $message = "";
$error = "";
$success = false;

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $error = "Please enter your name.";
    } else {
        $name = trim($_POST["name"]);
    }
    
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $error = "Please enter your email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $email = trim($_POST["email"]);
    }
    
    // Validate message
    if (empty(trim($_POST["message"]))) {
        $error = "Please enter your message.";
    } else {
        $message = trim($_POST["message"]);
    }
    
    // If no errors, insert the message into database
    if (empty($error)) {
        // Prepare an insert statement
        $sql = "INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sss", $param_name, $param_email, $param_message);
            
            // Set parameters
            $param_name = $name;
            $param_email = $email;
            $param_message = $message;
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                $success = true;
            } else {
                $error = "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
}

// Redirect back to the contact page with success/error message
if ($success) {
    header("location: ../index.php?contact_success=1#contact");
} else {
    header("location: ../index.php?contact_error=" . urlencode($error) . "#contact");
}
exit;
?>
