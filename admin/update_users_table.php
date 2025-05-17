<?php
// Include database connection
$conn = require_once '../config/db_connect.php';

// Alter users table to add is_admin column
$sql = "ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0";

if (mysqli_query($conn, $sql)) {
    echo "Users table updated successfully to include admin flag.<br>";
} else {
    echo "Error updating users table: " . mysqli_error($conn) . "<br>";
}

// Create admin user if it doesn't exist
$admin_email = "admin@lilliesfoodshop.com";
$admin_password = password_hash("admin123", PASSWORD_DEFAULT);
$admin_name = "Administrator";

// Check if admin already exists
$check_sql = "SELECT id FROM users WHERE email = '$admin_email'";
$result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($result) > 0) {
    // Admin exists, update to make sure they have admin privileges
    $update_sql = "UPDATE users SET is_admin = 1 WHERE email = '$admin_email'";
    if (mysqli_query($conn, $update_sql)) {
        echo "Existing admin account updated with admin privileges.<br>";
    } else {
        echo "Error updating admin privileges: " . mysqli_error($conn) . "<br>";
    }
} else {
    // Admin doesn't exist, create one
    $insert_sql = "INSERT INTO users (name, email, password, is_admin) VALUES ('$admin_name', '$admin_email', '$admin_password', 1)";
    if (mysqli_query($conn, $insert_sql)) {
        echo "New admin account created successfully.<br>";
        echo "<strong>Login credentials:</strong><br>";
        echo "Email: $admin_email<br>";
        echo "Password: admin123<br>";
        echo "<strong>Please change this password immediately after first login.</strong><br>";
    } else {
        echo "Error creating admin account: " . mysqli_error($conn) . "<br>";
    }
}

// Close the connection
mysqli_close($conn);
?> 