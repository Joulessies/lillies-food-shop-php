<?php
// Script to check and fix menu_items table issues

// Include database connection
require_once 'config/db_connect.php';

echo "<h1>Menu Items Diagnostic Tool</h1>";

// 1. Check if the menu_items table exists
$table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'menu_items'");
if (mysqli_num_rows($table_exists) == 0) {
    echo "<p style='color:red'>The menu_items table does not exist!</p>";
    
    // Create the table
    $create_table_sql = "CREATE TABLE menu_items (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        image VARCHAR(255),
        category_id INT,
        is_featured TINYINT(1) DEFAULT 0,
        is_available TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (mysqli_query($conn, $create_table_sql)) {
        echo "<p style='color:green'>menu_items table created successfully!</p>";
    } else {
        echo "<p style='color:red'>Error creating menu_items table: " . mysqli_error($conn) . "</p>";
    }
} else {
    echo "<p style='color:green'>menu_items table exists.</p>";
    
    // Check table structure
    echo "<h2>Table Structure:</h2>";
    $result = mysqli_query($conn, "DESCRIBE menu_items");
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    $has_category_id = false;
    $has_is_featured = false;
    $has_is_available = false;
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
        
        if ($row['Field'] == 'category_id') $has_category_id = true;
        if ($row['Field'] == 'is_featured') $has_is_featured = true;
        if ($row['Field'] == 'is_available') $has_is_available = true;
    }
    
    echo "</table>";
    
    // Check if required columns exist and add them if not
    if (!$has_category_id) {
        echo "<p style='color:orange'>category_id column is missing. Adding it now...</p>";
        if (mysqli_query($conn, "ALTER TABLE menu_items ADD COLUMN category_id INT DEFAULT NULL")) {
            echo "<p style='color:green'>category_id column added successfully!</p>";
        } else {
            echo "<p style='color:red'>Error adding category_id column: " . mysqli_error($conn) . "</p>";
        }
    }
    
    if (!$has_is_featured) {
        echo "<p style='color:orange'>is_featured column is missing. Adding it now...</p>";
        if (mysqli_query($conn, "ALTER TABLE menu_items ADD COLUMN is_featured TINYINT(1) DEFAULT 0")) {
            echo "<p style='color:green'>is_featured column added successfully!</p>";
        } else {
            echo "<p style='color:red'>Error adding is_featured column: " . mysqli_error($conn) . "</p>";
        }
    }
    
    if (!$has_is_available) {
        echo "<p style='color:orange'>is_available column is missing. Adding it now...</p>";
        if (mysqli_query($conn, "ALTER TABLE menu_items ADD COLUMN is_available TINYINT(1) DEFAULT 1")) {
            echo "<p style='color:green'>is_available column added successfully!</p>";
        } else {
            echo "<p style='color:red'>Error adding is_available column: " . mysqli_error($conn) . "</p>";
        }
    }
}

// 2. Check if there are any menu items
$count_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM menu_items");
$count = mysqli_fetch_assoc($count_result)['count'];

echo "<h2>Menu Items Count: $count</h2>";

