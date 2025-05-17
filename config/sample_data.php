<?php
// Include database connection
$conn = require_once 'database.php';

// Insert sample food categories
$categories = [
    ['name' => 'Burgers', 'description' => 'Delicious beef, chicken, and veggie burgers', 'image' => 'assets/images/category_burgers.jpg'],
    ['name' => 'Pizzas', 'description' => 'Fresh, handmade pizzas with various toppings', 'image' => 'assets/images/category_pizzas.jpg'],
    ['name' => 'Sides', 'description' => 'Perfect sides to complement your meal', 'image' => 'assets/images/category_sides.jpg'],
    ['name' => 'Drinks', 'description' => 'Refreshing beverages', 'image' => 'assets/images/category_drinks.jpg'],
    ['name' => 'Desserts', 'description' => 'Sweet treats to finish your meal', 'image' => 'assets/images/category_desserts.jpg']
];

foreach ($categories as $category) {
    $name = mysqli_real_escape_string($conn, $category['name']);
    $description = mysqli_real_escape_string($conn, $category['description']);
    $image = mysqli_real_escape_string($conn, $category['image']);
    
    $sql = "INSERT INTO food_categories (name, description, image) 
            SELECT '$name', '$description', '$image' 
            FROM dual 
            WHERE NOT EXISTS (
                SELECT 1 FROM food_categories WHERE name = '$name'
            ) LIMIT 1";
    
    mysqli_query($conn, $sql);
}

// Get category IDs
$burger_cat_id = mysqli_query($conn, "SELECT id FROM food_categories WHERE name = 'Burgers'")->fetch_assoc()['id'];
$pizza_cat_id = mysqli_query($conn, "SELECT id FROM food_categories WHERE name = 'Pizzas'")->fetch_assoc()['id'];
$sides_cat_id = mysqli_query($conn, "SELECT id FROM food_categories WHERE name = 'Sides'")->fetch_assoc()['id'];
$drinks_cat_id = mysqli_query($conn, "SELECT id FROM food_categories WHERE name = 'Drinks'")->fetch_assoc()['id'];
$dessert_cat_id = mysqli_query($conn, "SELECT id FROM food_categories WHERE name = 'Desserts'")->fetch_assoc()['id'];

// Insert sample menu items
$menu_items = [
    // Burgers
    [
        'category_id' => $burger_cat_id,
        'name' => 'Classic Cheeseburger',
        'description' => 'Juicy beef patty with melted cheese, lettuce, tomato, and our special sauce',
        'price' => 149.99,
        'image' => 'assets/images/cheeseburger.jpg',
        'is_featured' => 1
    ],
    [
        'category_id' => $burger_cat_id,
        'name' => 'Crispy Chicken Burger',
        'description' => 'Crispy fried chicken with fresh lettuce, mayo, and pickles',
        'price' => 139.99,
        'image' => 'assets/images/chickenburger.jpg',
        'is_featured' => 0
    ],
    
    // Pizzas
    [
        'category_id' => $pizza_cat_id,
        'name' => 'Margherita Pizza',
        'description' => 'Classic pizza with tomato sauce, mozzarella, and fresh basil',
        'price' => 199.99,
        'image' => 'assets/images/margherita.jpg',
        'is_featured' => 1
    ],
    [
        'category_id' => $pizza_cat_id,
        'name' => 'Pepperoni Pizza',
        'description' => 'Tomato sauce, mozzarella, and pepperoni slices',
        'price' => 249.99,
        'image' => 'assets/images/pepperoni.jpg',
        'is_featured' => 0
    ],
    
    // Sides
    [
        'category_id' => $sides_cat_id,
        'name' => 'French Fries',
        'description' => 'Crispy golden fries seasoned with salt',
        'price' => 79.99,
        'image' => 'assets/images/fries.jpg',
        'is_featured' => 1
    ],
    [
        'category_id' => $sides_cat_id,
        'name' => 'Onion Rings',
        'description' => 'Crispy battered onion rings',
        'price' => 89.99,
        'image' => 'assets/images/onionrings.jpg',
        'is_featured' => 0
    ],
    
    // Drinks
    [
        'category_id' => $drinks_cat_id,
        'name' => 'Soft Drink',
        'description' => 'Choice of cola, lemon-lime, or orange soda',
        'price' => 49.99,
        'image' => 'assets/images/softdrink.jpg',
        'is_featured' => 0
    ],
    [
        'category_id' => $drinks_cat_id,
        'name' => 'Iced Tea',
        'description' => 'Refreshing iced tea with lemon',
        'price' => 59.99,
        'image' => 'assets/images/icedtea.jpg',
        'is_featured' => 0
    ],
    
    // Desserts
    [
        'category_id' => $dessert_cat_id,
        'name' => 'Chocolate Cake',
        'description' => 'Rich chocolate cake with fudge frosting',
        'price' => 99.99,
        'image' => 'assets/images/chocolatecake.jpg',
        'is_featured' => 1
    ],
    [
        'category_id' => $dessert_cat_id,
        'name' => 'Ice Cream Sundae',
        'description' => 'Vanilla ice cream with chocolate sauce, whipped cream, and a cherry',
        'price' => 89.99,
        'image' => 'assets/images/icecreamsundae.jpg',
        'is_featured' => 0
    ]
];

foreach ($menu_items as $item) {
    $category_id = (int)$item['category_id'];
    $name = mysqli_real_escape_string($conn, $item['name']);
    $description = mysqli_real_escape_string($conn, $item['description']);
    $price = (float)$item['price'];
    $image = mysqli_real_escape_string($conn, $item['image']);
    $is_featured = (int)$item['is_featured'];
    
    $sql = "INSERT INTO menu_items (category_id, name, description, price, image, is_featured) 
            SELECT $category_id, '$name', '$description', $price, '$image', $is_featured 
            FROM dual 
            WHERE NOT EXISTS (
                SELECT 1 FROM menu_items WHERE name = '$name'
            ) LIMIT 1";
    
    mysqli_query($conn, $sql);
}

// Insert a sample admin user (password: admin123)
$admin_password = password_hash('admin123', PASSWORD_DEFAULT);
$admin_sql = "INSERT INTO users (name, email, password) 
              SELECT 'Admin User', 'admin@lilliesfoodshop.com', '$admin_password' 
              FROM dual 
              WHERE NOT EXISTS (
                  SELECT 1 FROM users WHERE email = 'admin@lilliesfoodshop.com'
              ) LIMIT 1";

if (mysqli_query($conn, $admin_sql)) {
    echo "Sample data inserted successfully!";
} else {
    echo "Error inserting sample data: " . mysqli_error($conn);
}

// Close connection
mysqli_close($conn);
?> 