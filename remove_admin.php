<?php
// Database connection
require_once 'config/db_connect.php';

echo "<h1>Admin User Removal</h1>";

// Check if admin user exists
$admin_email = "admin@lilliesfoodshop.com";
$result = mysqli_query($conn, "SELECT id FROM users WHERE email = '$admin_email'");

if (!$result) {
    die("Error checking for admin: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) == 0) {
    echo "<p>No admin user found with email: $admin_email</p>";
} else {
    // Check if there are other admins before deleting
    $check_other_admins = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE is_admin = 1 AND email != '$admin_email'");
    $admin_count = mysqli_fetch_assoc($check_other_admins);
    
    if ($admin_count['count'] == 0) {
        echo "<p>Cannot delete this admin as it appears to be the only admin user. Please create another admin user first.</p>";
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