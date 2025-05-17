<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "medicine_inventory");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create login_attempts table
$sql = "CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    time DATETIME NOT NULL,
    success TINYINT(1) NOT NULL,
    INDEX (username, time)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table login_attempts created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?> 