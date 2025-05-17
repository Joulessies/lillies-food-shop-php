<?php
// Directly connect to MySQL
$conn = mysqli_connect('localhost', 'root', '');
if (!$conn) {
    die('Could not connect to MySQL: ' . mysqli_connect_error());
}

// Use the correct database
if (!mysqli_select_db($conn, 'lillies_food_shop')) {
    die('Could not select database: ' . mysqli_error($conn));
}

echo "<h1>Emergency Fix for ID=0 Issue</h1>";

// Critical fix: Delete any record with ID=0
$delete_result = mysqli_query($conn, "DELETE FROM users WHERE id = 0");
if ($delete_result) {
    echo "<p style='color: green;'>Successfully removed any records with ID=0.</p>";
    
    // Set AUTO_INCREMENT to 1
    $set_auto_increment = mysqli_query($conn, "ALTER TABLE users AUTO_INCREMENT = 1");
    if ($set_auto_increment) {
        echo "<p style='color: green;'>Successfully set AUTO_INCREMENT to 1.</p>";
    } else {
        echo "<p style='color: red;'>Failed to set AUTO_INCREMENT: " . mysqli_error($conn) . "</p>";
    }
} else {
    echo "<p style='color: red;'>Error removing ID=0 records: " . mysqli_error($conn) . "</p>";
}

// Try to fix the issue if the table structure is wrong
$describe_result = mysqli_query($conn, "DESCRIBE users");
$id_column_needs_fix = false;
if ($describe_result) {
    while ($row = mysqli_fetch_assoc($describe_result)) {
        if ($row['Field'] == 'id') {
            if (strpos($row['Extra'], 'auto_increment') === false) {
                $id_column_needs_fix = true;
            }
        }
    }
}

if ($id_column_needs_fix) {
    echo "<p style='color: orange;'>ID column does not have AUTO_INCREMENT. Attempting to fix...</p>";
    
    $fix_column = mysqli_query($conn, "ALTER TABLE users MODIFY id INT NOT NULL PRIMARY KEY AUTO_INCREMENT");
    if ($fix_column) {
        echo "<p style='color: green;'>Successfully fixed ID column to use AUTO_INCREMENT.</p>";
    } else {
        echo "<p style='color: red;'>Failed to fix ID column: " . mysqli_error($conn) . "</p>";
    }
}

echo "<p>If you continue to experience issues, please try <a href='rebuild_users_table.php'>rebuilding the entire users table</a>.</p>";
echo "<p><a href='signup.php'>Return to signup</a></p>";
?> 