<!-- Footer --><footer class="footer">  <div class="container">
    <div class="row py-4">
      <div class="col-md-4">
        <h5 class="mb-3">Lillies <span class="text-primary">Food</span> Shop</h5>
        <p>Delicious food delivered to your doorstep.</p>
      </div>
      <div class="col-md-4 text-center">
        <ul class="list-unstyled">
          <li><a href="about.php">ABOUT</a></li>
          <li><a href="#">ORDER NOW</a></li>
          <li><a href="menu.php">MENU</a></li>
          <li><a href="#">RATE US</a></li>
          <?php if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] === true): ?>
            <li><a href="admin/dashboard.php" class="text-primary">ADMIN DASHBOARD</a></li>
          <?php endif; ?>
        </ul>
      </div>
      <div class="col-md-4 text-md-end">
        <p>CONTACT US: <a href="mailto:hello@lilliesfoodshop.com">hello@lilliesfoodshop.com</a></p>
      </div>
    </div>
    <hr>
    <div class="row py-2">
      <div class="col-md-6">
        <ul class="list-inline mb-0">
          <li class="list-inline-item"><a href="#">TERMS OF SERVICE</a></li>
          <li class="list-inline-item"><a href="#">PRIVACY POLICY</a></li>
          <li class="list-inline-item"><a href="#">COOKIES</a></li>
        </ul>
      </div>
      <div class="col-md-6 text-md-end">
        <p class="mb-0">&copy; 2024, Lillies Food Shop All Rights Reserved.</p>
      </div>
    </div>
  </div>
</footer>

<style>
  .footer {
    background-color: #fdf6dd;
    padding: 1rem 0;
    font-size: 0.9rem;
    color: #333;
  }
  
  .footer a {
    color: #333;
    text-decoration: none;
    font-weight: 600;
  }
  
  .footer a:hover {
    color: #0078ff;
  }
  
  .footer hr {
    border-top: 1px solid #ddd;
  }
  
  .footer ul li {
    margin-bottom: 0.5rem;
  }
</style>
