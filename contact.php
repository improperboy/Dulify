<?php
// Include database configuration
require_once "includes/config.php";

// Initialize variables
$name = $email = $subject = $message = "";
$contact_success = $contact_error = "";

// Process form submission
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate name
    if(empty(trim($_POST["name"]))){
        $contact_error = "Please enter your name.";
    } else{
        $name = trim($_POST["name"]);
    }
    
    // Validate email
    if(empty(trim($_POST["email"]))){
        $contact_error = "Please enter your email.";
    } elseif(!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)){
        $contact_error = "Please enter a valid email address.";
    } else{
        $email = trim($_POST["email"]);
    }
    
    // Validate subject
    if(empty(trim($_POST["subject"]))){
        $contact_error = "Please enter a subject.";
    } else{
        $subject = trim($_POST["subject"]);
    }
    
    // Validate message
    if(empty(trim($_POST["message"]))){
        $contact_error = "Please enter your message.";
    } else{
        $message = trim($_POST["message"]);
    }
    
    // If no errors, simulate sending the message
    if(empty($contact_error)){
        // In a real application, you would send an email or store in database
        $contact_success = "Thank you for your message! We will get back to you soon.";
        $name = $email = $subject = $message = "";
    }
}

// Check if user is logged in
$logged_in = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Dulify</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .contact-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }
        .contact-info {
            flex: 1;
            min-width: 300px;
        }
        .contact-form {
            flex: 2;
            min-width: 300px;
        }
        .contact-method {
            margin-bottom: 30px;
        }
        .contact-method h3 {
            margin-bottom: 10px;
            font-size: 18px;
            color: var(--primary-color);
        }
        .contact-method p {
            margin-bottom: 5px;
            font-size: 16px;
        }
        .contact-social {
            margin-top: 30px;
        }
        .contact-social a {
            display: inline-block;
            margin-right: 15px;
            color: var(--primary-color);
            font-size: 18px;
            transition: all 0.3s ease;
        }
        .contact-social a:hover {
            color: var(--secondary-color);
        }
        .map-container {
            margin-top: 50px;
            border-radius: 10px;
            overflow: hidden;
            height: 400px;
        }
        .map-placeholder {
            background-color: #f5f8ff;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            text-align: center;
            padding: 20px;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="container header-container">
            <div class="logo">
                <a href="index.php">Dulify</a>
            </div>
            <nav>
                <div class="nav-links">
                    <a href="index.php">Home</a>
                    <a href="services.php">Services</a>
                    <a href="about.php">About Us</a>
                    <a href="contact.php" class="active">Contact</a>
                </div>
            </nav>
            <div class="auth-buttons">
                <?php if($logged_in): ?>
                    <a href="dashboard.php" class="btn btn-outline">Dashboard</a>
                    <a href="php/logout.php" class="btn btn-primary">Logout</a>
                <?php else: ?>
                    <a href="php/login.php" class="btn btn-outline">Login</a>
                    <a href="php/register.php" class="btn btn-primary">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Contact Hero Section -->
    <section class="hero-section" style="background-color: #f5f8ff;">
        <div class="container">
            <div class="hero-content" style="text-align: center; max-width: 800px; margin: 0 auto;">
                <h1>Get in Touch</h1>
                <p>Have questions about our services? Need support with your digital solutions? Our team is here to help you succeed.</p>
            </div>
        </div>
    </section>

    <!-- Contact Main Section -->
    <section style="padding: 80px 0;">
        <div class="container">
            <?php if($contact_success): ?>
                <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 40px; text-align: center;">
                    <?php echo $contact_success; ?>
                </div>
            <?php endif; ?>
            
            <div class="contact-container">
                <div class="contact-info">
                    <h2 style="margin-bottom: 30px;">Contact Information</h2>
                    
                    <div class="contact-method">
                        <h3>Email Us</h3>
                        <p>General Inquiries: info@dulify.com</p>
                        <p>Support: support@dulify.com</p>
                        <p>Sales: sales@dulify.com</p>
                    </div>
                    
                    <div class="contact-method">
                        <h3>Call Us</h3>
                        <p>Phone: +1 (123) 456-7890</p>
                        <p>Toll-Free: 1-800-DULIFY</p>
                    </div>
                    
                    <div class="contact-method">
                        <h3>Visit Us</h3>
                        <p>123 Digital Lane</p>
                        <p>Tech City, TC 12345</p>
                        <p>United States</p>
                    </div>
                    
                    <div class="contact-method">
                        <h3>Business Hours</h3>
                        <p>Monday - Friday: 9:00 AM - 5:00 PM</p>
                        <p>Saturday: 10:00 AM - 2:00 PM</p>
                        <p>Sunday: Closed</p>
                    </div>
                    
                    <div class="contact-social">
                        <h3>Connect With Us</h3>
                        <a href="#"><span>Facebook</span></a>
                        <a href="#"><span>Twitter</span></a>
                        <a href="#"><span>LinkedIn</span></a>
                        <a href="#"><span>Instagram</span></a>
                    </div>
                </div>
                
                <div class="contact-form">
                    <h2 style="margin-bottom: 30px;">Send Us a Message</h2>
                    
                    <?php if($contact_error): ?>
                        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                            <?php echo $contact_error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label>Your Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Subject</label>
                            <input type="text" name="subject" class="form-control" value="<?php echo htmlspecialchars($subject); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Message</label>
                            <textarea name="message" class="form-control" rows="6" required><?php echo htmlspecialchars($message); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="form-submit">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="map-container">
                <div class="map-placeholder">
                    <div>
                        <h3>Our Location</h3>
                        <p>Pukhrayan, Kanpur Dehat, Uttar Pradesh 209111</p>
                        <p style="font-size: 14px; margin-top: 15px;">Map integration would be displayed here in a production environment.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>Dulify</h3>
                    <p>Empowering small schools and local businesses with affordable digital solutions.</p>
                </div>
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="services.php">Services</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contact Us</h3>
                    <ul>
                        <li>Email: hello@dulify.com</li>
                        <li>Phone: +91 79915 15802</li>
                        <li>Pukhrayan, Kanpur Dehat, Uttar Pradesh 209111</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date("Y"); ?> 2025 @Dulify. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>
