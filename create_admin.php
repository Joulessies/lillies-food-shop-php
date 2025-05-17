<?php
// Simple script to create an admin user in a new database

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lillies_food_shop"; // Use your new database name here

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "<h1>Creating Admin User</h1>";

// Create a hashed password
$hashed_password = password_hash("admin123", PASSWORD_DEFAULT);

// SQL to create admin user
$sql = "INSERT INTO users (name, email, password, is_admin) 
        VALUES ('Admin User', 'admin@lilliesfoodshop.com', '$hashed_password', 1)";

if (mysqli_query($conn, $sql)) {
    echo "<p style='color:green'>Admin user created successfully!</p>";
    echo "<p>Email: admin@lilliesfoodshop.com<br>Password: admin123</p>";
} else {