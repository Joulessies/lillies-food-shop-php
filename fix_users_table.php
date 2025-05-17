<?php
// Database connection
require_once 'config/db_connect.php';

echo "<h1>Fixing Users Table</h1>";

// Check if there's a user with ID=0
$result = mysqli_query($conn, "SELECT * FROM users WHERE id = 0");
if (!$result) {
    die("Error checking for ID=0: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) > 0) {
    echo "<p>Found record with ID=0. Attempting to fix...</p>";
    
    // Get the next available ID
    $id_result = mysqli_query($conn, "SELECT MAX(id) + 1 as next_id FROM users WHERE id > 0");
    $id_row = mysqli_fetch_assoc($id_result);
    $next_id = $id_row['next_id'];
    
    if (!$next_id) $next_id = 1; // If there are no other users, start with 1
    
    // Update the record with ID=0 to the next available ID
    $update = mysqli_query($conn, "UPDATE users SET id = $next_id WHERE id = 0");
    
    if ($update) {
        echo "<p>Successfully updated record with ID=0 to ID=$next_id.</p>";
    } else {
        echo "<p>Error updating record: " . mysqli_error($conn) . "</p>";
    }
} else {
    echo "<p>No records found with ID=0.</p>";
}

// Check if AUTO_INCREMENT is set on the id column
$result = mysqli_query($conn, "SHOW COLUMNS FROM users WHERE Field='id'");
$row = mysqli_fetch_assoc($result);
if ($row && strpos($row['Extra'], 'auto_increment') === false) {
    echo "<p>Auto increment not set on id column. Attempting to add it...</p>";
    
    // Add AUTO_INCREMENT to id column
    $alter = mysqli_query($conn, "ALTER TABLE users MODIFY id INT NOT NULL PRIMARY KEY AUTO_INCREMENT");
    
    if ($alter) {
        echo "<p>Successfully added AUTO_INCREMENT to id column.</p>";
    } else {
        echo "<p>Error setting AUTO_INCREMENT: " . mysqli_error($conn) . "</p>";
    }
} else {
    echo "<p>AUTO_INCREMENT is correctly set on id column.</p>";
}

echo "<p><a href='admin/user_management.php'>Return to user management</a></p>";
?> 