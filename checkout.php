<?php
// Start session
session_start();

// For testing purposes, create a test session if not logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // TEMPORARY CODE FOR DEMO - Remove in production
    $_SESSION["loggedin"] = true;
    $_SESSION["name"] = "Julius San Jose";
    $_SESSION["email"] = "cjcsanjose@tip.edu.ph";
    $_SESSION["id"] = 1;
    // In production, this would redirect to login:
    // header("location: login.php?redirect=checkout");
    // exit;
}

// Include CSS files
echo '<link rel="stylesheet" href="styles/styles.css">';
echo '<link rel="stylesheet" href="styles/checkout.css">';

// Include navigation
include_once 'Layout/Navigation/navigation.php';

// Process form submission
$orderPlaced = false;
$orderError = false;
$orderId = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process checkout form submission
    $fullName = trim($_POST["fullName"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $city = trim($_POST["city"]);
    $zipCode = trim($_POST["zipCode"]);
    $paymentMethod = trim($_POST["paymentMethod"]);
    
    // Validate form data
    if (empty($fullName) || empty($email) || empty($phone) || empty($address) || 
        empty($city) || empty($zipCode) || empty($paymentMethod)) {
        $orderError = "Please fill out all required fields.";
    } else {
        // Process the order (in a real application, this would involve database operations)
        $orderId = "LFS-" . strtoupper(substr(md5(uniqid()), 0, 8));
        $orderPlaced = true;
        
        // Clear the cart (typically you'd save it to the database first)
        echo '<script>localStorage.setItem("cart", JSON.stringify([]));</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Lillies Food Shop</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>

<?php if ($orderPlaced): ?>
    <!-- Order Success Page -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                        </div>
                        <h2 class="mb-4">Order Placed Successfully!</h2>
                        <p class="mb-3">Thank you for your order. Your order has been received and is being processed.</p>
                        <div class="order-details mt-4 mb-4">
                            <h5>Order Details</h5>
                            <div class="row mt-3">
                                <div class="col-md-6 text-md-end">Order Number:</div>
                                <div class="col-md-6 text-md-start"><strong><?php echo $orderId; ?></strong></div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6 text-md-end">Date:</div>
                                <div class="col-md-6 text-md-start"><strong><?php echo date("F j, Y"); ?></strong></div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6 text-md-end">Payment Method:</div>
                                <div class="col-md-6 text-md-start"><strong><?php echo $paymentMethod; ?></strong></div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="index.php" class="btn btn-primary">Return to Home Page</a>
                            <a href="orders.php" class="btn btn-outline-primary ms-2">View My Orders</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Checkout Page -->
    <div class="container my-5">
        <h2 class="mb-4">Checkout</h2>
        
        <?php if ($orderError): ?>
            <div class="alert alert-danger"><?php echo $orderError; ?></div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Order Summary -->
            <div class="col-md-4 order-md-2 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Order Summary</h4>
                    </div>
                    <div class="card-body">
                        <div id="orderSummary">
                            <p class="text-center text-muted">Loading order details...</p>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <span id="subtotal">₱0.00</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Delivery Fee:</span>
                            <span id="deliveryFee">₱50.00</span>
                        </div>
                        <div class="d-flex justify-content-between mt-3">
                            <strong>Total:</strong>
                            <strong id="totalAmount">₱0.00</strong>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Have a Voucher?</h5>
                    </div>
                    <div class="card-body">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Enter voucher code">
                            <button class="btn btn-outline-primary" type="button">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Checkout Form -->
            <div class="col-md-8 order-md-1">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <form id="checkoutForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <h4 class="mb-3">Shipping Information</h4>
                            
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="fullName" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="fullName" name="fullName" 
                                        value="<?php echo isset($_SESSION["name"]) ? htmlspecialchars($_SESSION["name"]) : ''; ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                        value="<?php echo isset($_SESSION["email"]) ? htmlspecialchars($_SESSION["email"]) : ''; ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="(+63) ..." required>
                                </div>
                                
                                <div class="col-12">
                                    <label for="address" class="form-label">Delivery Address</label>
                                    <input type="text" class="form-control" id="address" name="address" placeholder="House/Unit No., Street, Barangay" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city" placeholder="City/Municipality" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="zipCode" class="form-label">ZIP Code</label>
                                    <input type="text" class="form-control" id="zipCode" name="zipCode" placeholder="e.g. 1000" required>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <h4 class="mb-3">Delivery Options</h4>
                            <div class="my-3">
                                <div class="form-check">
                                    <input id="standardDelivery" name="deliveryOption" type="radio" class="form-check-input" checked required>
                                    <label class="form-check-label" for="standardDelivery">Standard Delivery (₱50) <small class="text-muted">- 2-3 business days</small></label>
                                </div>
                                <div class="form-check">
                                    <input id="expressDelivery" name="deliveryOption" type="radio" class="form-check-input" required>
                                    <label class="form-check-label" for="expressDelivery">Express Delivery (₱100) <small class="text-muted">- Same day delivery</small></label>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <h4 class="mb-3">Payment Method</h4>
                            <div class="my-3">
                                <div class="form-check">
                                    <input id="cashOnDelivery" name="paymentMethod" type="radio" class="form-check-input" value="Cash on Delivery" checked required>
                                    <label class="form-check-label" for="cashOnDelivery">Cash on Delivery</label>
                                </div>
                                <div class="form-check">
                                    <input id="creditCard" name="paymentMethod" type="radio" class="form-check-input" value="Credit Card" required>
                                    <label class="form-check-label" for="creditCard">Credit Card</label>
                                </div>
                                <div class="form-check">
                                    <input id="gcash" name="paymentMethod" type="radio" class="form-check-input" value="GCash" required>
                                    <label class="form-check-label" for="gcash">GCash</label>
                                </div>
                            </div>
                            
                            <div id="creditCardForm" class="d-none">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="cardName" class="form-label">Name on Card</label>
                                        <input type="text" class="form-control" id="cardName" placeholder="Full name as displayed on card">
                                    </div>
                                    
                                    <div class="col-12">
                                        <label for="cardNumber" class="form-label">Card Number</label>
                                        <input type="text" class="form-control" id="cardNumber" placeholder="XXXX XXXX XXXX XXXX">
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="expiryDate" class="form-label">Expiry Date</label>
                                        <input type="text" class="form-control" id="expiryDate" placeholder="MM/YY">
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="cvv" class="form-label">CVV</label>
                                        <input type="text" class="form-control" id="cvv" placeholder="XXX">
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <div class="form-check mb-4">
                                <input type="checkbox" class="form-check-input" id="saveInfo">
                                <label class="form-check-label" for="saveInfo">Save this delivery information for next time</label>
                            </div>
                            
                            <button id="placeOrderBtn" class="w-100 btn btn-primary btn-lg" type="submit">Place Order</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include 'Layout/Footer/footer.php'; ?>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Payment method toggle
        const paymentRadios = document.querySelectorAll('input[name="paymentMethod"]');
        const creditCardForm = document.getElementById('creditCardForm');
        
        paymentRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.id === 'creditCard') {
                    creditCardForm.classList.remove('d-none');
                } else {
                    creditCardForm.classList.add('d-none');
                }
            });
        });
        
        // Delivery option change
        const deliveryOptions = document.querySelectorAll('input[name="deliveryOption"]');
        const deliveryFeeElement = document.getElementById('deliveryFee');
        let deliveryFee = 50; // Default delivery fee
        
        deliveryOptions.forEach(option => {
            option.addEventListener('change', function() {
                if (this.id === 'expressDelivery') {
                    deliveryFee = 100;
                    deliveryFeeElement.textContent = '₱100.00';
                } else {
                    deliveryFee = 50;
                    deliveryFeeElement.textContent = '₱50.00';
                }
                updateTotal();
            });
        });
        
        // Load cart from localStorage
        function loadCart() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const orderSummary = document.getElementById('orderSummary');
            const subtotalElement = document.getElementById('subtotal');
            
            if (cart.length === 0) {
                orderSummary.innerHTML = `
                    <div class="text-center py-4">
                        <i class="bi bi-cart-x mb-3" style="font-size: 2rem; color: #ccc;"></i>
                        <p>Your cart is empty.</p>
                        <a href="Pages/menu.php" class="btn btn-outline-primary btn-sm">Browse Menu</a>
                    </div>
                `;
                subtotalElement.textContent = '₱0.00';
                updateTotal();
                
                // Disable the place order button
                document.getElementById('placeOrderBtn').disabled = true;
                return;
            }
            
            let summaryHTML = '<ul class="list-group list-group-flush">';
            let subtotal = 0;
            
            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                
                summaryHTML += `
                    <li class="list-group-item d-flex justify-content-between lh-sm py-3">
                        <div>
                            <h6 class="my-0">${item.name}</h6>
                            <small class="text-muted">₱${item.price.toFixed(2)} x ${item.quantity}</small>
                        </div>
                        <span>₱${itemTotal.toFixed(2)}</span>
                    </li>
                `;
            });
            
            summaryHTML += '</ul>';
            orderSummary.innerHTML = summaryHTML;
            subtotalElement.textContent = `₱${subtotal.toFixed(2)}`;
            updateTotal();
        }
        
        // Update total amount
        function updateTotal() {
            const subtotalText = document.getElementById('subtotal').textContent;
            const subtotal = parseFloat(subtotalText.replace('₱', ''));
            
            const total = subtotal + deliveryFee;
            document.getElementById('totalAmount').textContent = `₱${total.toFixed(2)}`;
        }
        
        // Initialize the page
        loadCart();
        
        // Form validation
        const checkoutForm = document.getElementById('checkoutForm');
        if (checkoutForm) {
            checkoutForm.addEventListener('submit', function(e) {
                const cart = JSON.parse(localStorage.getItem('cart')) || [];
                
                if (cart.length === 0) {
                    e.preventDefault();
                    alert('Your cart is empty. Please add items to your cart before checking out.');
                    return false;
                }
                
                return true;
            });
        }
    });
</script>

</body>
</html> 