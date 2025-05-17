<?php
// Include database connection
require_once 'config/db_connect.php';

// Get all food categories
$categories = [];
$sql = "SELECT * FROM food_categories ORDER BY name";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
}

// Get all menu items
$menu_items = [];
$sql = "SELECT m.*, c.name as category_name 
        FROM menu_items m 
        LEFT JOIN food_categories c ON m.category_id = c.id 
        ORDER BY c.name, m.name";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Group by category
        $menu_items[$row['category_id']][] = $row;
    }
}

// Get featured items
$featured_items = [];
$sql = "SELECT m.*, c.name as category_name 
        FROM menu_items m 
        LEFT JOIN food_categories c ON m.category_id = c.id 
        WHERE m.is_featured = 1 
        ORDER BY RAND() 
        LIMIT 4";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $featured_items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu | Lillies Food Shop</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
    <style>
        .menu-section {
            padding: 5rem 0 3rem;
        }
        
        .menu-category {
            margin-bottom: 3rem;
        }
        
        .menu-category h2 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .menu-category h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 4px;
            background-color: var(--secondary-color);
            border-radius: 2px;
        }
        
        .menu-item {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .menu-item-image {
            height: 200px;
            overflow: hidden;
        }
        
        .menu-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .menu-item:hover .menu-item-image img {
            transform: scale(1.1);
        }
        
        .menu-item-content {
            padding: 1.25rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .menu-item-title {
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }
        
        .menu-item-desc {
            color: #666;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            flex-grow: 1;
        }
        
        .menu-item-price {
            font-weight: 700;
            color: var(--primary-color);
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }
        
        .category-badge {
            display: inline-block;
            background-color: var(--light-color);
            color: var(--dark-color);
            padding: 0.25rem 0.75rem;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .add-to-cart-btn {
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .add-to-cart-btn:hover {
            transform: scale(1.05);
        }
        
        .featured-section {
            background-color: var(--light-color);
            padding: 3rem 0;
            margin-bottom: 3rem;
        }
        
        .featured-title {
            font-weight: 800;
            color: var(--dark-color);
            text-align: center;
            margin-bottom: 2rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .featured-title span {
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <?php include 'Layout/Navigation/navigation.php'; ?>

    <main class="menu-section">
        <div class="container">
            <h1 class="text-center mb-5">Our Menu</h1>
            
            <!-- Featured Items Section -->
            <?php if (!empty($featured_items)): ?>
                <div class="featured-section">
                    <div class="container">
                        <h2 class="featured-title">Featured <span>Dishes</span></h2>
                        <div class="row g-4">
                            <?php foreach ($featured_items as $item): ?>
                                <div class="col-md-6 col-lg-3">
                                    <div class="menu-item">
                                        <div class="menu-item-image">
                                            <?php if ($item['image']): ?>
                                                <img src="<?= $item['image'] ?>" alt="<?= $item['name'] ?>">
                                            <?php else: ?>
                                                <img src="assets/images/food-placeholder.jpg" alt="<?= $item['name'] ?>">
                                            <?php endif; ?>
                                        </div>
                                        <div class="menu-item-content">
                                            <span class="category-badge"><?= $item['category_name'] ?></span>
                                            <h3 class="menu-item-title"><?= $item['name'] ?></h3>
                                            <p class="menu-item-desc"><?= $item['description'] ? $item['description'] : 'Delicious ' . $item['name'] . ' freshly prepared for you.' ?></p>
                                            <div class="menu-item-price">₱<?= number_format($item['price'], 2) ?></div>
                                            <button class="btn btn-primary add-to-cart-btn w-100" 
                                                data-id="<?= $item['id'] ?>" 
                                                data-name="<?= $item['name'] ?>" 
                                                data-price="<?= $item['price'] ?>"
                                                data-image="<?= $item['image'] ? $item['image'] : 'assets/images/food-placeholder.jpg' ?>">
                                                <i class="bi bi-cart-plus"></i> Add to Cart
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Categories and Menu Items -->
            <?php foreach ($categories as $category): ?>
                <?php if (isset($menu_items[$category['id']])): ?>
                    <section class="menu-category" id="category-<?= $category['id'] ?>">
                        <h2><?= $category['name'] ?></h2>
                        <div class="row g-4">
                            <?php foreach ($menu_items[$category['id']] as $item): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="menu-item">
                                        <div class="menu-item-image">
                                            <?php if ($item['image']): ?>
                                                <img src="<?= $item['image'] ?>" alt="<?= $item['name'] ?>">
                                            <?php else: ?>
                                                <img src="assets/images/food-placeholder.jpg" alt="<?= $item['name'] ?>">
                                            <?php endif; ?>
                                        </div>
                                        <div class="menu-item-content">
                                            <h3 class="menu-item-title"><?= $item['name'] ?></h3>
                                            <p class="menu-item-desc"><?= $item['description'] ? $item['description'] : 'Delicious ' . $item['name'] . ' freshly prepared for you.' ?></p>
                                            <div class="menu-item-price">₱<?= number_format($item['price'], 2) ?></div>
                                            <button class="btn btn-primary add-to-cart-btn w-100" 
                                                data-id="<?= $item['id'] ?>" 
                                                data-name="<?= $item['name'] ?>" 
                                                data-price="<?= $item['price'] ?>"
                                                data-image="<?= $item['image'] ? $item['image'] : 'assets/images/food-placeholder.jpg' ?>">
                                                <i class="bi bi-cart-plus"></i> Add to Cart
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            <?php endforeach; ?>
            
            <?php if (empty($menu_items)): ?>
                <div class="text-center p-5">
                    <i class="bi bi-emoji-frown display-1 text-muted mb-3"></i>
                    <h3>No menu items found</h3>
                    <p>We're currently updating our menu. Please check back soon!</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'Layout/Footer/footer.php'; ?>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Add to Cart Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize cart in localStorage if it doesn't exist
            if (!localStorage.getItem('cart')) {
                localStorage.setItem('cart', JSON.stringify([]));
            }
            
            // Add event listeners to all add to cart buttons
            document.querySelectorAll('.add-to-cart-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const price = parseFloat(this.getAttribute('data-price'));
                    const image = this.getAttribute('data-image');
                    
                    addToCart(id, name, price, image);
                    
                    // Show a small toast or notification
                    alert(name + ' added to cart!');
                    // Update cart count
                    updateCartCount();
                });
            });
            
            // Function to add item to cart
            function addToCart(id, name, price, image) {
                const cart = JSON.parse(localStorage.getItem('cart')) || [];
                
                // Check if item already exists in cart
                const existingItemIndex = cart.findIndex(item => item.id === id);
                
                if (existingItemIndex > -1) {
                    // Item exists, update quantity
                    cart[existingItemIndex].quantity += 1;
                } else {
                    // Item doesn't exist, add new item
                    cart.push({
                        id: id,
                        name: name,
                        price: price,
                        image: image,
                        quantity: 1
                    });
                }
                
                // Save updated cart to localStorage
                localStorage.setItem('cart', JSON.stringify(cart));
            }
            
            // Function to update cart count in navbar
            function updateCartCount() {
                const cart = JSON.parse(localStorage.getItem('cart')) || [];
                const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
                document.querySelector('.cart-count').textContent = totalItems;
            }
            
            // Initial cart count update
            updateCartCount();
        });
    </script>
</body>
</html> 