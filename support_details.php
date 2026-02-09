<?php
// Include database configuration
require_once "includes/config.php";

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: php/login.php");
    exit;
}

// Check if support message ID is provided
if(!isset($_GET["id"]) || empty($_GET["id"])){
    header("location: dashboard.php?tab=support");
    exit;
}

$message_id = $_GET["id"];
$user_id = $_SESSION["id"];

// Fetch the support message details
$message = null;
$sql = "SELECT * FROM support_messages WHERE id = ? AND user_id = ?";

if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "ii", $message_id, $user_id);
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        if(mysqli_num_rows($result) == 1){
            $message = mysqli_fetch_assoc($result);
        } else {
            // Message not found or doesn't belong to user
            header("location: dashboard.php?tab=support");
            exit;
        }
    }
    mysqli_stmt_close($stmt);
}

// Fetch all replies for this message
$replies = array();
$sql = "SELECT * FROM support_replies WHERE message_id = ? ORDER BY created_at ASC";

if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $message_id);
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        while($row = mysqli_fetch_assoc($result)){
            $replies[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}

// Process new reply submission
$reply_success = $reply_error = "";
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_reply"])){
    $reply_text = trim($_POST["reply"]);
    
    if(empty($reply_text)){
        $reply_error = "Please enter your reply.";
    } else {
        // Insert reply into database
        $sql = "INSERT INTO support_replies (message_id, user_id, content) VALUES (?, ?, ?)";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "iis", $message_id, $user_id, $reply_text);
            
            if(mysqli_stmt_execute($stmt)){
                $reply_success = "Your reply has been sent.";
                
                // Update the message status to in_progress if it was closed
                if($message["status"] == "closed"){
                    $update_sql = "UPDATE support_messages SET status = 'in_progress' WHERE id = ?";
                    if($update_stmt = mysqli_prepare($conn, $update_sql)){
                        mysqli_stmt_bind_param($update_stmt, "i", $message_id);
                        mysqli_stmt_execute($update_stmt);
                        mysqli_stmt_close($update_stmt);
                    }
                }
                
                // Redirect to avoid form resubmission
                header("location: support_details.php?id=" . $message_id . "&success=1");
                exit;
            } else{
                $reply_error = "Something went wrong. Please try again later.";
            }
            
            mysqli_stmt_close($stmt);
        }
    }
}

