<?php
// Get the current page name and path for active state
$current_page = basename($_SERVER['PHP_SELF']);
$current_full_path = $_SERVER['PHP_SELF'];

// Determine if we're in a subdirectory (Pages/)
$in_pages_dir = strpos($current_full_path, '/Pages/') !== false;
$base_path = $in_pages_dir ? '../' : '';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
  <div class="container">
    <a class="navbar-brand" href="<?php echo $base_path; ?>index.php">
      <span class="logo-text">Lillies <span class="food-text">Food</span> Shop</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link <?php echo ($current_page == 'index.php' || $current_full_path == '/index.php') ? 'active' : ''; ?>"
            href="<?php echo $base_path; ?>index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($current_page == 'menu.php') ? 'active' : ''; ?>"
            href="<?php echo $base_path; ?>Pages/menu.php">Menu</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>"
            href="<?php echo $base_path; ?>Pages/about.php">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>"
            href="<?php echo $base_path; ?>Pages/contact.php">Contact</a>
        </li>
      </ul>
      <div class="d-flex align-items-center">
        <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
          <a href="#" class="cart-link me-3" data-bs-toggle="modal" data-bs-target="#cartModal">
            <i class="bi bi-cart3"></i>
            <span class="badge bg-danger rounded-pill cart-count">0</span>
          </a>
          <div class="dropdown">
            <a class="btn btn-link dropdown-toggle user-menu" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person-circle me-1"></i>
              <?php echo htmlspecialchars($_SESSION["name"]); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="<?php echo $base_path; ?>profile.php">My Profile</a></li>
              <li><a class="dropdown-item" href="<?php echo $base_path; ?>orders.php">My Orders</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="<?php echo $base_path; ?>logout.php">Logout</a></li>
              <?php if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] === true): ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?php echo $base_path; ?>admin/dashboard.php">Admin Dashboard</a></li>
              <?php endif; ?>
            </ul>
          </div>
        <?php else: ?>
          <a href="<?php echo $base_path; ?>login.php" class="btn btn-outline-primary me-2">Login</a>
          <a href="<?php echo $base_path; ?>signup.php" class="order-now-link">Sign Up</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<!-- Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cartModalLabel">Your Cart</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="cartItemsContainer">
          <div class="text-center py-5" id="emptyCartMessage">
            <i class="bi bi-cart-x" style="font-size: 3rem; color: #ccc;"></i>
            <p class="mt-3">Your cart is empty</p>
            <a href="<?php echo $base_path; ?>Pages/menu.php" class="btn btn-primary mt-2">Browse Menu</a>
          </div>
          <div id="cartItems" class="d-none">
            <!-- Cart items will be added here dynamically -->
          </div>
          <div id="savedItems" class="d-none mt-4">
            <h6 class="border-bottom pb-2 mb-3">Saved For Later</h6>
            <div id="savedItemsList">
              <!-- Saved items will be added here dynamically -->
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <div>
          <span class="fw-bold">Total: </span>
          <span class="cart-total">₱0.00</span>
        </div>
        <div>
          <button type="button" class="btn btn-outline-danger clear-cart-btn me-2">Clear Cart</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continue Shopping</button>
          <button type="button" class="btn btn-primary checkout-btn">Checkout</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Navbar scroll effect
  const navbar = document.querySelector('.navbar-custom');
  window.addEventListener('scroll', function() {
    if (window.scrollY > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });

  // Cart functionality
  updateCartCount();
  
  // Update cart display when cart modal is opened
  const cartModal = document.getElementById('cartModal');
  if (cartModal) {
    cartModal.addEventListener('show.bs.modal', updateCartDisplay);
  }

  // Add event listener for checkout button
  const checkoutBtn = document.querySelector('.checkout-btn');
  if (checkoutBtn) {
    checkoutBtn.addEventListener('click', function() {
      // Get cart items
      const cart = JSON.parse(localStorage.getItem('cart')) || [];
      
      // Only redirect to login if cart has items
      if (cart.length > 0) {
        // Check if user is logged in
        const isLoggedIn = <?php echo isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true ? 'true' : 'false'; ?>;
        
        if (isLoggedIn) {
          // Redirect to checkout page
          window.location.href = '<?php echo $base_path; ?>checkout.php';
        } else {
          // Redirect to login page instead of signup
          window.location.href = '<?php echo $base_path; ?>login.php?redirect=checkout';
        }
      } else {
        // Show message if cart is empty
        alert('Your cart is empty. Please add items before proceeding to checkout.');
      }
    });
  }

  // Add event listener for clear cart button
  const clearCartBtn = document.querySelector('.clear-cart-btn');
  if (clearCartBtn) {
    clearCartBtn.addEventListener('click', function() {
      // Confirm before clearing cart
      if (confirm('Are you sure you want to clear your cart? This action cannot be undone.')) {
        // Clear cart from localStorage
        localStorage.setItem('cart', JSON.stringify([]));
        
        // Update cart count and display
        updateCartCount();
        updateCartDisplay();
        
        // Show confirmation
        alert('Your cart has been cleared.');
      }
    });
  }

  // Function to update cart count
  function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
    const cartCountElements = document.querySelectorAll('.cart-count');
    cartCountElements.forEach(element => {
      element.textContent = totalItems;
      element.style.display = totalItems > 0 ? 'inline-block' : 'none';
    });
  }

  // Initialize saved items if doesn't exist
  if (!localStorage.getItem('savedItems')) {
    localStorage.setItem('savedItems', JSON.stringify([]));
  }
  
  // Function to update cart display in modal
  function updateCartDisplay() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const savedItems = JSON.parse(localStorage.getItem('savedItems')) || [];
    const cartItemsElement = document.getElementById('cartItems');
    const savedItemsElement = document.getElementById('savedItems');
    const savedItemsListElement = document.getElementById('savedItemsList');
    const emptyCartMessage = document.getElementById('emptyCartMessage');
    
    // Handle cart items display
    if (cart.length === 0) {
      cartItemsElement.classList.add('d-none');
      emptyCartMessage.classList.remove('d-none');
    } else {
      // Show cart items, hide empty message
      cartItemsElement.classList.remove('d-none');
      emptyCartMessage.classList.add('d-none');
      
      // Clear previous items
      cartItemsElement.innerHTML = '';
      
      // Create table headers
      const table = document.createElement('table');
      table.className = 'table';
      table.innerHTML = `
        <thead>
          <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody></tbody>
      `;
      
      let total = 0;
      
      // Add each cart item to the table
      cart.forEach((item, index) => {
        const subtotal = item.price * item.quantity;
        total += subtotal;
        
        const tr = document.createElement('tr');
        tr.dataset.index = index; // Store index for animation purposes
        tr.innerHTML = `
          <td>
            <div class="d-flex align-items-center">
              <img src="${item.image}" class="me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
              <span>${item.name}</span>
            </div>
          </td>
          <td>₱${item.price.toFixed(2)}</td>
          <td>
            <div class="input-group input-group-sm" style="width: 100px;">
              <button class="btn btn-outline-secondary qty-btn" data-action="decrease" data-index="${index}">-</button>
              <input type="number" min="1" class="form-control text-center qty-input" value="${item.quantity}" data-index="${index}">
              <button class="btn btn-outline-secondary qty-btn" data-action="increase" data-index="${index}">+</button>
            </div>
          </td>
          <td class="subtotal">₱${subtotal.toFixed(2)}</td>
          <td>
            <div class="btn-group btn-group-sm">
              <button class="btn btn-outline-primary save-for-later-btn" data-index="${index}" title="Save for later">
                <i class="bi bi-bookmark"></i>
              </button>
              <button class="btn btn-outline-danger remove-btn" data-index="${index}" title="Remove item">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </td>
        `;
        
        table.querySelector('tbody').appendChild(tr);
      });
      
      // Add table to cart items container
      cartItemsElement.appendChild(table);
      
      // Update total
      document.querySelector('.cart-total').textContent = `₱${total.toFixed(2)}`;
    }
    
    // Handle saved items display
    if (savedItems.length === 0) {
      savedItemsElement.classList.add('d-none');
    } else {
      savedItemsElement.classList.remove('d-none');
      savedItemsListElement.innerHTML = '';
      
      // Create table for saved items
      const savedTable = document.createElement('table');
      savedTable.className = 'table table-sm';
      savedTable.innerHTML = `
        <thead>
          <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody></tbody>
      `;
      
      // Add each saved item to the table
      savedItems.forEach((item, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>
            <div class="d-flex align-items-center">
              <img src="${item.image}" class="me-2" style="width: 30px; height: 30px; object-fit: cover; border-radius: 4px;">
              <span>${item.name}</span>
            </div>
          </td>
          <td>₱${item.price.toFixed(2)}</td>
          <td>
            <div class="btn-group btn-group-sm">
              <button class="btn btn-outline-primary move-to-cart-btn" data-index="${index}" title="Move to cart">
                <i class="bi bi-cart-plus"></i>
              </button>
              <button class="btn btn-outline-danger remove-saved-btn" data-index="${index}" title="Remove item">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </td>
        `;
        
        savedTable.querySelector('tbody').appendChild(tr);
      });
      
      savedItemsListElement.appendChild(savedTable);
    }
    
    // Add event listeners to quantity buttons
    document.querySelectorAll('.qty-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const action = this.dataset.action;
        const index = parseInt(this.dataset.index);
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        
        if (action === 'increase') {
          cart[index].quantity++;
        } else if (action === 'decrease') {
          if (cart[index].quantity > 1) {
            cart[index].quantity--;
          }
        }
        
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        
        // Get the row to highlight
        const row = document.querySelector(`tr[data-index="${index}"]`);
        
        // Calculate new subtotal and update without refreshing the entire cart
        const newSubtotal = cart[index].price * cart[index].quantity;
        row.querySelector('.subtotal').textContent = `₱${newSubtotal.toFixed(2)}`;
        row.querySelector('input.qty-input').value = cart[index].quantity;
        
        // Update total
        const newTotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
        document.querySelector('.cart-total').textContent = `₱${newTotal.toFixed(2)}`;
        
        // Add highlight effect
        highlightRow(row);
      });
    });
    
    // Add event listeners to quantity input fields
    document.querySelectorAll('.qty-input').forEach(input => {
      input.addEventListener('change', function() {
        const index = parseInt(this.dataset.index);
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        let newQty = parseInt(this.value);
        
        // Validate quantity (minimum 1)
        if (isNaN(newQty) || newQty < 1) {
          newQty = 1;
          this.value = 1;
        }
        
        cart[index].quantity = newQty;
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        
        // Get the row to highlight
        const row = document.querySelector(`tr[data-index="${index}"]`);
        
        // Calculate new subtotal and update
        const newSubtotal = cart[index].price * cart[index].quantity;
        row.querySelector('.subtotal').textContent = `₱${newSubtotal.toFixed(2)}`;
        
        // Update total
        const newTotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
        document.querySelector('.cart-total').textContent = `₱${newTotal.toFixed(2)}`;
        
        // Add highlight effect
        highlightRow(row);
      });
    });
    
    // Add event listeners to remove buttons
    document.querySelectorAll('.remove-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const index = parseInt(this.dataset.index);
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const row = document.querySelector(`tr[data-index="${index}"]`);
        
        // Add remove animation
        row.classList.add('bg-danger', 'text-white', 'fade-out');
        
        setTimeout(() => {
          // Remove the item after animation
          cart.splice(index, 1);
          localStorage.setItem('cart', JSON.stringify(cart));
          updateCartCount();
          updateCartDisplay();
        }, 500);
      });
    });
    
    // Add event listeners to "Save for Later" buttons
    document.querySelectorAll('.save-for-later-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const index = parseInt(this.dataset.index);
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const savedItems = JSON.parse(localStorage.getItem('savedItems')) || [];
        const item = cart[index];
        
        // Move item to saved items
        savedItems.push(item);
        localStorage.setItem('savedItems', JSON.stringify(savedItems));
        
        // Remove from cart
        cart.splice(index, 1);
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Update display
        updateCartCount();
        updateCartDisplay();
      });
    });
    
    // Add event listeners to "Move to Cart" buttons
    document.querySelectorAll('.move-to-cart-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const index = parseInt(this.dataset.index);
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const savedItems = JSON.parse(localStorage.getItem('savedItems')) || [];
        const item = savedItems[index];
        
        // Ensure quantity is set
        if (!item.quantity) item.quantity = 1;
        
        // Move item to cart
        cart.push(item);
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Remove from saved items
        savedItems.splice(index, 1);
        localStorage.setItem('savedItems', JSON.stringify(savedItems));
        
        // Update display
        updateCartCount();
        updateCartDisplay();
      });
    });
    
    // Add event listeners to remove saved item buttons
    document.querySelectorAll('.remove-saved-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const index = parseInt(this.dataset.index);
        const savedItems = JSON.parse(localStorage.getItem('savedItems')) || [];
        
        // Remove item
        savedItems.splice(index, 1);
        localStorage.setItem('savedItems', JSON.stringify(savedItems));
        
        // Update display
        updateCartDisplay();
      });
    });
  }
  
  // Function to highlight a row when quantity changes
  function highlightRow(row) {
    row.classList.add('highlight-row');
    setTimeout(() => {
      row.classList.remove('highlight-row');
    }, 700);
  }
});
</script>