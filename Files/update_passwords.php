<?php
require_once 'security.php';

// Database connection
$conn = new mysqli("localhost", "root", "", "medicine_inventory");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all users
$sql = "SELECT id, username, password FROM admin";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Hash the existing password
        $hashed_password = hashPassword($row['password']);
        
        // Update the password in database
        $update_sql = "UPDATE admin SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $hashed_password, $row['id']);
        
        if ($stmt->execute()) {
            echo "Updated password for user: " . $row['username'] . "<br>";
        } else {
            echo "Error updating password for user: " . $row['username'] . "<br>";
        }
    }
} else {
    echo "No users found";
}

$conn->close();
?> 