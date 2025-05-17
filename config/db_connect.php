<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration
$conn = require_once 'database.php';

// Function to close database connection
function closeConnection($conn) {
    mysqli_close($conn);
}

// Function to sanitize input data
function sanitize($conn, $data) {
    return mysqli_real_escape_string($conn, trim($data));
}
?> 