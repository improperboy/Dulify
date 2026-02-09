<?php
// Include database configuration
require_once "../includes/config.php";
require_once "../includes/email_helper.php";

// Define variables and initialize with empty values
$email = "";
$email_err = $success_msg = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter your email address.";
    } else{
        $email = trim($_POST["email"]);
        
        // Check if email exists
        $sql = "SELECT id FROM users WHERE email = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = $email;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    // Email exists, generate reset token
                    $token = bin2hex(random_bytes(32));
                    $token_expiry = date('Y-m-d H:i:s', strtotime('+2 hour'));
                    
                    // Update user with reset token
                    $update_sql = "UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?";
                    
                    if($update_stmt = mysqli_prepare($conn, $update_sql)){
                        mysqli_stmt_bind_param($update_stmt, "sss", $token, $token_expiry, $email);
                        
                        if(mysqli_stmt_execute($update_stmt)){
                            // Send password reset email
                            // Check if we're on localhost or production server
    $is_local = (PHP_SAPI === 'cli' || (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost') || (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] == '127.0.0.1'));
    
    if ($is_local) {
        $reset_link = "http://{$_SERVER['HTTP_HOST']}/dulify/php/reset_password.php?token=" . $token;
    } else {
        // On production server (InfinityFree), use root path
        $reset_link = "http://{$_SERVER['HTTP_HOST']}/php/reset_password.php?token=" . $token;
    }
                            
                            // Prepare email content
                            $subject = "Reset Your Password - Dulify";
                            $message = "<html><body>";
                            $message .= "<h2>Reset Your Password</h2>";
                            $message .= "<p>Hello,</p>";
                            $message .= "<p>We received a request to reset your password. Click the link below to set a new password:</p>";
                            $message .= "<p><a href='$reset_link'>Reset Password</a></p>";
                            $message .= "<p>If you didn't request this, you can ignore this email.</p>";
                            $message .= "<p>The link will expire in 1 hour.</p>";
                            $message .= "<p>Regards,<br>The Dulify Team</p>";
                            $message .= "</body></html>";
                            
                            // Send email
                            if(sendEmail($email, $subject, $message)){
                                $success_msg = "A password reset link has been sent to your email address.";
                            } else {
                                $email_err = "Error sending email. Please try again later.";
                            }
                        } else{
                            $email_err = "Something went wrong. Please try again later.";
                        }
                        
                        mysqli_stmt_close($update_stmt);
                    }
                } else{
                    // For security reasons, don't reveal if email doesn't exist
                    $success_msg = "If your email exists in our system, a password reset link has been sent.";
                }
            } else{
                $email_err = "Something went wrong. Please try again later.";
            }
            
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Dulify</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .primary-bg {
            background-color: #4CAF50;
        }
        .primary-text {
            color: #4CAF50;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
        <div class="text-center mb-8">
            <i class="fas fa-lock text-4xl primary-text mb-4"></i>
            <h2 class="text-2xl font-bold text-gray-800">Forgot Your Password?</h2>
            <p class="text-gray-600 mt-2">Enter your email address and we'll send you a link to reset your password.</p>
        </div>
        
        <?php if(!empty($success_msg)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p><?php echo $success_msg; ?></p>
            </div>
        <?php endif; ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-6">
                <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </div>
                    <input type="email" name="email" id="email" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 <?php echo (!empty($email_err)) ? 'border-red-500' : ''; ?>" placeholder="Enter your email" value="<?php echo $email; ?>">
                </div>
                <?php if(!empty($email_err)): ?>
                    <p class="text-red-500 text-xs mt-1"><?php echo $email_err; ?></p>
                <?php endif; ?>
            </div>
            
            <div class="mb-6">
                <button type="submit" class="w-full primary-bg hover:bg-green-600 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-300">
                    Send Reset Link
                </button>
            </div>
            
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Remember your password? <a href="login.php" class="primary-text hover:underline">Back to Login</a>
                </p>
            </div>
        </form>
    </div>
</body>
</html>