<?php
// Include database configuration
require_once "../includes/config.php";

// Define variables and initialize with empty values
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = $token_err = "";
$success_msg = "";

// Check if token is provided in URL
if(!isset($_GET["token"]) || empty($_GET["token"])){
    $token_err = "Invalid or missing token. Please request a new password reset link.";
} else {
    $token = $_GET["token"];
    
    // Check if token exists and is not expired
    $sql = "SELECT id, email FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()";
    
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "s", $token);
        
        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_store_result($stmt);
            
            if(mysqli_stmt_num_rows($stmt) != 1){
                $token_err = "Invalid or expired token. Please request a new password reset link.";
            } else {
                mysqli_stmt_bind_result($stmt, $user_id, $user_email);
                mysqli_stmt_fetch($stmt);
            }
        } else {
            $token_err = "Something went wrong. Please try again later.";
        }
        
        mysqli_stmt_close($stmt);
    }
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST" && empty($token_err)){
    
    // Validate new password
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Please enter the new password.";
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "Password must have at least 6 characters.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm the password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before updating the database
    if(empty($new_password_err) && empty($confirm_password_err)){
        // Prepare an update statement
        $sql = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_password, $token);
            
            // Set parameters
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Password updated successfully. Display success message and provide login link
                $success_msg = "Your password has been reset successfully. You can now login with your new password.";
            } else{
                $token_err = "Something went wrong. Please try again later.";
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
    <title>Reset Password - Dulify</title>
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
            <i class="fas fa-key text-4xl primary-text mb-4"></i>
            <h2 class="text-2xl font-bold text-gray-800">Reset Your Password</h2>
            <p class="text-gray-600 mt-2">Create a new password for your account</p>
        </div>
        
        <?php if(!empty($token_err)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p><?php echo $token_err; ?></p>
                <p class="mt-2"><a href="forgot_password.php" class="primary-text hover:underline">Request a new password reset</a></p>
            </div>
        <?php elseif(!empty($success_msg)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p><?php echo $success_msg; ?></p>
                <p class="mt-2"><a href="login.php" class="primary-text hover:underline">Go to Login</a></p>
            </div>
        <?php else: ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?token=' . $token; ?>" method="post">
                <div class="mb-4">
                    <label for="new_password" class="block text-gray-700 text-sm font-medium mb-2">New Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="new_password" id="new_password" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 <?php echo (!empty($new_password_err)) ? 'border-red-500' : ''; ?>" placeholder="Enter new password">
                    </div>
                    <?php if(!empty($new_password_err)): ?>
                        <p class="text-red-500 text-xs mt-1"><?php echo $new_password_err; ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="mb-6">
                    <label for="confirm_password" class="block text-gray-700 text-sm font-medium mb-2">Confirm Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="confirm_password" id="confirm_password" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 <?php echo (!empty($confirm_password_err)) ? 'border-red-500' : ''; ?>" placeholder="Confirm new password">
                    </div>
                    <?php if(!empty($confirm_password_err)): ?>
                        <p class="text-red-500 text-xs mt-1"><?php echo $confirm_password_err; ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="mb-6">
                    <button type="submit" class="w-full primary-bg hover:bg-green-600 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-300">
                        Reset Password
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>