<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php?redirect=orders");
    exit;
}

// Include CSS files
echo '<link rel="stylesheet" href="styles/styles.css">';
echo '<link rel="stylesheet" href="styles/orders.css">';

// Include navigation
include_once 'Layout/Navigation/navigation.php';

// In a real application, these would be fetched from a database
// Dummy orders for demonstration
$orders = [
    [
        "id" => "LFS-A7F3D961",
        "date" => "2023-07-12",
        "status" => "Delivered",
        "total" => 385.00,
        "items" => [
            ["name" => "Classic Cheeseburger", "quantity" => 2, "price" => 149.00],
            ["name" => "French Fries", "quantity" => 1, "price" => 69.00],
            ["name" => "Coke (Can)", "quantity" => 1, "price" => 45.00]
        ]
    ],
    [
        "id" => "LFS-B9E1C472",
        "date" => "2023-08-23",
        "status" => "Processing",
        "total" => 436.00,
        "items" => [
            ["name" => "Bacon BBQ Burger", "quantity" => 1, "price" => 179.00],
            ["name" => "Onion Rings", "quantity" => 2, "price" => 79.00],
            ["name" => "Iced Tea", "quantity" => 3, "price" => 39.00]
        ]
    ]
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | Lillies Food Shop</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">My Orders</h2>
            
            <?php if (empty($orders)): ?>
                <div class="alert alert-info text-center p-5">
                    <i class="bi bi-bag mb-3" style="font-size: 3rem;"></i>
                    <h4>No Orders Yet</h4>
                    <p class="mb-4">You haven't placed any orders yet.</p>
                    <a href="Pages/menu.php" class="btn btn-primary">Browse Menu</a>
                </div>
            <?php else: ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table orders-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><strong><?php echo $order["id"]; ?></strong></td>
                                            <td><?php echo date("M j, Y", strtotime($order["date"])); ?></td>
                                            <td>
                                                <?php 
                                                    $itemCount = count($order["items"]);
                                                    echo $itemCount . " item" . ($itemCount > 1 ? "s" : ""); 
                                                ?>
                                            </td>
                                            <td>₱<?php echo number_format($order["total"], 2); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo strtolower($order["status"]); ?>">
                                                    <?php echo $order["status"]; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary view-order-btn" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#orderDetailsModal" 
                                                        data-order-id="<?php echo $order["id"]; ?>">
                                                    View Details
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="orderDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" class="btn btn-primary" id="reorderBtn">Reorder</a>
            </div>
        </div>
    </div>
</div>

<?php include 'Layout/Footer/footer.php'; ?>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Store orders data in JavaScript
        const orders = <?php echo json_encode($orders); ?>;
        
        // Handle View Details button clicks
        const viewOrderBtns = document.querySelectorAll('.view-order-btn');
        const orderDetailsContent = document.getElementById('orderDetailsContent');
        const reorderBtn = document.getElementById('reorderBtn');
        
        viewOrderBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const orderId = this.getAttribute('data-order-id');
                const order = orders.find(o => o.id === orderId);
                
                if (order) {
                    let content = `
                        <div class="order-header mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Order ID:</strong> ${order.id}</p>
                                    <p><strong>Date:</strong> ${new Date(order.date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <p><strong>Status:</strong> <span class="status-badge status-${order.status.toLowerCase()}">${order.status}</span></p>
                                    <p><strong>Total:</strong> ₱${order.total.toFixed(2)}</p>
                                </div>
                            </div>
                        </div>
                        <div class="order-items">
                            <h6 class="mb-3">Order Items</h6>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-end">Price</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                    `;
                    
                    order.items.forEach(item => {
                        const subtotal = item.price * item.quantity;
                        content += `
                            <tr>
                                <td>${item.name}</td>
                                <td class="text-center">${item.quantity}</td>
                                <td class="text-end">₱${item.price.toFixed(2)}</td>
                                <td class="text-end">₱${subtotal.toFixed(2)}</td>
                            </tr>
                        `;
                    });
                    
                    content += `
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                            <td class="text-end"><strong>₱${order.total.toFixed(2)}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    `;
                    
                    orderDetailsContent.innerHTML = content;
                    
                    // Setup reorder button
                    reorderBtn.onclick = function() {
                        reorderItems(order.items);
                        return false;
                    };
                }
            });
        });
        
        // Function to reorder items
        function reorderItems(items) {
            // Get current cart
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            // Add items to cart
            items.forEach(item => {
                const existingItemIndex = cart.findIndex(cartItem => cartItem.name === item.name);
                
                if (existingItemIndex > -1) {
                    // Update quantity if item exists
                    cart[existingItemIndex].quantity += item.quantity;
                } else {
                    // Add new item
                    cart.push({
                        name: item.name,
                        price: item.price,
                        quantity: item.quantity,
                        // Since we don't have all the details, we'll use placeholder data
                        image: 'https://via.placeholder.com/80x80',
                        category: 'Reorder'
                    });
                }
            });
            
            // Save cart
            localStorage.setItem('cart', JSON.stringify(cart));
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('orderDetailsModal'));
            modal.hide();
            
            // Show confirmation
            alert('Items have been added to your cart!');
            
            // Redirect to checkout
            window.location.href = 'checkout.php';
        }
    });
</script>

</body>
</html> 