<?php
// Start session to track user login status
session_start();

// Database connection - this was missing
require_once $_SERVER['DOCUMENT_ROOT'] . '/lillies-food-shop/config/db_connect.php';

// Check if in Pages directory or root
$in_pages_dir = (strpos($_SERVER['PHP_SELF'], '/Pages/') !== false);

// Fetch categories from database
$categories = [];
$categories_query = "SELECT * FROM food_categories ORDER BY name";
$categories_result = mysqli_query($conn, $categories_query);
if ($categories_result) {
    while ($category = mysqli_fetch_assoc($categories_result)) {
        $categories[] = $category;
    }
}

// Fetch featured items from database
$featured_items = [];
$featured_query = "SELECT m.*, c.name as category_name 
                 FROM menu_items m 
                 LEFT JOIN food_categories c ON m.category_id = c.id 
                 WHERE m.is_featured = 1 AND m.is_available = 1 
                 ORDER BY m.name";
$featured_result = mysqli_query($conn, $featured_query);
if ($featured_result) {
    while ($item = mysqli_fetch_assoc($featured_result)) {
        $featured_items[] = $item;
    }
}

// Fetch all menu items by category for display
$menu_items_by_category = [];
foreach ($categories as $category) {
    $cat_id = $category['id'];
    $cat_name = $category['name'];
    
    $items_query = "SELECT * FROM menu_items WHERE category_id = $cat_id AND is_available = 1 ORDER BY name";
    $items_result = mysqli_query($conn, $items_query);
    
    if ($items_result && mysqli_num_rows($items_result) > 0) {
        while ($item = mysqli_fetch_assoc($items_result)) {
            $menu_items_by_category[$cat_name][] = $item;
        }
    }
}

// Include CSS files
echo '<link rel="stylesheet" href="../styles/styles.css">';
echo '<link rel="stylesheet" href="../styles/menu.css">';

// Include Bootstrap
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';
echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">';

// Include navigation with correct path
include_once $_SERVER['DOCUMENT_ROOT'] . '/lillies-food-shop/Layout/Navigation/navigation.php';
?>

<!-- Custom Modal Styles -->
<style>
  /* Modal styling */
  #productModal .modal-content {
    border-radius: 15px;
    overflow: hidden;
    border: none;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
  }
  
  #productModal .modal-header {
    background-color: #0078ff;
    color: white;
    border-bottom: none;
    padding: 15px 20px;
  }
  
  #productModal .modal-title {
    font-weight: 700;
    font-size: 1.3rem;
  }
  
  #productModal .btn-close {
    color: white;
    filter: brightness(0) invert(1);
    opacity: 0.8;
    transition: opacity 0.3s;
  }
  
  #productModal .btn-close:hover {
    opacity: 1;
  }
  
  #productModal .modal-body {
    padding: 20px;
  }
  
  #productModal .product-image {
    text-align: center;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }
  
  #productModal .product-image img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    transition: transform 0.5s ease;
  }
  
  #productModal .product-image:hover img {
    transform: scale(1.03);
  }
  
  #productModal .product-category {
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  }
  
  #productModal .product-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #333;
    margin-top: 10px;
    line-height: 1.3;
  }
  
  #productModal .product-description {
    color: #666;
    font-size: 1rem;
    line-height: 1.6;
    margin: 15px 0;
  }
  
  #productModal .product-price {
    color: #0078ff;
    font-size: 1.5rem;
    font-weight: 700;
  }
  
  #productModal .quantity-control {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 3px;
    border: 1px solid #eee;
  }
  
  #productModal .qty-btn {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    background-color: white;
    border: 1px solid #0078ff;
    font-size: 1rem;
    font-weight: bold;
    transition: all 0.2s;
    padding: 0;
  }
  
  #productModal .qty-btn:hover {
    background-color: #0078ff;
    color: white;
  }
  
  #productModal .qty-input {
    border: none;
    background-color: transparent;
    text-align: center;
    font-weight: 600;
    font-size: 1rem;
    width: 40px;
  }
  
  #productModal .modal-footer {
    border-top: 1px solid #f1f1f1;
    padding: 15px 20px;
  }
  
  #productModal .btn-secondary {
    background-color: #f2f2f2;
    color: #555;
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 600;
    transition: all 0.3s;
  }
  
  #productModal .btn-secondary:hover {
    background-color: #e5e5e5;
    color: #333;
  }
  
  #productModal .add-to-cart-btn {
    background-color: #0078ff;
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 600;
    transition: all 0.3s;
  }
  
  #productModal .add-to-cart-btn:hover {
    background-color: #0066cc;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  }
  
  /* Add cart icon animation */
  @keyframes cart-bounce {
    0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
    40% {transform: translateY(-10px);}
    60% {transform: translateY(-5px);}
  }
  
  .cart-bounce {
    animation: cart-bounce 0.5s ease;
    color: #0078ff;
  }
  
  /* Toast styling */
  .toast-container {
    z-index: 1070;
  }
  
  .toast {
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
  }
  
  .toast-body {
    font-weight: 500;
    padding: 12px 15px;
  }
  
  /* Recently viewed items styling */
  .recent-item {
    width: 180px;
    margin-right: 15px;
    margin-bottom: 15px;
    padding: 10px;
    border-radius: 8px;
    background-color: #f9f9f9;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: transform 0.2s, box-shadow 0.2s;
  }
  
  .recent-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
  }
  
  .recent-item img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 6px;
    margin-bottom: 8px;
  }
  
  .recent-item div {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
  }
  
  .recent-item div span:first-child {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 5px;
  }
  
  .recent-item div span:last-child {
    color: #0078ff;
    font-weight: 600;
    font-size: 0.9rem;
  }
