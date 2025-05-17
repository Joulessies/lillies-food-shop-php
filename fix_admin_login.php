<?php
// Script to fix admin login issues

// Include database connection
require_once 'config/db_connect.php';

echo "<h1>Admin Login Diagnostic Tool</h1>";

// 1. Check database connection
echo "<h2>1. Database Connection</h2>";
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    echo "<p style='color:green'>Database connection successful!</p>";
}

// 2. Check users table structure
echo "<h2>2. Users Table Structure</h2>";
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
if (mysqli_num_rows($table_check) == 0) {
    echo "<p style='color:red'>Users table does not exist! Creating it now...</p>";
    
    $create_table_sql = "CREATE TABLE users (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        is_admin TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (mysqli_query($conn, $create_table_sql)) {
        echo "<p style='color:green'>Users table created successfully!</p>";
    } else {
        die("Error creating users table: " . mysqli_error($conn));
    }
} else {
    echo "<p style='color:green'>Users table exists.</p>";
    
    // Check if is_admin column exists
    $result = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'is_admin'");
    if (mysqli_num_rows($result) == 0) {
        echo "<p style='color:orange'>is_admin column missing. Adding it now...</p>";
        $alter_sql = "ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0";
        if (mysqli_query($conn, $alter_sql)) {
            echo "<p style='color:green'>is_admin column added successfully!</p>";
        } else {
            echo "<p style='color:red'>Error adding is_admin column: " . mysqli_error($conn) . "</p>";
        }
    }
}

// 3. Check admin user
echo "<h2>3. Admin User Check</h2>";
$admin_email = "admin@lilliesfoodshop.com";
$admin_password = "admin123";

// Check if admin exists
$check_sql = "SELECT id, name, email, password, is_admin FROM users WHERE email = ?";
if ($stmt = mysqli_prepare($conn, $check_sql)) {
    mysqli_stmt_bind_param($stmt, "s", $admin_email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if (mysqli_stmt_num_rows($stmt) > 0) {
        echo "<p style='color:green'>Admin user found!</p>";
        
        // Get admin details
        mysqli_stmt_bind_result($stmt, $id, $name, $email, $hashed_password, $is_admin);
        mysqli_stmt_fetch($stmt);
        
        echo "<p>User ID: $id</p>";
        echo "<p>Name: $name</p>";
        echo "<p>Email: $email</p>";
        echo "<p>Is Admin: " . ($is_admin ? "Yes" : "No") . "</p>";
        
        // Check if password is correct
        if (password_verify($admin_password, $hashed_password)) {
            echo "<p style='color:green'>Password verification successful!</p>";
        } else {
            echo "<p style='color:red'>Password verification failed! Updating password...</p>";
            
            // Update password
            $new_hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE users SET password = ? WHERE email = ?";
            
            if ($update_stmt = mysqli_prepare($conn, $update_sql)) {
                mysqli_stmt_bind_param($update_stmt, "ss", $new_hashed_password, $admin_email);
                
                if (mysqli_stmt_execute($update_stmt)) {
                    echo "<p style='color:green'>Password updated successfully!</p>";
                } else {
                    echo "<p style='color:red'>Error updating password: " . mysqli_error($conn) . "</p>";
                }
                
                mysqli_stmt_close($update_stmt);
            }
        }
        
        // Ensure admin privileges
        if (!$is_admin) {
            echo "<p style='color:orange'>User does not have admin privileges. Updating...</p>";
            
            $update_sql = "UPDATE users SET is_admin = 1 WHERE email = ?";
            if ($update_stmt = mysqli_prepare($conn, $update_sql)) {
                mysqli_stmt_bind_param($update_stmt, "s", $admin_email);
                
                if (mysqli_stmt_execute($update_stmt)) {
                    echo "<p style='color:green'>Admin privileges granted successfully!</p>";
                } else {
                    echo "<p style='color:red'>Error granting admin privileges: " . mysqli_error($conn) . "</p>";
                }
                
                mysqli_stmt_close($update_stmt);
            }
        }
    } else {
        echo "<p style='color:orange'>Admin user not found. Creating new admin user...</p>";
        
        // Create new admin user
        $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
        $name = "Administrator";
        
        $insert_sql = "INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, 1)";
        
        if ($insert_stmt = mysqli_prepare($conn, $insert_sql)) {
            mysqli_stmt_bind_param($insert_stmt, "sss", $name, $admin_email, $hashed_password);
            
            if (mysqli_stmt_execute($insert_stmt)) {
                echo "<p style='color:green'>Admin user created successfully!</p>";
                echo "<p>Email: $admin_email<br>Password: $admin_password</p>";
            } else {
                echo "<p style='color:red'>Error creating admin user: " . mysqli_error($conn) . "</p>";
            }
            
            mysqli_stmt_close($insert_stmt);
        }
    }
    
    mysqli_stmt_close($stmt);
}

// 4. Test login process
echo "<h2>4. Testing Login Process</h2>";
$test_sql = "SELECT id, name, email, password, is_admin FROM users WHERE email = ?";
if ($test_stmt = mysqli_prepare($conn, $test_sql)) {
    mysqli_stmt_bind_param($test_stmt, "s", $admin_email);
    
    if (mysqli_stmt_execute($test_stmt)) {
        mysqli_stmt_store_result($test_stmt);
        
        if (mysqli_stmt_num_rows($test_stmt) == 1) {
            mysqli_stmt_bind_result($test_stmt, $id, $name, $email, $hashed_password, $is_admin);
            
            if (mysqli_stmt_fetch($test_stmt)) {
                if (password_verify($admin_password, $hashed_password)) {
                    echo "<p style='color:green'>Login test successful!</p>";
                    echo "<p>You should now be able to log in with:</p>";
                    echo "<p>Email: $admin_email<br>Password: $admin_password</p>";
                    
                    echo "<p><a href='admin/index.php' style='color:blue;'>Go to Admin Login Page</a></p>";
                } else {
                    echo "<p style='color:red'>Login test failed: Password verification failed</p>";
                }
            }
        } else {
            echo "<p style='color:red'>Login test failed: User not found</p>";
        }
    } else {
        echo "<p style='color:red'>Login test failed: Query execution error</p>";
    }
    
    mysqli_stmt_close($test_stmt);
}

// Close connection
mysqli_close($conn);
?> 