// Handle reply success notification
if(isset($_GET['success']) && $_GET['success'] == '1'){
    $reply_success = "Your reply has been sent successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Conversation - Dulify</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            scroll-behavior: smooth;
        }
        .primary-bg {
            background-color: #4CAF50;
        }
        .primary-text {
            color: #4CAF50;
        }
        .secondary-bg {
            background-color: #FFC107;
        }
        .secondary-text {
            color: #FFC107;
        }
        .nav-link:hover {
            color: #4CAF50;
        }
        .mobile-menu {
            transition: all 0.3s ease;
        }
        
        /* Support specific styles */
        .message-header {
            background-color: #f0fdf4;
            padding: 1.5rem;
            border-radius: 0.5rem 0.5rem 0 0;
            border-bottom: 1px solid #e1e6f0;
        }
        .message-subject {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .message-metadata {
            display: flex;
            justify-content: space-between;
            color: #6b7280;
            font-size: 0.875rem;
        }
        .message-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            color: white;
            font-weight: 500;
        }
        .message-status.open {
            background-color: #4CAF50;
        }
        .message-status.in-progress {
            background-color: #FFC107;
        }
        .message-status.closed {
            background-color: #6b7280;
        }
        .chat-body {
            background-color: white;
            padding: 1.5rem;
            border-radius: 0 0 0.5rem 0.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .chat-message {
            margin-bottom: 1.25rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid #e5e7eb;
        }
        .chat-message:last-child {
            border-bottom: none;
        }
        .chat-message.original {
            background-color: #f0fdf4;
            padding: 1.25rem;
            border-radius: 0.5rem;
            margin: -1.5rem -1.5rem 1.5rem -1.5rem;
        }
        .chat-message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            color: #6b7280;
        }
        .chat-message-sender {
            font-weight: 500;
            color: #111827;
        }
        .user-message .chat-message-content {
            background-color: #f0fdf4;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-top: 0.5rem;
        }
        .admin-message .chat-message-content {
            background-color: #f3f4f6;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-top: 0.5rem;
        }
        .reply-form {
            margin-top: 2rem;
        }
        .reply-heading {
            margin-bottom: 1rem;
            font-weight: 500;
            font-size: 1.125rem;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation - Consistent with main site -->
    <nav class="bg-white shadow-md fixed w-full z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-school primary-text text-3xl mr-2"></i>
                        <span class="text-xl font-bold text-gray-800">Dulify</span>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Home</a>
                    <a href="services.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Services</a>
                    <a href="about.php" class="nav-link text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">About</a>
                    <a href="dashboard.php" class="primary-bg hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-300">Dashboard</a>
                    <a href="php/logout.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition duration-300">Logout</a>
                </div>
                <div class="md:hidden flex items-center">
                    <button id="menu-btn" class="text-gray-500 hover:text-gray-900 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile menu -->
        <div id="mobile-menu" class="mobile-menu hidden md:hidden bg-white shadow-lg">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="index.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Home</a>
                <a href="services.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Services</a>
                <a href="about.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">About</a>
                <a href="dashboard.php" class="block px-3 py-2 rounded-md text-base font-medium text-white bg-green-500 hover:bg-green-600">Dashboard</a>
                <a href="php/logout.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Support Conversation Section -->
    <section class="pt-24 pb-12 md:pt-32 md:pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php if($message): ?>
                <div class="max-w-4xl mx-auto">
                    <div class="mb-6">
                        <a href="dashboard.php?tab=support" class="inline-flex items-center text-green-600 hover:text-green-800">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Support
                        </a>
                    </div>
                    
                    <?php if($reply_success): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
                            <?php echo $reply_success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($reply_error): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                            <?php echo $reply_error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="message-header">
                        <div class="message-subject"><?php echo htmlspecialchars($message["subject"]); ?></div>
                        <div class="message-metadata">
                            <div>Opened on <?php echo date("M d, Y \a\\t h:i A", strtotime($message["created_at"])); ?></div>
                            <div class="message-status <?php echo str_replace('_', '-', $message["status"]); ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $message["status"])); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="chat-body">
                        <!-- Original Message -->
                        <div class="chat-message original">
                            <div class="chat-message-header">
                                <div class="chat-message-sender">You</div>
                                <div><?php echo date("M d, Y \a\\t h:i A", strtotime($message["created_at"])); ?></div>
                            </div>
                            <div class="chat-message-content">
                                <?php echo nl2br(htmlspecialchars($message["message"])); ?>
                            </div>
                        </div>
                        
                        <!-- Replies -->
                        <?php if(!empty($replies)): ?>
                            <?php foreach($replies as $reply): ?>
                                <div class="chat-message <?php echo $reply["user_id"] ? 'user-message' : 'admin-message'; ?>">
                                    <div class="chat-message-header">
                                        <div class="chat-message-sender"><?php echo $reply["user_id"] ? 'You' : 'Support Team'; ?></div>
                                        <div><?php echo date("M d, Y \a\\t h:i A", strtotime($reply["created_at"])); ?></div>
                                    </div>
                                    <div class="chat-message-content">
                                        <?php echo nl2br(htmlspecialchars($reply["content"])); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <!-- Reply Form -->
                        <div class="reply-form">
                            <div class="reply-heading">Add a Reply</div>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $message_id); ?>" method="post">
                                <div class="mb-4">
                                    <textarea name="reply" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition duration-300" rows="5" required></textarea>
                                </div>
                                <div>
                                    <button type="submit" name="submit_reply" class="primary-bg hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition duration-300">Send Reply</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <p class="text-lg mb-6">The requested support conversation could not be found.</p>
                    <a href="dashboard.php?tab=support" class="primary-bg hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition duration-300 inline-block">Back to Support</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer - Consistent with main site -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-school text-green-400 text-3xl mr-2"></i>
                        <span class="text-xl font-bold">Dulify</span>
                    </div>
                    <p class="text-gray-400">Empowering local schools and small businesses with affordable digital solutions.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-400 hover:text-white transition duration-300">Home</a></li>
                        <li><a href="services.php" class="text-gray-400 hover:text-white transition duration-300">Services</a></li>
                        <li><a href="about.php" class="text-gray-400 hover:text-white transition duration-300">About</a></li>
                        <li><a href="dashboard.php" class="text-gray-400 hover:text-white transition duration-300">Dashboard</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Services</h4>
                    <ul class="space-y-2">
                        <?php
                        // Display service links in footer
                        $sql = "SELECT id, name FROM services LIMIT 4";
                        $result = mysqli_query($conn, $sql);
                        
                        if(mysqli_num_rows($result) > 0) {
                            while($service = mysqli_fetch_assoc($result)) {
                                echo '<li><a href="services.php?id=' . htmlspecialchars($service['id']) . '" class="text-gray-400 hover:text-white transition duration-300">' . htmlspecialchars($service['name']) . '</a></li>';
                            }
                        }
                        ?>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="privacy.php" class="text-gray-400 hover:text-white transition duration-300">Privacy Policy</a></li>
                        <li><a href="terms.php" class="text-gray-400 hover:text-white transition duration-300">Terms of Service</a></li>
                        <li><a href="cookies.php" class="text-gray-400 hover:text-white transition duration-300">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; <?php echo date("Y"); ?> Dulify. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>