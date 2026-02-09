<?php
// Include database configuration
require_once "../includes/config.php";
require_once "../includes/email_helper.php";

// Check if user is logged in and is an admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_type"] !== "admin") {
    header("location: ../php/login.php");
    exit;
}

// Check if form was submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $reply_to_email = trim($_POST["reply_to_email"]);
    $reply_to_name = trim($_POST["reply_to_name"]);
    $message_id = (int)$_POST["message_id"];
    $subject = trim($_POST["reply_subject"]);
    $message = trim($_POST["reply_message"]);
    
    // Validate inputs
    if(empty($reply_to_email) || empty($subject) || empty($message)) {
        $_SESSION["reply_error"] = "All fields are required.";
        header("location: messages.php");
        exit;
    }
    
    // Create HTML message
    $html_message = "<html><body>";
    $html_message .= "<p>Dear " . htmlspecialchars($reply_to_name) . ",</p>";
    $html_message .= "<p>" . nl2br(htmlspecialchars($message)) . "</p>";
    $html_message .= "<p>Regards,<br>The Dulify Team</p>";
    $html_message .= "</body></html>";
    
    // Send email
    $email_sent = sendEmail($reply_to_email, $subject, $html_message, $reply_to_name);
    
    if($email_sent) {
        // Update message status to read if not already
        $sql = "UPDATE contact_messages SET is_read = 1 WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $message_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        
        // Set success message
        $_SESSION["reply_success"] = "Your reply has been sent successfully.";
    } else {
        // Set error message
        $_SESSION["reply_error"] = "Failed to send email. Please try again.";
    }
    
    // Redirect back to messages page
    header("location: messages.php");
    exit;
}

// If not a POST request, redirect to messages page
header("location: messages.php");
exit;
?>