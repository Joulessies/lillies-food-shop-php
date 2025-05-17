<?php
// Database connection
require_once 'config/db_connect.php';

// The email of the admin to remove
$admin_email = "admin@lilliesfoodshop.com";

echo "<h1>Removing Specific Admin</h1>";

// Check if admin exists
$result = mysqli_query($conn, "SELECT id FROM users WHERE email = '$admin_email'");

if (!$result) {
    die("Error checking for admin: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) == 0) {
    echo "<p>No admin user found with email: $admin_email</p>";
} else {
    $admin = mysqli_fetch_assoc($result);
    $admin_id = $admin['id'];
    
    // Check if there are other admins before deleting
    $check_other_admins = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE is_admin = 1 AND email != '$admin_email'");
    $admin_count = mysqli_fetch_assoc($check_other_admins);
    
    if ($admin_count['count'] == 0) {
        echo "<p>Cannot delete this admin as it appears to be the only admin user. Please create another admin user first.</p>";
        
        // Create a new admin user if this is the only one
        $new_admin_name = "Admin";
        $new_admin_email = "admin" . rand(1000, 9999) . "@example.com";
        $new_admin_password = password_hash("admin123", PASSWORD_DEFAULT);
        
        $create_admin = mysqli_query($conn, "INSERT INTO users (name, email, password, is_admin) VALUES ('$new_admin_name', '$new_admin_email', '$new_admin_password', 1)");
        
        if ($create_admin) {
            echo "<p>Created a new admin user with email: $new_admin_email and password: admin123</p>";
            
            // Now try to delete the original admin
            $delete = mysqli_query($conn, "DELETE FROM users WHERE email = '$admin_email'");
            
            if ($delete) {
                echo "<p>Successfully removed admin user with email: $admin_email</p>";
            } else {
                echo "<p>Error removing admin: " . mysqli_error($conn) . "</p>";
            }
        } else {
            echo "<p>Error creating new admin user: " . mysqli_error($conn) . "</p>";
        }
    } else {
        // Delete the admin
        $delete = mysqli_query($conn, "DELETE FROM users WHERE email = '$admin_email'");
        
        if ($delete) {
            echo "<p>Successfully removed admin user with email: $admin_email</p>";
        } else {
            echo "<p>Error removing admin: " . mysqli_error($conn) . "</p>";
        }
    }
}

echo "<p><a href='admin/user_management.php'>Return to user management</a></p>";
?> 