if ($count == 0) {
    echo "<p style='color:orange'>No menu items found. Adding some sample items...</p>";
    
    // First make sure food_categories table exists and has categories
    $categories_exist = mysqli_query($conn, "SHOW TABLES LIKE 'food_categories'");
    if (mysqli_num_rows($categories_exist) == 0) {
        echo "<p style='color:orange'>food_categories table does not exist. Creating it...</p>";
        
        $create_categories_sql = "CREATE TABLE food_categories (
            id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if (mysqli_query($conn, $create_categories_sql)) {
            echo "<p style='color:green'>food_categories table created successfully!</p>";
            
            // Add default categories
            $insert_categories_sql = "INSERT INTO food_categories (name, description) VALUES 
                ('Burgers', 'Delicious burger selections'),
                ('Sides', 'Perfect complement to your meal'),
                ('Drinks', 'Refreshing beverages')";
                
            if (mysqli_query($conn, $insert_categories_sql)) {
                echo "<p style='color:green'>Default categories added!</p>";
            } else {
                echo "<p style='color:red'>Error adding default categories: " . mysqli_error($conn) . "</p>";
            }
        } else {
            echo "<p style='color:red'>Error creating food_categories table: " . mysqli_error($conn) . "</p>";
        }
    }
    
    // Get category IDs
    $categories = [];
    $cat_result = mysqli_query($conn, "SELECT id, name FROM food_categories");
    while ($row = mysqli_fetch_assoc($cat_result)) {
        $categories[$row['name']] = $row['id'];
    }
    
    if (count($categories) > 0) {
        // Add sample menu items
        $burger_id = isset($categories['Burgers']) ? $categories['Burgers'] : 1;
        $sides_id = isset($categories['Sides']) ? $categories['Sides'] : 2;
        $drinks_id = isset($categories['Drinks']) ? $categories['Drinks'] : 3;
        
        $sample_items = [
            [
                'name' => 'Double Cheese Burger',
                'description' => 'Juicy beef patty with double cheese and all the fixings',
                'price' => 199.00,
                'category_id' => $burger_id,
                'is_featured' => 1,
                'is_available' => 1
            ],
            [
                'name' => 'Classic Fries',
                'description' => 'Crispy golden french fries',
                'price' => 99.00,
                'category_id' => $sides_id,
                'is_featured' => 1,
                'is_available' => 1
            ],
            [
                'name' => 'Iced Tea',
                'description' => 'Refreshing iced tea',
                'price' => 69.00,
                'category_id' => $drinks_id,
                'is_featured' => 0,
                'is_available' => 1
            ]
        ];
        
        foreach ($sample_items as $item) {
            $insert_sql = "INSERT INTO menu_items (name, description, price, category_id, is_featured, is_available) 
                           VALUES (?, ?, ?, ?, ?, ?)";
            
            if ($stmt = mysqli_prepare($conn, $insert_sql)) {
                mysqli_stmt_bind_param($stmt, "ssdidd", $item['name'], $item['description'], $item['price'], $item['category_id'], $item['is_featured'], $item['is_available']);
                
                if (mysqli_stmt_execute($stmt)) {
                    echo "<p style='color:green'>Added sample item: " . $item['name'] . "</p>";
                } else {
                    echo "<p style='color:red'>Error adding sample item " . $item['name'] . ": " . mysqli_error($conn) . "</p>";
                }
                
                mysqli_stmt_close($stmt);
            }
        }
    } else {
        echo "<p style='color:red'>No categories found. Cannot add sample items.</p>";
    }
} else {
    // Show items
    echo "<h2>Current Menu Items:</h2>";
    $items_result = mysqli_query($conn, "SELECT m.*, c.name as category_name 
                                          FROM menu_items m 
                                          LEFT JOIN food_categories c ON m.category_id = c.id 
                                          ORDER BY m.id");
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Price</th><th>Category</th><th>Featured</th><th>Available</th></tr>";
    
    while ($item = mysqli_fetch_assoc($items_result)) {
        echo "<tr>";
        echo "<td>" . $item['id'] . "</td>";
        echo "<td>" . $item['name'] . "</td>";
        echo "<td>₱" . number_format($item['price'], 2) . "</td>";
        echo "<td>" . ($item['category_name'] ?? "None") . "</td>";
        echo "<td>" . ($item['is_featured'] ? "Yes" : "No") . "</td>";
        echo "<td>" . ($item['is_available'] ? "Yes" : "No") . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
}

// 3. Check if the menu page is properly fetching items
echo "<h2>Menu Page Check</h2>";
echo "<p>To make the menu page display these items, ensure the following:</p>";
echo "<ol>";
echo "<li>The menu.php file includes a database connection.</li>";
echo "<li>The menu.php file queries the menu_items table.</li>";
echo "<li>Items have 'is_available' set to 1.</li>";
echo "<li>Items have a valid category_id.</li>";
echo "</ol>";

echo "<p>View your <a href='Pages/menu.php' style='color:blue;'>Menu Page</a> to see if the items appear.</p>";
echo "<p>If they still don't appear, check the browser console for JavaScript errors.</p>";

// Close connection
mysqli_close($conn);
?> 