</style>

<!-- Menu Page Content -->
<main class="menu-section">
  <h2>Our Menu</h2>

  <!-- Featured Items Section -->
  <section class="featured-items">
    <h3>Featured Items</h3>
    <div class="featured-grid">
      <?php if (!empty($featured_items)): ?>
        <?php foreach ($featured_items as $item): ?>
          <div class="featured-item" data-bs-toggle="modal" data-bs-target="#productModal" 
              data-name="<?= htmlspecialchars($item['name']) ?>" 
              data-price="<?= $item['price'] ?>" 
              data-category="<?= htmlspecialchars($item['category_name'] ?? 'Featured') ?>" 
              data-image="<?= !empty($item['image']) ? '../' . $item['image'] : 'https://images.unsplash.com/photo-1565299507177-b0ac66763828?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80' ?>"
              data-description="<?= htmlspecialchars($item['description']) ?>">
            <div class="featured-img">
              <img src="<?= !empty($item['image']) ? '../' . $item['image'] : 'https://images.unsplash.com/photo-1565299507177-b0ac66763828?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80' ?>" alt="<?= htmlspecialchars($item['name']) ?>">
              <span class="featured-badge">Featured</span>
            </div>
            <div class="featured-info">
              <h4><?= htmlspecialchars($item['name']) ?></h4>
              <p><?= htmlspecialchars($item['description']) ?></p>
              <div class="featured-price-action">
                <span class="featured-price">₱<?= number_format($item['price'], 2) ?></span>
                <button class="btn btn-sm btn-primary quick-add" 
                  data-name="<?= htmlspecialchars($item['name']) ?>" 
                  data-price="<?= $item['price'] ?>" 
                  data-image="<?= !empty($item['image']) ? '../' . $item['image'] : 'https://images.unsplash.com/photo-1565299507177-b0ac66763828?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80' ?>">
                  <i class="bi bi-plus-circle"></i> Quick Add
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <!-- Fallback if no featured items -->
        <div class="featured-item" data-bs-toggle="modal" data-bs-target="#productModal" 
            data-name="Double Cheese Deluxe" data-price="199" data-category="Featured" 
            data-image="https://images.unsplash.com/photo-1565299507177-b0ac66763828?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80"
            data-description="Our signature double-patty burger with two layers of melted cheese, crispy bacon, fresh lettuce, tomato, and our special sauce on a brioche bun.">
          <div class="featured-img">
            <img src="https://images.unsplash.com/photo-1565299507177-b0ac66763828?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80" alt="Double Cheese Deluxe">
            <span class="featured-badge">Most Popular</span>
          </div>
          <div class="featured-info">
            <h4>Double Cheese Deluxe</h4>
            <p>Double beef patty with double cheese and all the fixings</p>
            <div class="featured-price-action">
              <span class="featured-price">₱199</span>
              <button class="btn btn-sm btn-primary quick-add" data-name="Double Cheese Deluxe" data-price="199" 
                data-image="https://images.unsplash.com/photo-1565299507177-b0ac66763828?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80">
                <i class="bi bi-plus-circle"></i> Quick Add
              </button>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- Category Sections -->
  <?php if (!empty($menu_items_by_category)): ?>
    <?php foreach ($menu_items_by_category as $category_name => $items): ?>
      <section class="menu-category">
        <h3><?= htmlspecialchars($category_name) ?></h3>
        <ul>
          <?php foreach ($items as $item): ?>
            <li class="menu-item" data-bs-toggle="modal" data-bs-target="#productModal" 
                data-name="<?= htmlspecialchars($item['name']) ?>" 
                data-price="<?= $item['price'] ?>" 
                data-category="<?= htmlspecialchars($category_name) ?>" 
                data-image="<?= !empty($item['image']) ? '../' . $item['image'] : 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80' ?>"
                data-description="<?= htmlspecialchars($item['description']) ?>">
              <img src="<?= !empty($item['image']) ? '../' . $item['image'] : 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-1.2.1&auto=format&fit=crop&w=80&h=80&q=80' ?>" alt="<?= htmlspecialchars($item['name']) ?>" />
              <div>
                <span><?= htmlspecialchars($item['name']) ?></span>
                <span>₱<?= number_format($item['price'], 2) ?></span>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      </section>
    <?php endforeach; ?>
  <?php else: ?>
    <!-- Fallback if no categories or items -->
    <section class="menu-category">
      <h3>Burgers</h3>
      <ul>
        <li class="menu-item" data-bs-toggle="modal" data-bs-target="#productModal" data-name="Classic Cheeseburger"
          data-price="149" data-category="Burgers" 
          data-image="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80"
          data-description="Our signature beef patty topped with melted American cheese, fresh lettuce, tomato, and our special sauce on a toasted bun.">
          <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-1.2.1&auto=format&fit=crop&w=80&h=80&q=80" alt="Classic Cheeseburger" />
          <div><span>Classic Cheeseburger</span><span>₱149</span></div>
        </li>
        <li class="menu-item" data-bs-toggle="modal" data-bs-target="#productModal" data-name="Bacon BBQ Burger"
          data-price="179" data-category="Burgers" 
          data-image="https://images.unsplash.com/photo-1595974787270-81addba290b6?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80"
          data-description="Premium beef patty topped with crispy bacon, cheddar cheese, onion rings, and our tangy BBQ sauce.">
          <img src="https://images.unsplash.com/photo-1595974787270-81addba290b6?ixlib=rb-1.2.1&auto=format&fit=crop&w=80&h=80&q=80" alt="Bacon BBQ Burger" />
          <div><span>Bacon BBQ Burger</span><span>₱179</span></div>
        </li>
      </ul>
    </section>
  <?php endif; ?>

  <!-- Recently Viewed Section -->
  <section class="menu-category recently-viewed-section d-none">
    <h3>Recently Viewed</h3>
    <ul id="recentlyViewedItems" class="d-flex flex-row flex-wrap justify-content-start">
      <!-- Items will be dynamically added here -->
    </ul>
  </section>
