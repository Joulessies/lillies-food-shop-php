<?php
// Start session and include database connection
session_start();
require_once '../config/db_connect.php';

// Check if user is logged in as admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

// Process form data when adding a new menu item
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_item"])) {
    $category_id = $_POST["category_id"];
    $name = $_POST["name"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $is_featured = isset($_POST["is_featured"]) ? 1 : 0;
    
    // Handling image upload
    $image_path = "";
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_dir = "../assets/images/";
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = "assets/images/" . $new_filename;
        }
    }
    
    // Insert into database
    $sql = "INSERT INTO menu_items (category_id, name, description, price, image, is_featured) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "issdsi", $category_id, $name, $description, $price, $image_path, $is_featured);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Menu item added successfully!";
        } else {
            $error_message = "Error adding menu item: " . mysqli_error($conn);
        }
        
        mysqli_stmt_close($stmt);
    }
}

// Process form data when deleting a menu item
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_item"])) {
    $item_id = $_POST["item_id"];
    
    $sql = "DELETE FROM menu_items WHERE id = ?";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $item_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Menu item deleted successfully!";
        } else {
            $error_message = "Error deleting menu item: " . mysqli_error($conn);
        }
        
        mysqli_stmt_close($stmt);
    }
}

// Get all food categories
$categories = [];
$sql = "SELECT * FROM food_categories ORDER BY name";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
}

// Get all menu items
$menu_items = [];
$sql = "SELECT m.*, c.name as category_name 
        FROM menu_items m 
        LEFT JOIN food_categories c ON m.category_id = c.id 
        ORDER BY c.name, m.name";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $menu_items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management | Lillies Food Shop</title>
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
        .admin-container {
            padding: 2rem 0;
            margin-top: 80px;
        }
        
        .card {
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .menu-item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Lillies Food Shop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="menu_management.php">Menu Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">Orders</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="../logout.php" class="btn btn-light">Log Out</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container admin-container">
        <h1 class="mb-4">Menu Management</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?= $success_message ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>
        
        <!-- Add New Menu Item Form -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Add New Menu Item</h5>
            </div>
            <div class="card-body">
                <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Item Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="price" class="form-label">Price (PHP)</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" class="form-control" id="image" name="image">
                        </div>
                        
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured">
                                <label class="form-check-label" for="is_featured">
                                    Featured Item
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" name="add_item" class="btn btn-primary">Add Item</button>
                </form>
            </div>
        </div>
        
        <!-- Menu Items Table -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Menu Items</h5>
            </div>
            <div class="card-body">
                <?php if (empty($menu_items)): ?>
                    <p class="text-center">No menu items found. Add some items using the form above.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Featured</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($menu_items as $item): ?>
                                    <tr>
                                        <td>
                                            <?php if ($item['image']): ?>
                                                <img src="../<?= $item['image'] ?>" class="menu-item-image" alt="<?= $item['name'] ?>">
                                            <?php else: ?>
                                                <div class="bg-light text-center menu-item-image d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $item['name'] ?></td>
                                        <td><?= $item['category_name'] ?></td>
                                        <td>₱<?= number_format($item['price'], 2) ?></td>
                                        <td>
                                            <?= $item['is_featured'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="edit_menu_item.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                    Edit
                                                </a>
                                                <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                                    <button type="submit" name="delete_item" class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 