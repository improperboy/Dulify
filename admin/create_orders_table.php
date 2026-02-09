<?php
// Include database configuration
require_once __DIR__ . "/../includes/config.php";

// SQL to create orders table
$sql = "CREATE TABLE IF NOT EXISTS orders (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    additional_notes TEXT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (service_id) REFERENCES services(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Orders table created successfully";
} else {
    echo "Error creating orders table: " . $conn->error;
}

// Check if purchases table has a status column, if not add it
$result = $conn->query("SHOW COLUMNS FROM purchases LIKE 'status'");
if ($result->num_rows == 0) {
    $sql = "ALTER TABLE purchases ADD COLUMN status ENUM('active', 'expired', 'cancelled') DEFAULT 'active'";
    if ($conn->query($sql) === TRUE) {
        echo "<br>Status column added to purchases table";
    } else {
        echo "<br>Error adding status column: " . $conn->error;
    }
}

$conn->close();
?>
