<!-- Main content only - no HTML/head/body tags needed since this is included in index.php -->
<main>
  <!-- Hero Section -->
  <section class="landing-page position-relative">
    <div class="hero-bg-text">
      Friendly Neighborhood Food<br>Friendly Neighborhood Food<br>Friendly Neighborhood Food
    </div>
    <div class="landing-hero-wrapper">
      <div class="circular-frame mx-auto mb-4">
        <img src="assets/images/burgerfries.jpg" alt="Delicious food" class="food-image">
      </div>
      <a href="#" class="btn btn-primary mt-3">Order Now</a>
    </div>
  </section>

  <!-- About Section -->
  <section class="about-section">
    <div class="container">
      <h2>ABOUT <span style="color:#fff;">COFITEARIA</span></h2>
      <p class="mb-4">Lillies Food Shop is a project for our Platform Technologies course at the Technological Institute of the Philippines. As students, we are exploring the most efficient ways to develop the website with minimal effort while maintaining quality, functionality, and a user-friendly experience.</p>
      <a href="#" class="btn">Learn More</a>
    </div>
  </section>

  <!-- Local/Fresh Section -->
  <section class="local-section">
    <div class="container">
      <h1><span class="text-black">LOCAL</span> FRESH, <span class="text-black">DAMN, TASTY</span></h1>
      <p class="mb-4">Lillies Food Shop is a project for our Platform Technologies course at the Technological Institute of the Philippines. As students, we are exploring the most efficient ways to develop the website with minimal effort while maintaining quality, functionality, and a user-friendly experience.</p>
      <div class="drink-container">
        <img src="assets/images/food1.jpg" class="bubble-img">
        <img src="assets/images/food2.jpg" class="bubble-img">
        <img src="assets/images/food3.jpg" class="bubble-img">
      </div>
    </div>
  </section>

  <!-- Featured Section -->
  <section class="featured-section">
    <div class="container">
      <h2>FEATURED <img src="assets/images/burger.png" style="width:40px;vertical-align:middle;"></h2>
      <div class="featured-grid">
        <div class="featured-card">
          <img src="assets/images/dining.jpg" class="card-img mb-3">
          <h4>Dining Experience</h4>
          <p>Enjoy our delicious meals in a cozy atmosphere</p>
        </div>
        <div class="featured-card">
          <img src="assets/images/friends.jpg" class="card-img mb-3">
          <h4>Share with Friends</h4>
          <p>Enjoy our delicious meals in a cozy atmosphere</p>
        </div>
        <div class="featured-card">
          <img src="assets/images/family.jpg" class="card-img mb-3">
          <h4>Share with loved ones</h4>
          <p>Enjoy your delicious meals with your loved ones</p>
        </div>
        <div class="featured-card">
          <img src="assets/images/fries.jpg" class="card-img mb-3">
          <h4>French Fries Discount</h4>
          <p>Crispy fries at a special price</p>
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
            <h3>John Doe</h3>
            <p>Regular Customer</p>
          </div>
          <div class="review-stars">★★★★★</div>
          <p class="review-text">"The burgers here are absolutely amazing! The quality of ingredients and the taste is unmatched. My go-to place for a delicious meal."</p>
          <p class="review-date">March 15, 2024</p>
        </div>
        <div class="review-card">
          <div class="reviewer-info">
            <h3>Sarah Smith</h3>
            <p>Food Enthusiast</p>
          </div>
          <div class="review-stars">★★★★★</div>
          <p class="review-text">"Great atmosphere and even better food! The staff is friendly and the service is quick. Love their special sauce!"</p>
          <p class="review-date">March 18, 2024</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Order Now CTA -->
  <section class="order-now-cta">
    ORDER NOW
    <a href="#" class="btn">ORDER NOW</a>
  </section>
</main>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<!-- Initialize Swiper -->
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
    });
</script>