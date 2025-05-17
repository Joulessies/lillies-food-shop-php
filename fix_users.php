<?php
// Include database connection
require_once 'config/db_connect.php';

// Check if the admin user exists and update them
$admin_email = "admin@lilliesfoodshop.com";
$check_sql = "SELECT id FROM users WHERE email = '$admin_email'";
$result = mysqli_query($conn, $check_sql);

if ($result && mysqli_num_rows($result) > 0) {
    // Admin exists, update to make sure they have admin privileges
    $update_sql = "UPDATE users SET is_admin = 1 WHERE email = '$admin_email'";
    if (mysqli_query($conn, $update_sql)) {
        echo "Existing admin account updated with admin privileges.<br>";
    } else {
        echo "Error updating admin privileges: " . mysqli_error($conn) . "<br>";
    }
} else {
    // Admin doesn't exist, find a different user to make admin
    $find_user_sql = "SELECT id, email FROM users LIMIT 1";
    $user_result = mysqli_query($conn, $find_user_sql);
    
    if ($user_result && mysqli_num_rows($user_result) > 0) {
        $user = mysqli_fetch_assoc($user_result);
        $user_id = $user['id'];
        $user_email = $user['email'];
        
        $update_sql = "UPDATE users SET is_admin = 1 WHERE id = $user_id";
        if (mysqli_query($conn, $update_sql)) {
            echo "User $user_email has been given admin privileges.<br>";
            echo "You can log in with this user's credentials to access the admin panel.<br>";
        } else {
            echo "Error updating user privileges: " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "No users found in the database to grant admin privileges.<br>";
    }
}

// Close the connection
mysqli_close($conn);
echo "<br><a href='login.php'>Go to login page</a>";
?> 