<?php
// Include CSS files
echo '<link rel="stylesheet" href="../styles/styles.css">';
echo '<link rel="stylesheet" href="../styles/contact.css">';

// Include Bootstrap
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';
echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">';

// Include navigation with correct path
include_once $_SERVER['DOCUMENT_ROOT'] . '/lillies-food-shop/Layout/Navigation/navigation.php';
?>

<!-- Contact Page Content -->
<main>
  <section class="contact-hero-section">
    <div class="container py-5 text-center">
      <h1 class="display-3 fw-bold">Contact Us</h1>
      <p class="lead">We'd love to hear from you</p>
    </div>
  </section>
  
  <section class="contact-content-section">
    <div class="container py-5">
      <div class="row">
        <div class="col-md-6">
          <h2>Get in Touch</h2>
          <p>Please fill out the form and our team will get back to you within 24 hours.</p>
          
          <form class="contact-form mt-4">
            <div class="mb-3">
              <label for="name" class="form-label">Your Name</label>
              <input type="text" class="form-control" id="name" placeholder="Enter your name">
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" class="form-control" id="email" placeholder="Enter your email">
            </div>
            <div class="mb-3">
              <label for="subject" class="form-label">Subject</label>
              <input type="text" class="form-control" id="subject" placeholder="What is this regarding?">
            </div>
            <div class="mb-3">
              <label for="message" class="form-label">Message</label>
              <textarea class="form-control" id="message" rows="5" placeholder="Your message"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Message</button>
          </form>
        </div>
        
        <div class="col-md-6 mt-4 mt-md-0">
          <h2>Contact Information</h2>
          <div class="contact-info mt-4">
            <div class="d-flex mb-4">
              <div class="icon-wrapper me-3">
                <i class="bi bi-geo-alt-fill"></i>
              </div>
              <div>
                <h5>Address</h5>
                <p>123 Main Street, Cityville, State 12345</p>
              </div>
            </div>
            
            <div class="d-flex mb-4">
              <div class="icon-wrapper me-3">
                <i class="bi bi-telephone-fill"></i>
              </div>
              <div>
                <h5>Phone</h5>
                <p>(123) 456-7890</p>
              </div>
            </div>
            
            <div class="d-flex mb-4">
              <div class="icon-wrapper me-3">
                <i class="bi bi-envelope-fill"></i>
              </div>
              <div>
                <h5>Email</h5>
                <p>hello@lilliesfoodshop.com</p>
              </div>
            </div>
            
            <div class="d-flex">
              <div class="icon-wrapper me-3">
                <i class="bi bi-clock-fill"></i>
              </div>
              <div>
                <h5>Hours</h5>
                <p>Monday - Friday: 9:00 AM - 10:00 PM<br>
                Saturday - Sunday: 10:00 AM - 11:00 PM</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- Add Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> 