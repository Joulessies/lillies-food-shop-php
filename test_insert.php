<?php
// Simple test script to diagnose database insertion issues

// Include database connection
require_once 'config/db_connect.php';

echo "<h1>Database Test</h1>";

// 1. Check connection
echo "<h2>1. Connection Test</h2>";
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    echo "<p style='color:green'>Database connection successful!</p>";
}

// 2. Check table structure
echo "<h2>2. Table Structure</h2>";
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
if (mysqli_num_rows($table_check) == 0) {
    die("<p style='color:red'>Users table does not exist!</p>");
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

// 4. Try to insert a test user
echo "<h2>4. Test Insertion</h2>";

// First, set auto_increment to start at 1 if not already set
mysqli_query($conn, "ALTER TABLE users AUTO_INCREMENT = 1");

// Generate a unique email
$unique_email = "test_user_" . time() . "@example.com";
$password = password_hash("password123", PASSWORD_DEFAULT);

$insert_sql = "INSERT INTO users (name, email, password, is_admin) 
             VALUES ('Test User', '$unique_email', '$password', 0)";

if (mysqli_query($conn, $insert_sql)) {
    $new_id = mysqli_insert_id($conn);
    echo "<p style='color:green'>Test user inserted successfully! New ID: $new_id</p>";
} else {
    echo "<p style='color:red'>Error inserting test user: " . mysqli_error($conn) . "</p>";
}

// 5. Show all existing users
echo "<h2>5. Existing Users</h2>";
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
}

// 6. Additional diagnostics
echo "<h2>6. Additional Diagnostics</h2>";

// Check MySQL version
$version_result = mysqli_query($conn, "SELECT VERSION() as version");
$version = mysqli_fetch_assoc($version_result);
echo "<p>MySQL Version: " . $version['version'] . "</p>";

// Check if ID=0 exists
$zero_id_check = mysqli_query($conn, "SELECT * FROM users WHERE id = 0");
if (mysqli_num_rows($zero_id_check) > 0) {
    echo "<p style='color:red'>WARNING: Found a user with ID=0 which may cause issues. Consider deleting this record.</p>";
}

?> 