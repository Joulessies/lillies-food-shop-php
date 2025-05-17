<!-- Main content only - no HTML/head/body tags needed since this is included in index.php -->
<main>
  <!-- Hero Section with Carousel -->
  <section class="hero-carousel">
    <div id="homeCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-indicators">
        <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
      </div>
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img src="https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1171&q=80" class="d-block w-100" alt="Delicious burger">
          <div class="carousel-caption">
            <h1>Delicious Food, Delivered Fresh</h1>
            <p>Enjoy the best burgers and fries in town</p>
            <a href="Pages/menu.php" class="btn btn-primary btn-lg">View Our Menu</a>
          </div>
        </div>
        <div class="carousel-item">
          <img src="https://images.unsplash.com/photo-1561758033-d89a9ad46330?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80" class="d-block w-100" alt="Family meal">
          <div class="carousel-caption">
            <h1>Perfect for Family Gatherings</h1>
            <p>Share delicious moments with loved ones</p>
            <a href="Pages/menu.php" class="btn btn-primary btn-lg">Order Now</a>
          </div>
        </div>
        <div class="carousel-item">
          <img src="https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80" class="d-block w-100" alt="Desserts">
          <div class="carousel-caption">
            <h1>Sweet Treats and Desserts</h1>
            <p>End your meal with something sweet</p>
            <a href="Pages/menu.php" class="btn btn-primary btn-lg">See Desserts</a>
          </div>
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#homeCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#homeCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </section>

  <!-- About Section -->
  <section class="about-section">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <h2>ABOUT <span style="color:#fff;">LILLIES FOOD SHOP</span></h2>
          <p class="mb-4">Lillies Food Shop is a project for our Platform Technologies course at the Technological Institute of the Philippines. As students, we are exploring the most efficient ways to develop the website with minimal effort while maintaining quality, functionality, and a user-friendly experience.</p>
          <a href="Pages/about.php" class="btn">Learn More</a>
        </div>
        <div class="col-lg-6 mt-4 mt-lg-0">
          <img src="https://images.unsplash.com/photo-1556745753-b2904692b3cd?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1073&q=80" alt="About us" class="img-fluid rounded shadow-lg">
        </div>
      </div>
    </div>
  </section>

  <!-- Popular Items Section -->
  <section class="popular-items-section py-5">
    <div class="container">
      <h2 class="text-center mb-5">OUR POPULAR ITEMS</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="menu-item">
            <div class="menu-img">
              <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=500&h=400&q=80" alt="Classic Burger" class="img-fluid">
            </div>
            <div class="menu-info">
              <h3>Classic Cheeseburger</h3>
              <div class="price">₱149.00</div>
              <p>Juicy beef patty with melted cheese, lettuce, tomato, and our special sauce</p>
              <button class="btn btn-primary w-100 add-to-cart" data-name="Classic Cheeseburger" data-price="149.00" data-image="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=500&h=400&q=80">Add to Cart</button>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="menu-item">
            <div class="menu-img">
              <img src="https://images.unsplash.com/photo-1585109649139-366815a0d713?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=500&h=400&q=80" alt="French Fries" class="img-fluid">
            </div>
            <div class="menu-info">
              <h3>Golden French Fries</h3>
              <div class="price">₱69.00</div>
              <p>Crispy, golden brown fries served with ketchup and our signature dip</p>
              <button class="btn btn-primary w-100 add-to-cart" data-name="Golden French Fries" data-price="69.00" data-image="https://images.unsplash.com/photo-1585109649139-366815a0d713?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=500&h=400&q=80">Add to Cart</button>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="menu-item">
            <div class="menu-img">
              <img src="https://images.unsplash.com/photo-1579954115545-a95591f28bfc?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=500&h=400&q=80" alt="Milkshake" class="img-fluid">
            </div>
            <div class="menu-info">
              <h3>Creamy Milkshake</h3>
              <div class="price">₱89.00</div>
              <p>Rich and creamy milkshake available in chocolate, vanilla, or strawberry</p>
              <button class="btn btn-primary w-100 add-to-cart" data-name="Creamy Milkshake" data-price="89.00" data-image="https://images.unsplash.com/photo-1579954115545-a95591f28bfc?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=500&h=400&q=80">Add to Cart</button>
            </div>
          </div>
        </div>
      </div>
      <div class="text-center mt-5">
        <a href="Pages/menu.php" class="btn btn-outline-primary btn-lg">View Full Menu</a>
      </div>
    </div>
  </section>

  <!-- Featured Section -->
  <section class="featured-section">
    <div class="container">
      <h2>FEATURED <img src="https://icons.iconarchive.com/icons/iconsmind/outline/128/Burger-icon.png" style="width:40px;vertical-align:middle;"></h2>
      <div class="featured-grid">
        <div class="featured-card">
          <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80" class="card-img mb-3">
          <h4>Dining Experience</h4>
          <p>Enjoy our delicious meals in a cozy atmosphere</p>
        </div>
        <div class="featured-card">
          <img src="https://images.unsplash.com/photo-1544148103-0773bf10d330?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80" class="card-img mb-3">
          <h4>Share with Friends</h4>
          <p>Our food is perfect for sharing with friends</p>
        </div>
        <div class="featured-card">
          <img src="https://images.unsplash.com/photo-1511688878353-3a2f5be94cd7?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1074&q=80" class="card-img mb-3">
          <h4>Family Meals</h4>
          <p>Enjoy your meals with your loved ones</p>
        </div>
        <div class="featured-card">
          <img src="https://images.unsplash.com/photo-1576107232684-1279f390859f?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=880&q=80" class="card-img mb-3">
          <h4>Weekly Specials</h4>
          <p>Check out our special offers every week</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Customer Reviews -->
  <section class="customer-review">
    <div class="container">
      <h2>CUSTOMER REVIEWS</h2>
      <div class="review-grid">
        <div class="review-card">
          <div class="reviewer-info">
            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="John Doe" class="reviewer-img">
            <div>
              <h3>John Doe</h3>
              <p>Regular Customer</p>
            </div>
          </div>
          <div class="review-stars">★★★★★</div>
          <p class="review-text">"The burgers here are absolutely amazing! The quality of ingredients and the taste is unmatched. My go-to place for a delicious meal."</p>
          <p class="review-date">March 15, 2024</p>
        </div>
        <div class="review-card">
          <div class="reviewer-info">
            <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Sarah Smith" class="reviewer-img">
            <div>
              <h3>Sarah Smith</h3>
              <p>Food Enthusiast</p>
            </div>
          </div>
          <div class="review-stars">★★★★★</div>
          <p class="review-text">"Great atmosphere and even better food! The staff is friendly and the service is quick. Love their special sauce!"</p>
          <p class="review-date">March 18, 2024</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Order Now CTA -->
  <section class="order-now-cta mb-0">
    ORDER NOW
    <a href="Pages/menu.php" class="btn">ORDER NOW</a>
  </section>
</main>

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
        
        // Add to cart functionality
        const addToCartButtons = document.querySelectorAll('.add-to-cart');
        
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function() {
                const name = this.dataset.name;
                const price = parseFloat(this.dataset.price);
                const image = this.dataset.image;
                
                // Get current cart from localStorage
                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                
                // Check if item is already in cart
                const existingItemIndex = cart.findIndex(item => item.name === name);
                
                if (existingItemIndex > -1) {
                    // Increment quantity if item exists
                    cart[existingItemIndex].quantity += 1;
                } else {
                    // Add new item to cart
                    cart.push({
                        name: name,
                        price: price,
                        image: image,
                        quantity: 1
                    });
                }
                
                // Save cart back to localStorage
                localStorage.setItem('cart', JSON.stringify(cart));
                
                // Update cart count
                updateCartCount();
                
                // Show success alert
                alert(`${name} added to cart!`);
            });
        });
        
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
        
        // Initialize cart count
        updateCartCount();
    });
</script>