<?php
// Include database connection
require_once 'config/db_connect.php';

// Create menu_items table
$sql = "CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    category VARCHAR(50),
    is_available TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sql)) {
    echo "menu_items table created successfully.<br>";
} else {
    echo "Error creating menu_items table: " . mysqli_error($conn) . "<br>";
}

// Create orders table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sql)) {
    echo "orders table created successfully.<br>";
} else {
    echo "Error creating orders table: " . mysqli_error($conn) . "<br>";
}

// Insert sample menu items
$sample_items = [
    [
        'name' => 'Spicy Chicken Burger',
        'description' => 'Crispy chicken fillet with our special spicy sauce and fresh vegetables.',
        'price' => 199.50,
        'image' => 'assets/images/menu/burger1.jpg',
        'category' => 'Burgers'
    ],
    [
        'name' => 'Classic Beef Burger',
        'description' => '100% pure beef patty with cheese, lettuce, tomato, and our secret sauce.',
        'price' => 229.00,
        'image' => 'assets/images/menu/burger2.jpg',
        'category' => 'Burgers'
    ],
    [
        'name' => 'Veggie Supreme Pizza',
        'description' => 'Fresh vegetables on our house-made tomato sauce with premium mozzarella.',
        'price' => 349.00,
        'image' => 'assets/images/menu/pizza1.jpg',
        'category' => 'Pizza'
    ],
    [
        'name' => 'Chocolate Milkshake',
        'description' => 'Rich chocolate ice cream blended with milk and topped with whipped cream.',
        'price' => 129.00,
        'image' => 'assets/images/menu/shake1.jpg',
        'category' => 'Drinks'
    ]
];

// Check if there are already menu items to avoid duplicates
$check_sql = "SELECT COUNT(*) as count FROM menu_items";
$result = mysqli_query($conn, $check_sql);
$row = mysqli_fetch_assoc($result);

if ($row['count'] == 0) {
    foreach ($sample_items as $item) {
        $insert_sql = "INSERT INTO menu_items (name, description, price, image, category) 
                      VALUES ('{$item['name']}', '{$item['description']}', {$item['price']}, '{$item['image']}', '{$item['category']}')";
        
        if (mysqli_query($conn, $insert_sql)) {
            echo "Added menu item: {$item['name']}<br>";
        } else {
            echo "Error adding menu item: " . mysqli_error($conn) . "<br>";
        }
    }
} else {
    echo "Menu items already exist, skipping sample data.<br>";
}

// Close the connection
mysqli_close($conn);
echo "<br><a href='admin/dashboard.php'>Go to admin dashboard</a>";
?> 