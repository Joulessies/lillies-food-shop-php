<?php
// Database connection
require_once 'config/db_connect.php';

// Start the output
echo "<h1>Rebuilding Users Table</h1>";

// Step 1: Backup existing users if the table exists
$users_backup = [];
$table_exists = false;

$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
if (mysqli_num_rows($check_table) > 0) {
    $table_exists = true;
    
    echo "<p>Backing up existing users...</p>";
    
    $result = mysqli_query($conn, "SELECT * FROM users WHERE id != 0");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users_backup[] = $row;
        }
        echo "<p>Backed up " . count($users_backup) . " users.</p>";
    } else {
        echo "<p>Error backing up users: " . mysqli_error($conn) . "</p>";
    }
}

// Step 2: Drop the existing table
if ($table_exists) {
    echo "<p>Dropping existing users table...</p>";
    
    $drop_result = mysqli_query($conn, "DROP TABLE users");
    if (!$drop_result) {
        die("<p>Error dropping table: " . mysqli_error($conn) . "</p>");
    }
    
    echo "<p>Users table dropped successfully.</p>";
}

// Step 3: Create a new users table with proper structure
echo "<p>Creating new users table...</p>";

$create_sql = "CREATE TABLE users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$create_result = mysqli_query($conn, $create_sql);
if (!$create_result) {
    die("<p>Error creating table: " . mysqli_error($conn) . "</p>");
}

echo "<p>Users table created successfully with proper structure.</p>";

// Step 4: Restore backed up users
if (count($users_backup) > 0) {
    echo "<p>Restoring " . count($users_backup) . " users...</p>";
    
    $restored = 0;
    foreach ($users_backup as $user) {
        $name = mysqli_real_escape_string($conn, $user['name']);
        $email = mysqli_real_escape_string($conn, $user['email']);
        $password = mysqli_real_escape_string($conn, $user['password']);
        $is_admin = (int)$user['is_admin'];
        
        $insert_sql = "INSERT INTO users (name, email, password, is_admin) 
                       VALUES ('$name', '$email', '$password', $is_admin)";
        
        if (mysqli_query($conn, $insert_sql)) {
            $restored++;
        }
    }
    
    echo "<p>Successfully restored $restored users.</p>";
}

// Step 5: Create default admin if none exists
$admin_check = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE is_admin = 1");
$admin_row = mysqli_fetch_assoc($admin_check);

if ($admin_row['count'] == 0) {
    echo "<p>No admin users found. Creating default admin...</p>";
    
    $admin_name = "Admin";
    $admin_email = "admin@example.com";
    $admin_password = password_hash("admin123", PASSWORD_DEFAULT);
    
    $admin_sql = "INSERT INTO users (name, email, password, is_admin) 
                  VALUES ('$admin_name', '$admin_email', '$admin_password', 1)";
    
    if (mysqli_query($conn, $admin_sql)) {
        echo "<p>Created default admin user:</p>";
        echo "<ul>";
        echo "<li>Email: admin@example.com</li>";
        echo "<li>Password: admin123</li>";
        echo "</ul>";
    } else {
        echo "<p>Error creating admin: " . mysqli_error($conn) . "</p>";
    }
}

// Step 6: Verify the table structure
echo "<h2>Verification</h2>";

$describe_result = mysqli_query($conn, "DESCRIBE users");
if ($describe_result) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($row = mysqli_fetch_assoc($describe_result)) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "<td>{$row['Extra']}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
}

// Step 7: Show user count
$count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
$count_row = mysqli_fetch_assoc($count_result);
echo "<p>Total users in rebuilt table: {$count_row['total']}</p>";

// Step 8: Show navigation links
echo "<div style='margin-top: 20px;'>";
echo "<a href='direct_signup.php' style='padding: 10px 15px; background-color: #4CAF50; color: white; text-decoration: none; margin-right: 10px;'>Try Signup Now</a>";
echo "<a href='index.php' style='padding: 10px 15px; background-color: #2196F3; color: white; text-decoration: none;'>Back to Home</a>";
echo "</div>";
?> 