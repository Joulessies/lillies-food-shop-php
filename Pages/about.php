<?php
// Include CSS files
echo '<link rel="stylesheet" href="../styles/styles.css">';
echo '<link rel="stylesheet" href="../styles/about.css">';

// Include Bootstrap
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';
echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">';

// Include navigation with correct path
include_once $_SERVER['DOCUMENT_ROOT'] . '/lillies-food-shop/Layout/Navigation/navigation.php';
?>

<!-- About Banner -->
<section class="about-banner">
  <div class="container text-center py-5">
    <h1><span class="text-yellow">ABOUT</span> LILLIES FOOD SHOP</h1>
    <p class="mt-4">
      Lillies Food Shop is a project for our Platform Technologies course at the Technological Institute of the 
      Philippines. As students, we are exploring the most efficient ways to develop the website with minimal
      effort while maintaining quality, functionality, and a user-friendly experience.
    </p>
    <a href="#who-we-are" class="btn btn-light mt-4">LEARN MORE</a>
  </div>
</section>

<!-- About Page Content -->
<main>
  <!-- Who Are We Section -->
  <section class="who-we-are-section" id="who-we-are">
    <div class="container py-5">
      <div class="row">
        <div class="col-md-7 fade-in-left">
          <h1 class="section-title text-primary">WHO ARE WE</h1>
          <p class="mb-4">Lillies Food Shop was founded in 2022 with a simple mission - to provide delicious, fresh food made with quality ingredients at affordable prices. What started as a small family business has grown into a beloved neighborhood institution, serving thousands of happy customers every month.</p>
          <p>Our dedication to quality and service has never wavered, and we continue to explore new flavors and experiences to delight our customers.</p>
        </div>
        <div class="col-md-5 fade-in-right">
          <div class="about-image-container">
            <img src="https://placehold.co/600x400/0078ff/ffffff?text=Our+Story" alt="Our Story" class="img-fluid rounded shadow hover-lift">
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Why Us Section -->
  <section class="why-us-section">
    <div class="container py-5">
      <div class="row">
        <div class="col-md-7 fade-in-left">
          <h2 class="section-title text-primary">WHY US</h2>
          <p>At Lillies Food Shop, we pride ourselves on using only the freshest ingredients. Our recipes have been perfected over time, ensuring that every bite is delicious and satisfying. We believe in treating our customers like family, providing a warm and welcoming environment for everyone who walks through our doors.</p>
          <p>Our commitment to excellence extends beyond our food. We strive to create a memorable dining experience through exceptional service, a comfortable atmosphere, and a menu that caters to diverse tastes and preferences.</p>
        </div>
        <div class="col-md-5 fade-in-right">
          <div class="about-card hover-lift">
            <h3>Our Values</h3>
            <ul>
              <li>Quality ingredients</li>
              <li>Exceptional service</li>
              <li>Community focus</li>
              <li>Sustainable practices</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Our Mission Section -->
  <section class="our-mission-section">
    <div class="container py-5">
      <h2 class="section-title text-white fade-in-bottom">OUR MISSION</h2>
      <div class="mission-card mb-5 fade-in-bottom">
        <p>Our mission is to create delicious food that brings people together. We believe that good food has the power to create lasting memories and foster meaningful connections. Every dish we serve is made with care, using recipes that have been perfected over time.</p>
      </div>
      <div class="row g-4 mt-4">
        <div class="col-md-4 fade-in-bottom" style="animation-delay: 0.2s">
          <div class="value-card hover-lift">
            <div class="icon-wrapper">
              <i class="bi bi-heart-fill"></i>
            </div>
            <h3>Quality</h3>
            <p>We never compromise on the quality of our ingredients.</p>
          </div>
        </div>
        <div class="col-md-4 fade-in-bottom" style="animation-delay: 0.4s">
          <div class="value-card hover-lift">
            <div class="icon-wrapper">
              <i class="bi bi-people-fill"></i>
            </div>
            <h3>Community</h3>
            <p>We believe in being an active part of our community.</p>
          </div>
        </div>
        <div class="col-md-4 fade-in-bottom" style="animation-delay: 0.6s">
          <div class="value-card hover-lift">
            <div class="icon-wrapper">
              <i class="bi bi-globe"></i>
            </div>
            <h3>Sustainability</h3>
            <p>We are committed to reducing our environmental impact.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Our Team Section -->
  <section class="our-team-section">
    <div class="container py-5">
      <h2 class="section-title text-primary text-center fade-in-bottom">OUR TEAM</h2>
      <p class="text-center mb-5 fade-in-bottom">Meet the talented individuals who make Lillies Food Shop exceptional.</p>
      <div class="row g-4 justify-content-center">
        <div class="col-md-4 fade-in-bottom" style="animation-delay: 0.2s">
          <div class="team-member text-center hover-lift">
            <img src="https://placehold.co/400x400/0078ff/ffffff?text=Team+Member" alt="Shawn Andrei Paolo Agres" class="img-fluid rounded-circle mb-3 team-img">
            <h3>AGRES, SHAWN ANDREI PAOLO</h3>
            <p class="position">Lead Developer</p>
            <p class="bio">Leading the development of our digital platforms with innovative solutions and technical expertise.</p>
            <div class="social-links">
              <a href="#" class="social-link"><i class="bi bi-facebook"></i></a>
              <a href="#" class="social-link"><i class="bi bi-twitter"></i></a>
              <a href="#" class="social-link"><i class="bi bi-instagram"></i></a>
            </div>
          </div>
        </div>
        <div class="col-md-4 fade-in-bottom" style="animation-delay: 0.4s">
          <div class="team-member text-center hover-lift">
            <img src="https://placehold.co/400x400/0078ff/ffffff?text=Team+Member" alt="Aldean Jude De Guzman" class="img-fluid rounded-circle mb-3 team-img">
            <h3>DE GUZMAN, ALDEAN JUDE</h3>
            <p class="position">UI/UX Designer</p>
            <p class="bio">Creating beautiful and intuitive user experiences that delight our customers and enhance functionality.</p>
            <div class="social-links">
              <a href="#" class="social-link"><i class="bi bi-facebook"></i></a>
              <a href="#" class="social-link"><i class="bi bi-twitter"></i></a>
              <a href="#" class="social-link"><i class="bi bi-instagram"></i></a>
            </div>
          </div>
        </div>
        <div class="col-md-4 fade-in-bottom" style="animation-delay: 0.6s">
          <div class="team-member text-center hover-lift">
            <img src="https://placehold.co/400x400/0078ff/ffffff?text=Team+Member" alt="Rodnic Jeof Duco" class="img-fluid rounded-circle mb-3 team-img">
            <h3>DUCO, RODNIC JEOF</h3>
            <p class="position">Backend Developer</p>
            <p class="bio">Ensuring our systems run smoothly with robust backend solutions and database architecture.</p>
            <div class="social-links">
              <a href="#" class="social-link"><i class="bi bi-facebook"></i></a>
              <a href="#" class="social-link"><i class="bi bi-twitter"></i></a>
              <a href="#" class="social-link"><i class="bi bi-instagram"></i></a>
            </div>
          </div>
        </div>
      </div>
      <div class="row g-4 justify-content-center mt-4">
        <div class="col-md-4 fade-in-bottom" style="animation-delay: 0.8s">
          <div class="team-member text-center hover-lift">
            <img src="https://placehold.co/400x400/0078ff/ffffff?text=Team+Member" alt="Alex Arthur Enzon" class="img-fluid rounded-circle mb-3 team-img">
            <h3>ENZON, ALEX ARTHUR</h3>
            <p class="position">Quality Assurance</p>
            <p class="bio">Maintaining the highest standards across all aspects of our product and service delivery.</p>
            <div class="social-links">
              <a href="#" class="social-link"><i class="bi bi-facebook"></i></a>
              <a href="#" class="social-link"><i class="bi bi-twitter"></i></a>
              <a href="#" class="social-link"><i class="bi bi-instagram"></i></a>
            </div>
          </div>
        </div>
        <div class="col-md-4 fade-in-bottom" style="animation-delay: 1.0s">
          <div class="team-member text-center hover-lift">
            <img src="https://placehold.co/400x400/0078ff/ffffff?text=Team+Member" alt="Julius San Jose" class="img-fluid rounded-circle mb-3 team-img">
            <h3>SAN JOSE, JULIUS</h3>
            <p class="position">Project Manager</p>
            <p class="bio">Coordinating our teams and projects to ensure timely delivery and effective communication.</p>
            <div class="social-links">
              <a href="#" class="social-link"><i class="bi bi-facebook"></i></a>
              <a href="#" class="social-link"><i class="bi bi-twitter"></i></a>
              <a href="#" class="social-link"><i class="bi bi-instagram"></i></a>
            </div>
          </div>
        </div>
        <div class="col-md-4 fade-in-bottom" style="animation-delay: 1.2s">
          <div class="team-member text-center hover-lift">
            <img src="https://placehold.co/400x400/0078ff/ffffff?text=Team+Member" alt="Roger Servidad Jr." class="img-fluid rounded-circle mb-3 team-img">
            <h3>SERVIDAD, ROGER JR.</h3>
            <p class="position">System Architect</p>
            <p class="bio">Designing robust and scalable system architectures that power our digital solutions.</p>
            <div class="social-links">
              <a href="#" class="social-link"><i class="bi bi-facebook"></i></a>
              <a href="#" class="social-link"><i class="bi bi-twitter"></i></a>
              <a href="#" class="social-link"><i class="bi bi-instagram"></i></a>
            </div>
          </div>
        </div>
      </div>
      <div class="row g-4 justify-content-center mt-4">
        <div class="col-md-4 fade-in-bottom" style="animation-delay: 1.4s">
          <div class="team-member text-center hover-lift">
            <img src="https://placehold.co/400x400/0078ff/ffffff?text=Team+Member" alt="Jaycee Tesorero" class="img-fluid rounded-circle mb-3 team-img">
            <h3>TESORERO, JAYCEE</h3>
            <p class="position">Frontend Developer</p>
            <p class="bio">Creating responsive and visually appealing interfaces that enhance user experience.</p>
            <div class="social-links">
              <a href="#" class="social-link"><i class="bi bi-facebook"></i></a>
              <a href="#" class="social-link"><i class="bi bi-twitter"></i></a>
              <a href="#" class="social-link"><i class="bi bi-instagram"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Our Storefront Section -->
  <section class="storefront-section">
    <div class="container py-5">
      <div class="row align-items-center">
        <div class="col-md-6 fade-in-left">
          <h2 class="section-title text-primary">OUR SHOP</h2>
          <p class="mb-4">Located in the heart of the city, our shop provides a cozy and welcoming atmosphere for all our customers. Whether you're grabbing a quick bite or sitting down for a relaxed meal, we've created a space that feels like home.</p>
          <p>We're open 7 days a week from 10:00 AM to 10:00 PM, ready to serve you delicious meals made with care and passion.</p>
          <div class="mt-4">
            <a href="../Pages/contact.php" class="btn btn-primary">Find Us</a>
          </div>
        </div>
        <div class="col-md-6 fade-in-right">
          <div class="storefront-image-container">
            <img src="https://placehold.co/600x400/0078ff/ffffff?text=Our+Store" alt="Our Store" class="img-fluid rounded shadow hover-lift">
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Most Ordered Section -->
  <section class="most-ordered-section">
    <div class="container py-5">
      <h2 class="section-title text-primary text-center fade-in-bottom">MOST ORDERED</h2>
      <div class="row g-4 justify-content-center">
        <div class="col-md-4 fade-in-bottom" style="animation-delay: 0.2s">
          <div class="food-item text-center hover-lift">
            <img src="https://placehold.co/300x300/0078ff/ffffff?text=Classic+Fries" alt="Classic Fries" class="img-fluid rounded-circle mb-3">
            <h3>Classic Fries</h3>
            <p>Our signature crispy golden fries, perfectly seasoned.</p>
            <p class="price">$3.99</p>
          </div>
        </div>
        <div class="col-md-4 fade-in-bottom" style="animation-delay: 0.4s">
          <div class="food-item text-center hover-lift">
            <img src="https://placehold.co/300x300/0078ff/ffffff?text=Cheese+Fries" alt="Cheese Fries" class="img-fluid rounded-circle mb-3">
            <h3>Cheese Fries</h3>
            <p>Loaded with melted cheese and special seasoning.</p>
            <p class="price">$4.99</p>
          </div>
        </div>
        <div class="col-md-4 fade-in-bottom" style="animation-delay: 0.6s">
          <div class="food-item text-center hover-lift">
            <img src="https://placehold.co/300x300/0078ff/ffffff?text=Spicy+Fries" alt="Spicy Fries" class="img-fluid rounded-circle mb-3">
            <h3>Spicy Fries</h3>
            <p>For those who like it hot! With our secret spice blend.</p>
            <p class="price">$4.99</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ Section -->
  <section class="faq-section">
    <div class="container py-5">
      <h2 class="section-title text-white text-center fade-in-bottom">Frequently Asked Questions</h2>
      <div class="accordion fade-in-bottom" id="faqAccordion">
        <div class="accordion-item">
          <h3 class="accordion-header" id="faqOne">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
              Why did you create Lillies?
            </button>
          </h3>
          <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="faqOne" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              We created Lillies Food Shop because we wanted to share our love for quality food with our community. We saw a need for fresh, delicious meals made with high-quality ingredients at reasonable prices.
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h3 class="accordion-header" id="faqTwo">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
              What is your most popular item?
            </button>
          </h3>
          <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="faqTwo" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              Our signature burgers and classic fries are consistently our top sellers. Customers especially love our special house sauce that comes with all of our burger options.
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h3 class="accordion-header" id="faqThree">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
              Do you have a food truck coming for events?
            </button>
          </h3>
          <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="faqThree" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              Yes! We offer catering services and can bring our food truck to your event. Please contact us at least two weeks in advance to check availability and discuss menu options.
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h3 class="accordion-header" id="faqFour">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
              What is your recipe?
            </button>
          </h3>
          <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="faqFour" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              Our recipes are family secrets passed down through generations. While we can't share the exact formulas, we can tell you that we use only the freshest, highest-quality ingredients and prepare everything with care and attention to detail.
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php
// Include footer
include_once $_SERVER['DOCUMENT_ROOT'] . '/lillies-food-shop/Layout/Footer/footer.php';
?>

<!-- Add Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>