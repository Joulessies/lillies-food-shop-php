<?php
// Script to verify database and users table setup

require_once 'config/db_connect.php';

echo "<h1>Database Verification</h1>";

// 1. Check connection
echo "<h2>1. Connection Test</h2>";
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    echo "<p style='color:green'>Database connection successful!</p>";
    $db_name_result = mysqli_query($conn, "SELECT DATABASE()");
    $db_name = mysqli_fetch_row($db_name_result)[0];
    echo "<p>Connected to database: " . $db_name . "</p>";
}

// 2. Check table structure
echo "<h2>2. Table Structure</h2>";
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
if (mysqli_num_rows($table_check) == 0) {
    echo "<p style='color:red'>Users table does not exist!</p>";
    
    echo "<h3>Creating users table...</h3>";
    $create_table_sql = "CREATE TABLE users (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        is_admin TINYINT(1) DEFAULT 0
    )";
    
    if (mysqli_query($conn, $create_table_sql)) {
        echo "<p style='color:green'>Users table created successfully!</p>";
    } else {
        echo "<p style='color:red'>Error creating users table: " . mysqli_error($conn) . "</p>";
    }
} else {
    echo "<p style='color:green'>Users table exists.</p>";
    
    $result = mysqli_query($conn, "DESCRIBE users");
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
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

// 3. Check AUTO_INCREMENT value
echo "<h2>3. AUTO_INCREMENT Setup</h2>";
$auto_inc_result = mysqli_query($conn, "SHOW TABLE STATUS LIKE 'users'");
$auto_inc_info = mysqli_fetch_assoc($auto_inc_result);
echo "<p>Current AUTO_INCREMENT value: " . $auto_inc_info['Auto_increment'] . "</p>";

// 4. List existing users
echo "<h2>4. Existing Users</h2>";
$users_result = mysqli_query($conn, "SELECT id, name, email, is_admin FROM users ORDER BY id");
if (mysqli_num_rows($users_result) > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Admin?</th></tr>";
    while ($row = mysqli_fetch_assoc($users_result)) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['name']}</td>";
        echo "<td>{$row['email']}</td>";
        echo "<td>{$row['is_admin']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No users found in the database.</p>";
    
    // Offer to create an admin user
    echo "<p><a href='create_admin.php' style='color:blue;'>Create Admin User</a></p>";
}

// Close connection
mysqli_close($conn);
?> 