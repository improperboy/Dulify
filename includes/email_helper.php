<?php
/**
 * Email Helper Functions
 * 
 * This file contains functions for sending emails and generating verification tokens
 */

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load email configuration
require_once __DIR__ . '/email_config.php';

/**
 * Generates a random token for email verification
 * 
 * @return string The generated token
 */
function generateVerificationToken() {
    return bin2hex(random_bytes(32)); // 64 characters long
}

/**
 * Calculates the expiry time for a verification token
 * 
 * @param int $hours Number of hours until expiry
 * @return string MySQL datetime string for the expiry time
 */
function calculateTokenExpiry($hours = 24) {
    return date('Y-m-d H:i:s', strtotime("+{$hours} hours"));
}

/**
 * Sends an email using PHPMailer
 * 
 * @param string $to_email The recipient's email address
 * @param string $subject The email subject
 * @param string $message The email message (HTML)
 * @param string $to_name The recipient's name (optional)
 * @return bool Whether the email was sent successfully
 */
function sendEmail($to_email, $subject, $message, $to_name = '') {
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->SMTPDebug = EMAIL_DEBUG;                      // Enable verbose debug output
        $mail->isSMTP();                                     // Send using SMTP
        $mail->Host       = SMTP_HOST;                       // Set the SMTP server to send through
        $mail->SMTPAuth   = SMTP_AUTH;                       // Enable SMTP authentication
        $mail->Username   = SMTP_USERNAME;                   // SMTP username
        $mail->Password   = SMTP_PASSWORD;                   // SMTP password
        $mail->SMTPSecure = SMTP_SECURE === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS; // Enable TLS or SSL encryption
        $mail->Port       = SMTP_PORT;                       // TCP port to connect to
        
        // Recipients
        $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
        $mail->addAddress($to_email, $to_name);               // Add a recipient
        
        // Content
        $mail->isHTML(true);                                // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;
        
        // Send the email
        $mail->send();
        
        // Log the email for debugging
        $logFile = __DIR__ . "/email_log.txt";
        $logMessage = "\n\n" . date("Y-m-d H:i:s") . "\n";
        $logMessage .= "To: $to_email\n";
        $logMessage .= "Subject: $subject\n";
        $logMessage .= "Email sent successfully using PHPMailer\n";
        $logMessage .= "----------------------------------------\n";
        
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        
        return true;
    } catch (Exception $e) {
        // Log the error
        $logFile = __DIR__ . "/email_log.txt";
        $logMessage = "\n\n" . date("Y-m-d H:i:s") . "\n";
        $logMessage .= "To: $to_email\n";
        $logMessage .= "Subject: $subject\n";
        $logMessage .= "PHPMailer Error: " . $mail->ErrorInfo . "\n";
        $logMessage .= "----------------------------------------\n";
        
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        
        return false;
    }
}

/**
 * Sends a verification email to the user using PHPMailer
 * 
 * @param string $email The recipient's email address
 * @param string $username The recipient's username
 * @param string $token The verification token
 * @return bool Whether the email was sent successfully
 */
function sendVerificationEmail($email, $username, $token) {
    // Get the server protocol (http or https)
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    
    // Get the server host name
    $host = $_SERVER['HTTP_HOST'];
    
    // Build the verification URL
    // Check if we're on localhost or production server
    $is_local = (PHP_SAPI === 'cli' || (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost') || (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] == '127.0.0.1'));
    
    if ($is_local) {
        $verificationUrl = "$protocol://$host/dulify/verify_email.php?token=$token";
    } else {
        // On production server (InfinityFree), use root path
        $verificationUrl = "$protocol://$host/verify_email.php?token=$token";
    }
    
    // Set email subject
    $subject = "Verify Your Email Address - Dulify";
    
    // Create email message
    $message = "<html><body>";
    $message .= "<h2>Welcome to Dulify!</h2>";
    $message .= "<p>Hello $username,</p>";
    $message .= "<p>Thank you for registering with Dulify. Please click the button below to verify your email address:</p>";
    $message .= "<p><a href='$verificationUrl' style='display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Verify Email Address</a></p>";
    $message .= "<p>If the button above doesn't work, copy and paste the following link into your browser:</p>";
    $message .= "<p>$verificationUrl</p>";
    $message .= "<p>This link will expire in 24 hours.</p>";
    $message .= "<p>If you did not create an account, please ignore this email.</p>";
    $message .= "<p>Regards,<br>The Dulify Team</p>";
    $message .= "</body></html>";
    
    // Use the general sendEmail function
    return sendEmail($email, $subject, $message, $username);
}

?>