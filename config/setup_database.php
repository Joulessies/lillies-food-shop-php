<?php
// Database setup script
require_once 'db_connect.php';

// Create users table if it doesn't exist
$users_table = "
CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!mysqli_query($conn, $users_table)) {
    die("Error creating users table: " . mysqli_error($conn));
}

// Check if admin user exists
$check_admin = mysqli_query($conn, "SELECT * FROM users WHERE is_admin = 1 LIMIT 1");
if (mysqli_num_rows($check_admin) == 0) {
    // Create default admin user
    $admin_password = password_hash("admin123", PASSWORD_DEFAULT);
    $insert_admin = "INSERT INTO users (name, email, password, is_admin) 
                     VALUES ('Admin', 'admin@example.com', '$admin_password', 1)";
    
    if (!mysqli_query($conn, $insert_admin)) {
        die("Error creating admin user: " . mysqli_error($conn));
    }
    
    echo "Default admin created with credentials: admin@example.com / admin123<br>";
}

// Create menu_items table if it doesn't exist
$menu_table = "
CREATE TABLE IF NOT EXISTS menu_items (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    category VARCHAR(50),
    is_available TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!mysqli_query($conn, $menu_table)) {
    die("Error creating menu_items table: " . mysqli_error($conn));
}

// Create orders table if it doesn't exist
$orders_table = "
CREATE TABLE IF NOT EXISTS orders (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

if (!mysqli_query($conn, $orders_table)) {
    die("Error creating orders table: " . mysqli_error($conn));
}

// Create order_items table if it doesn't exist
$order_items_table = "
CREATE TABLE IF NOT EXISTS order_items (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id)
)";

if (!mysqli_query($conn, $order_items_table)) {
    die("Error creating order_items table: " . mysqli_error($conn));
}

echo "Database setup completed successfully!";
?> 