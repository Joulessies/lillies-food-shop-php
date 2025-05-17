<?php
// Start session and include database connection
session_start();
require_once '../config/db_connect.php';

// Check if user is logged in as admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] !== true) {
    header("location: index.php");
    exit;
}

// Get statistics for dashboard
// Count total users
$users_count = 0;
$sql = "SELECT COUNT(*) as total FROM users WHERE is_admin = 0";
$result = mysqli_query($conn, $sql);
if ($result && $row = mysqli_fetch_assoc($result)) {
    $users_count = $row['total'];
}

// Count total menu items
$menu_items_count = 0;
$sql = "SELECT COUNT(*) as total FROM menu_items";
$result = mysqli_query($conn, $sql);
if ($result && $row = mysqli_fetch_assoc($result)) {
    $menu_items_count = $row['total'];
}

// Count total orders
$orders_count = 0;
$sql = "SELECT COUNT(*) as total FROM orders";
$result = mysqli_query($conn, $sql);
if ($result && $row = mysqli_fetch_assoc($result)) {
    $orders_count = $row['total'];
}

// Count total revenue
$total_revenue = 0;
$sql = "SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed'";
$result = mysqli_query($conn, $sql);
if ($result && $row = mysqli_fetch_assoc($result)) {
    $total_revenue = $row['total'] ? $row['total'] : 0;
}

// Get recent orders (last 5)
$recent_orders = [];
$sql = "SELECT o.id, o.total_amount, o.status, o.created_at, u.name as user_name 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC LIMIT 5";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $recent_orders[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Lillies Food Shop</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .admin-container {
            padding: 2rem 0;
            margin-top: 80px;
        }
        
        .card {
            margin-bottom: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border: none;
        }
        
        .card-stats {
            transition: all 0.3s ease;
        }
        
        .card-stats:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
        }
        
        .bg-blue {
            background-color: rgba(0, 120, 255, 0.1);
            color: #0078ff;
        }
        
        .bg-green {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .bg-orange {
            background-color: rgba(255, 153, 0, 0.1);
            color: #ff9900;
        }
        
        .bg-red {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        
        .stats-title {
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }
        
        .stats-value {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        
        .welcome-text {
            font-size: 1.2rem;
            color: #6c757d;
        }
        
        .status-badge {
            padding: 0.35em 0.65em;
            border-radius: 0.5rem;
            font-size: 0.75em;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }
        
        .status-processing {
            background-color: rgba(0, 123, 255, 0.1);
            color: #007bff;
        }
        
        .status-completed {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .status-cancelled {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
    </style>
</head>
<body>
    <!-- Admin Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Lillies Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="menu_management.php">Menu Items</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="user_management.php">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">Orders</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <span class="text-white me-3"><?php echo htmlspecialchars($_SESSION["name"]); ?></span>
                    <a href="../logout.php" class="btn btn-light btn-sm">Log Out</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Admin Content -->
    <div class="container admin-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-2">Admin Dashboard</h1>
                <p class="welcome-text mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION["name"]); ?>!</p>
            </div>
            <a href="../index.php" class="btn btn-outline-primary" target="_blank">View Website</a>
        </div>
        
        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card card-stats">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center">
                            <div class="stats-icon bg-blue">
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                        <h5 class="stats-title">TOTAL USERS</h5>
                        <p class="stats-value"><?php echo $users_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center">
                            <div class="stats-icon bg-green">
                                <i class="bi bi-egg-fried"></i>
                            </div>
                        </div>
                        <h5 class="stats-title">MENU ITEMS</h5>
                        <p class="stats-value"><?php echo $menu_items_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center">
                            <div class="stats-icon bg-orange">
                                <i class="bi bi-basket"></i>
                            </div>
                        </div>
                        <h5 class="stats-title">TOTAL ORDERS</h5>
                        <p class="stats-value"><?php echo $orders_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center">
                            <div class="stats-icon bg-red">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                        </div>
                        <h5 class="stats-title">TOTAL REVENUE</h5>
                        <p class="stats-value">₱<?php echo number_format($total_revenue, 2); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="card">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Orders</h5>
                    <a href="orders.php" class="btn btn-sm btn-primary">View All</a>
                </div>
            </div>
            <div class="card-body">
                <?php if (count($recent_orders) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['user_name'] ?? 'Guest'); ?></td>
                                <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <?php 
                                    $status_class = '';
                                    switch ($order['status']) {
                                        case 'pending':
                                            $status_class = 'status-pending';
                                            break;
                                        case 'processing':
                                            $status_class = 'status-processing';
                                            break;
                                        case 'completed':
                                            $status_class = 'status-completed';
                                            break;
                                        case 'cancelled':
                                            $status_class = 'status-cancelled';
                                            break;
                                    }
                                    ?>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-center mb-0">No orders yet.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Quick Links -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="menu_management.php" class="btn btn-outline-primary w-100">
                            <i class="bi bi-plus-circle me-2"></i> Add Menu Item
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="user_management.php" class="btn btn-outline-primary w-100">
                            <i class="bi bi-people me-2"></i> Manage Users
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="orders.php" class="btn btn-outline-primary w-100">
                            <i class="bi bi-basket me-2"></i> Process Orders
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="../index.php" class="btn btn-outline-primary w-100" target="_blank">
                            <i class="bi bi-house me-2"></i> View Website
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 