</main>

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="productModalLabel">Product Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="product-details">
          <div class="product-image mb-3 position-relative">
            <img src="" alt="Product" class="img-fluid rounded">
            <span class="badge bg-primary product-category position-absolute top-0 end-0 m-2"></span>
          </div>
          <div class="product-info">
            <h4 class="product-name mb-2"></h4>
            <p class="product-description"></p>
            
            <div class="accordion mt-3 mb-3" id="nutritionAccordion">
              <div class="accordion-item">
                <h2 class="accordion-header" id="nutritionHeading">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#nutritionCollapse" aria-expanded="false" aria-controls="nutritionCollapse">
                    Nutritional Information
                  </button>
                </h2>
                <div id="nutritionCollapse" class="accordion-collapse collapse" aria-labelledby="nutritionHeading" data-bs-parent="#nutritionAccordion">
                  <div class="accordion-body">
                    <div class="nutrition-info">
                      <table class="table table-sm table-borderless mb-0">
                        <tr>
                          <td>Calories:</td>
                          <td class="text-end nutrition-calories">320 kcal</td>
                        </tr>
                        <tr>
                          <td>Protein:</td>
                          <td class="text-end nutrition-protein">18g</td>
                        </tr>
                        <tr>
                          <td>Carbohydrates:</td>
                          <td class="text-end nutrition-carbs">35g</td>
                        </tr>
                        <tr>
                          <td>Fat:</td>
                          <td class="text-end nutrition-fat">12g</td>
                        </tr>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-4">
              <h5 class="product-price mb-0"></h5>
              <div class="quantity-control d-flex align-items-center">
                <button class="btn btn-outline-primary qty-btn" data-action="decrease">-</button>
                <input type="text" class="form-control mx-2 qty-input" value="1"
                  style="width: 50px; text-align: center;">
                <button class="btn btn-outline-primary qty-btn" data-action="increase">+</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary add-to-cart-btn">
          <i class="bi bi-cart-plus me-1"></i> Add to Cart
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  console.log("DOM fully loaded");
   
  // Ensure localStorage is available
  function isLocalStorageAvailable() {
    try {
      localStorage.setItem('test', 'test');
      localStorage.removeItem('test');
      return true;
    } catch(e) {
      console.error('localStorage is not available:', e);
      return false;
    }
  }
   
  // Check localStorage availability before proceeding
  if (!isLocalStorageAvailable()) {
    alert('Your browser does not support local storage. Cart functionality may not work properly.');
  }
  
  // Initialize cart if it doesn't exist
  if (!localStorage.getItem('cart')) {
    localStorage.setItem('cart', JSON.stringify([]));
    console.log("Cart initialized in localStorage");
  }
  
  const productModal = document.getElementById('productModal');
  const menuItems = document.querySelectorAll('.menu-item');
  const addToCartBtn = document.querySelector('.add-to-cart-btn');
  const qtyBtns = document.querySelectorAll('.qty-btn');
  const qtyInput = document.querySelector('.qty-input');
  const quickAddBtns = document.querySelectorAll('.quick-add');

  console.log("Menu items found:", menuItems.length);
  console.log("Add to cart button found:", addToCartBtn !== null);
  console.log("Quick add buttons found:", quickAddBtns.length);
  
  // Initialize Bootstrap modal
  const productModalInstance = new bootstrap.Modal(productModal);
  
  // Quick Add functionality for featured items
  quickAddBtns.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.stopPropagation(); // Prevent opening the modal when clicking the button
      
      const name = this.dataset.name;
      const price = parseFloat(this.dataset.price);
      const image = this.dataset.image;
      
      // Add item to cart
      addItemToCart(name, price, image, 1);
      
      // Show success message with toast
      showAddedToCartToast(name);
      
      // Update cart count
      updateCartCount();
    });
  });
  
  // Function to add item to cart
  function addItemToCart(name, price, image, quantity) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Check if item is already in cart
    const existingItemIndex = cart.findIndex(item => item.name === name);
    
    if (existingItemIndex > -1) {
      // Update quantity if item exists
      cart[existingItemIndex].quantity += quantity;
    } else {
      // Add new item
      cart.push({
        name,
        price,
        image,
        quantity
      });
    }
    
    // Save updated cart
    localStorage.setItem('cart', JSON.stringify(cart));
    console.log(`Added ${quantity} ${name} to cart`);
    
    // Add cart bounce animation to cart icon
    const cartIcons = document.querySelectorAll('.bi-cart3');
    cartIcons.forEach(icon => {
      icon.classList.add('cart-bounce');
      setTimeout(() => {
        icon.classList.remove('cart-bounce');
      }, 500);
    });
  }
  
  // Function to show toast when item added to cart
  function showAddedToCartToast(itemName) {
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    
    if (!toastContainer) {
      toastContainer = document.createElement('div');
      toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
      document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toastId = `toast-${Date.now()}`;
    const toastHTML = `
      <div id="${toastId}" class="toast align-items-center text-white bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body">
            <i class="bi bi-check-circle-fill me-2"></i> ${itemName} added to cart
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    
    // Initialize and show toast
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, {
      autohide: true,
      delay: 3000
    });
    
    toast.show();
    
    // Remove toast element after it's hidden
    toastElement.addEventListener('hidden.bs.toast', function() {
      toastElement.remove();
    });
  }

  // Set up focus trap for the modal
  const setupFocusTrap = () => {
    const focusableElements = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
    const modal = document.querySelector('#productModal');
    
    // If modal not found, return
    if (!modal) return;
    
    const firstFocusableElement = modal.querySelectorAll(focusableElements)[0];
    const focusableContent = modal.querySelectorAll(focusableElements);
    const lastFocusableElement = focusableContent[focusableContent.length - 1];
    
    document.addEventListener('keydown', function(e) {
      let isTabPressed = e.key === 'Tab' || e.keyCode === 9;
      
      if (!isTabPressed || !modal.classList.contains('show')) {
        return;
      }
      
      if (e.shiftKey) { // if shift key pressed for shift + tab combination
        if (document.activeElement === firstFocusableElement) {
          lastFocusableElement.focus(); // add focus for the last focusable element
          e.preventDefault();
        }
      } else { // if tab key is pressed
        if (document.activeElement === lastFocusableElement) {
          firstFocusableElement.focus(); // add focus for the first focusable element
          e.preventDefault();
        }
      }
    });
  };
  
  // Call setupFocusTrap when the page loads
  setupFocusTrap();

  // Store current product data
  let currentProduct = {};
  
  // Nutritional information database (simulated)
  const nutritionDatabase = {
    'Burgers': {
      'Classic Cheeseburger': { calories: 550, protein: 25, carbs: 45, fat: 30 },
      'Bacon BBQ Burger': { calories: 750, protein: 35, carbs: 52, fat: 42 },
      'Spicy Jalapeño Burger': { calories: 650, protein: 28, carbs: 48, fat: 38 }
    },
    'Sides': {
      'French Fries': { calories: 320, protein: 4, carbs: 42, fat: 16 },
      'Onion Rings': { calories: 340, protein: 5, carbs: 40, fat: 19 },
      'Cheese Sticks': { calories: 290, protein: 9, carbs: 28, fat: 15 }
    },
    'Drinks': {
      'Coke (Can)': { calories: 140, protein: 0, carbs: 39, fat: 0 },
      'Iced Tea': { calories: 90, protein: 0, carbs: 22, fat: 0 },
      'Bottled Water': { calories: 0, protein: 0, carbs: 0, fat: 0 }
    }
  };

  // Handle menu item click
  menuItems.forEach(item => {
    item.addEventListener('click', function(e) {
      e.preventDefault();
      console.log("Menu item clicked:", this.dataset.name);
      
      // Get product data from data attributes
      currentProduct = {
        name: this.dataset.name,
        price: parseInt(this.dataset.price),
        category: this.dataset.category,
        image: this.dataset.image,
        description: this.dataset.description,
        quantity: 1
      };
      
      console.log("Current product:", currentProduct);

      // Update modal with product data
      productModal.querySelector('.product-name').textContent = currentProduct.name;
      productModal.querySelector('.product-category').textContent = currentProduct.category;
      productModal.querySelector('.product-description').textContent = currentProduct.description;
      productModal.querySelector('.product-price').textContent = `₱${currentProduct.price}`;
      productModal.querySelector('.product-image img').src = currentProduct.image;
      productModal.querySelector('.qty-input').value = 1;
      
      // Update nutritional information if available
      const nutritionInfo = nutritionDatabase[currentProduct.category]?.[currentProduct.name];
      if (nutritionInfo) {
        productModal.querySelector('.nutrition-calories').textContent = `${nutritionInfo.calories} kcal`;
        productModal.querySelector('.nutrition-protein').textContent = `${nutritionInfo.protein}g`;
        productModal.querySelector('.nutrition-carbs').textContent = `${nutritionInfo.carbs}g`;
        productModal.querySelector('.nutrition-fat').textContent = `${nutritionInfo.fat}g`;
        document.getElementById('nutritionAccordion').classList.remove('d-none');
      } else {
        document.getElementById('nutritionAccordion').classList.add('d-none');
      }
      
      // Ensure accordions are closed when modal opens
      const accordionItems = productModal.querySelectorAll('.accordion-collapse');
      accordionItems.forEach(item => {
        const accordionInstance = bootstrap.Collapse.getInstance(item);
        if (accordionInstance) accordionInstance.hide();
      });
      
      // Add recently viewed item to localStorage
      addToRecentlyViewed(currentProduct);
      
      // Show the modal programmatically
      productModalInstance.show();
      
      // Fix for accessibility - ensure modal doesn't have aria-hidden when shown
      productModal.addEventListener('shown.bs.modal', function() {
        // Remove aria-hidden attribute if it exists
        if (productModal.getAttribute('aria-hidden')) {
          productModal.removeAttribute('aria-hidden');
        }
        
        // Set focus to the modal title for better accessibility
        const modalTitle = productModal.querySelector('.modal-title');
        if (modalTitle) {
          modalTitle.focus();
        }
      });
    });
  });

  // Function to add product to recently viewed
  function addToRecentlyViewed(product) {
    const recentlyViewed = JSON.parse(localStorage.getItem('recentlyViewed') || '[]');
    
    // Check if product is already in the recently viewed
    const existingIndex = recentlyViewed.findIndex(item => item.name === product.name);
    
    // If it exists, remove it so it can be added to the beginning
    if (existingIndex > -1) {
      recentlyViewed.splice(existingIndex, 1);
    }
    
    // Add product to beginning of array
    recentlyViewed.unshift({
      name: product.name,
      price: product.price,
      category: product.category,
      image: product.image
    });
    
    // Keep only most recent 5 items
    if (recentlyViewed.length > 5) {
      recentlyViewed.pop();
    }
    
    // Save back to localStorage
    localStorage.setItem('recentlyViewed', JSON.stringify(recentlyViewed));
  }

  // Quantity buttons
  qtyBtns.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault(); // Prevent default button behavior
      e.stopPropagation(); // Stop event propagation
      
      console.log("Quantity button clicked:", this.dataset.action);
      const action = this.dataset.action;
      let currentQty = parseInt(qtyInput.value);

      if (action === 'increase') {
        currentQty++;
      } else if (action === 'decrease' && currentQty > 1) {
        currentQty--;
      }

      qtyInput.value = currentQty;
      currentProduct.quantity = currentQty;
      console.log("Updated quantity:", currentQty);
    });
  });

  // Ensure quantity is valid when manually entered
  qtyInput.addEventListener('change', function() {
    let value = parseInt(this.value);
    if (isNaN(value) || value < 1) {
      value = 1;
    }
    this.value = value;
    currentProduct.quantity = value;
    console.log("Quantity input changed:", value);
  });

  // Add to cart button
  if (addToCartBtn) {
    addToCartBtn.addEventListener('click', function(e) {
      e.preventDefault(); // Prevent default button behavior
      console.log("Add to cart button clicked");
      
      // Check if user is logged in
      const isLoggedIn = <?php echo isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true ? 'true' : 'false'; ?>;
      
      if (isLoggedIn) {
        // Add to cart if logged in
        addToCart(currentProduct);
      } else {
        // Show signup warning for non-logged in users
        showSignupPrompt(currentProduct.name);
        
        // Close the product modal
        const bsModal = bootstrap.Modal.getInstance(productModal);
        bsModal.hide();
      }
    });
  }

  // Function to show signup prompt
  function showSignupPrompt(productName) {
    // Create a Bootstrap modal for the signup prompt
    const modalHTML = `
      <div class="modal fade" id="signupPromptModal" tabindex="-1" aria-labelledby="signupPromptModalLabel">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header bg-warning">
              <h5 class="modal-title" id="signupPromptModalLabel">Sign Up Required</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="text-center mb-4">
                <i class="bi bi-exclamation-circle" style="font-size: 3rem; color: #FFC107;"></i>
              </div>
              <p>You need to be signed in to add items to your cart.</p>
              <p>Would you like to sign up or log in now?</p>
            </div>
            <div class="modal-footer justify-content-center">
              <a href="<?php echo $in_pages_dir ? '../login.php' : 'login.php'; ?>" class="btn btn-outline-primary">Log In</a>
              <a href="<?php echo $in_pages_dir ? '../signup.php' : 'signup.php'; ?>" class="btn btn-primary">Sign Up</a>
            </div>
          </div>
        </div>
      </div>
    `;
    
    // Append the modal to the body if it doesn't exist
    if (!document.getElementById('signupPromptModal')) {
      const modalContainer = document.createElement('div');
      modalContainer.innerHTML = modalHTML;
      document.body.appendChild(modalContainer.firstChild);
    }
    
    // Initialize and show the modal
    const signupPromptModal = document.getElementById('signupPromptModal');
    const signupModalInstance = new bootstrap.Modal(signupPromptModal);
    signupModalInstance.show();
  }

  // Function to add product to cart
  function addToCart(product) {
    try {
      console.log("Adding to cart:", product);
      
      // Validate product data
      if (!product || !product.name || !product.price) {
        console.error("Invalid product data:", product);
        showToast("Error adding item to cart. Invalid product data.");
        return;
      }
      
      // Get current cart or initialize empty cart
      let cart;
      try {
        cart = JSON.parse(localStorage.getItem('cart')) || [];
        console.log("Current cart:", cart);
      } catch (e) {
        console.error("Error parsing cart from localStorage:", e);
        cart = [];
      }

      // Check if product already exists in cart
      const existingProductIndex = cart.findIndex(item => item.name === product.name);
      let isNewItem = existingProductIndex === -1;

      if (existingProductIndex > -1) {
        // Update quantity if product exists
        cart[existingProductIndex].quantity += product.quantity;
        console.log("Updated existing product in cart");
      } else {
        // Add new product to cart
        cart.push({
          ...product
        });
        console.log("Added new product to cart");
      }

      // Save cart to localStorage
      try {
        localStorage.setItem('cart', JSON.stringify(cart));
        console.log("Cart saved to localStorage");
      } catch (e) {
        console.error("Error saving cart to localStorage:", e);
        showToast("Error saving cart. Please try again.");
        return;
      }

      // Update cart count in navbar
      updateCartCount();

      // Close modal
      try {
        const bsModal = bootstrap.Modal.getInstance(productModal);
        if (bsModal) {
          bsModal.hide();
        }
      } catch (e) {
        console.error("Error closing modal:", e);
      }

      // Show success message with animation
      animateCartIcon();
      showToast(`${product.name} added to cart!`);
    } catch (e) {
      console.error("Error in addToCart function:", e);
      showToast("An error occurred while adding to cart.");
    }
  }

  // Function to animate cart icon when product is added
  function animateCartIcon() {
    const cartIcon = document.querySelector('.cart-link i');
    
    if (!cartIcon) return;
    
    // Create a clone of the product image that will fly to the cart
    const productImg = document.querySelector('.product-image img');
    if (productImg) {
      const imgClone = document.createElement('img');
      imgClone.src = productImg.src;
      imgClone.style.position = 'fixed';
      imgClone.style.height = '50px';
      imgClone.style.width = '50px';
      imgClone.style.borderRadius = '50%';
      imgClone.style.objectFit = 'cover';
      imgClone.style.boxShadow = '0 5px 15px rgba(0,0,0,0.3)';
      imgClone.style.transition = 'all 0.8s cubic-bezier(0.18, 0.89, 0.32, 1.28)';
      imgClone.style.zIndex = '9999';
      
      // Get positions
      const imgRect = productImg.getBoundingClientRect();
      const cartRect = cartIcon.getBoundingClientRect();
      
      // Set starting position
      imgClone.style.top = `${imgRect.top}px`;
      imgClone.style.left = `${imgRect.left}px`;
      
      // Add to body
      document.body.appendChild(imgClone);
      
      // Start animation in the next frame
      setTimeout(() => {
        imgClone.style.top = `${cartRect.top}px`;
        imgClone.style.left = `${cartRect.left}px`;
        imgClone.style.height = '20px';
        imgClone.style.width = '20px';
        imgClone.style.opacity = '0.5';
      }, 10);
      
      // Handle animation end
      imgClone.addEventListener('transitionend', function() {
        // Remove clone
        document.body.removeChild(imgClone);
        
        // Animate cart icon
        cartIcon.classList.add('cart-bounce');
        setTimeout(() => {
          cartIcon.classList.remove('cart-bounce');
        }, 500);
      });
    } else {
      // If no product image, just animate the cart icon
      cartIcon.classList.add('cart-bounce');
      setTimeout(() => {
        cartIcon.classList.remove('cart-bounce');
      }, 500);
    }
  }

  // Update cart count
  function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
    console.log("Updating cart count:", totalItems);
    
    const cartCountElements = document.querySelectorAll('.cart-count');
    console.log("Cart count elements found:", cartCountElements.length);
    
    if (cartCountElements.length > 0) {
      cartCountElements.forEach(element => {
        element.textContent = totalItems;
        element.style.display = totalItems > 0 ? 'inline-block' : 'none';
        console.log("Updated cart count element");
      });
    } else {
      console.log("No cart count elements found");
    }
  }

  // Show toast message
  function showToast(message) {
    console.log("Showing toast message:", message);
    
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
      toastContainer = document.createElement('div');
      toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
      document.body.appendChild(toastContainer);
      console.log("Created toast container");
    }

    // Create toast element
    const toastEl = document.createElement('div');
    toastEl.className = 'toast align-items-center text-white bg-primary border-0';
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');

    toastEl.innerHTML = `
        <div class="d-flex">
          <div class="toast-body">
            ${message}
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      `;

    // Append toast to container
    toastContainer.appendChild(toastEl);

    // Initialize Bootstrap toast and show it
    const bsToast = new bootstrap.Toast(toastEl);
    bsToast.show();

    // Remove toast after it's hidden
    toastEl.addEventListener('hidden.bs.toast', function() {
      toastEl.remove();
    });
  }

  // Update recently viewed section on page load
  updateRecentlyViewedSection();
  
  function updateRecentlyViewedSection() {
    const recentlyViewed = JSON.parse(localStorage.getItem('recentlyViewed') || '[]');
    const recentlyViewedSection = document.querySelector('.recently-viewed-section');
    const recentlyViewedList = document.getElementById('recentlyViewedItems');
    
    if (recentlyViewed.length === 0) {
      recentlyViewedSection.classList.add('d-none');
      return;
    }
    
    // Show the section and clear previous items
    recentlyViewedSection.classList.remove('d-none');
    recentlyViewedList.innerHTML = '';
    
    // Add each recently viewed item to the list
    recentlyViewed.forEach(item => {
      const li = document.createElement('li');
      li.className = 'menu-item recent-item';
      li.dataset.bsToggle = 'modal';
      li.dataset.bsTarget = '#productModal';
      li.dataset.name = item.name;
      li.dataset.price = item.price;
      li.dataset.category = item.category;
      li.dataset.image = item.image;
      
      // Get product description from matching menu item
      const menuItem = Array.from(document.querySelectorAll('.menu-item')).find(
        menuItem => menuItem.dataset.name === item.name
      );
      li.dataset.description = menuItem ? menuItem.dataset.description : '';
      
      li.innerHTML = `
        <div class="position-relative">
          <img src="${item.image}" alt="${item.name}">
          <div><span>${item.name}</span><span>₱${item.price}</span></div>
        </div>
      `;
      
      recentlyViewedList.appendChild(li);
      
      // Add event listener to the newly created element
      li.addEventListener('click', function() {
        const currentProduct = {
          name: this.dataset.name,
          price: parseInt(this.dataset.price),
          category: this.dataset.category,
          image: this.dataset.image,
          description: this.dataset.description,
          quantity: 1
        };
        
        // Update modal with product data
        productModal.querySelector('.product-name').textContent = currentProduct.name;
        productModal.querySelector('.product-category').textContent = currentProduct.category;
        productModal.querySelector('.product-description').textContent = currentProduct.description;
        productModal.querySelector('.product-price').textContent = `₱${currentProduct.price}`;
        productModal.querySelector('.product-image img').src = currentProduct.image;
        productModal.querySelector('.qty-input').value = 1;
        
        // Update nutritional information if available
        const nutritionInfo = nutritionDatabase[currentProduct.category]?.[currentProduct.name];
        if (nutritionInfo) {
          productModal.querySelector('.nutrition-calories').textContent = `${nutritionInfo.calories} kcal`;
          productModal.querySelector('.nutrition-protein').textContent = `${nutritionInfo.protein}g`;
          productModal.querySelector('.nutrition-carbs').textContent = `${nutritionInfo.carbs}g`;
          productModal.querySelector('.nutrition-fat').textContent = `${nutritionInfo.fat}g`;
          document.getElementById('nutritionAccordion').classList.remove('d-none');
        } else {
          document.getElementById('nutritionAccordion').classList.add('d-none');
        }
      });
    });
  }
  
  // Update recently viewed section when the modal is closed
  productModal.addEventListener('hidden.bs.modal', function() {
    updateRecentlyViewedSection();
  });
});
</script>