<?php
// Include database configuration
require_once 'db_connect.php';

// SQL to create orders table
$orders_table = "CREATE TABLE IF NOT EXISTS orders (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    shipping_address TEXT NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

// SQL to create order_items table
$order_items_table = "CREATE TABLE IF NOT EXISTS order_items (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id)
)";

// Execute the SQL statements
if (mysqli_query($conn, $orders_table)) {
    echo "Orders table created successfully.<br>";
} else {
    echo "Error creating orders table: " . mysqli_error($conn) . "<br>";
}

if (mysqli_query($conn, $order_items_table)) {
    echo "Order items table created successfully.<br>";
} else {
    echo "Error creating order items table: " . mysqli_error($conn) . "<br>";
}

// Insert sample orders if needed
$check_orders = mysqli_query($conn, "SELECT * FROM orders LIMIT 1");
if (mysqli_num_rows($check_orders) == 0) {
    // Insert a sample order
    $sample_order = "INSERT INTO orders (user_id, total_amount, status, shipping_address, phone_number, payment_method, notes)
    VALUES (1, 36.96, 'completed', '123 Main St, Anytown, USA', '555-123-4567', 'Credit Card', 'Please deliver to the side door')";
    
    if (mysqli_query($conn, $sample_order)) {
        echo "Sample order added successfully.<br>";
        
        // Get the inserted order ID
        $order_id = mysqli_insert_id($conn);
        
        // Insert sample order items
        $sample_items = [
            "INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES ($order_id, 1, 'Classic Burger', 2, 12.99)",
            "INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES ($order_id, 2, 'French Fries', 1, 4.99)",
            "INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES ($order_id, 3, 'Chocolate Milkshake', 1, 5.99)"
        ];
        
        foreach ($sample_items as $query) {
            if (mysqli_query($conn, $query)) {
                echo "Sample order item added successfully.<br>";
            } else {
                echo "Error adding sample order item: " . mysqli_error($conn) . "<br>";
            }
        }
    } else {
        echo "Error adding sample order: " . mysqli_error($conn) . "<br>";
    }
}

// Close connection
mysqli_close($conn);

echo "<p>Database setup completed. <a href='/index.php'>Return to home page</a></p>";
?> 