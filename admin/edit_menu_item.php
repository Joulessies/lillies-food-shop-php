<?php
// Start session and include database connection
session_start();
require_once '../config/db_connect.php';

// Check if user is logged in as admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] !== true) {
    header("location: ../login.php");
    exit;
}

// Initialize variables
$item_id = $name = $description = $price = $category_id = "";
$is_featured = $is_available = 0;
$image_path = "";
$name_err = $price_err = $category_err = "";
$success_message = $error_message = "";

// Check if id parameter exists
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    // Get URL parameter
    $item_id = trim($_GET["id"]);
    
    // Prepare a select statement
    $sql = "SELECT * FROM menu_items WHERE id = ?";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $item_id);
        
        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) == 1) {
                // Fetch result row as an associative array
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                
                // Retrieve individual field value
                $name = $row["name"];
                $description = $row["description"];
                $price = $row["price"];
                $image_path = $row["image"];
                $category_id = isset($row["category_id"]) ? $row["category_id"] : "";
                
                // Check if the is_featured column exists in the table
                $is_featured = isset($row["is_featured"]) ? $row["is_featured"] : 0;
                
                // Check if the is_available column exists in the table
                $is_available = isset($row["is_available"]) ? $row["is_available"] : 1;
            } else {
                // No valid id parameter
                header("location: menu_management.php");
                exit();
            }
            
        } else {
            $error_message = "Oops! Something went wrong. Please try again later.";
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
    }
} else {
    // No valid id parameter
    header("location: menu_management.php");
    exit();
}

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter a name.";
    } else {
        $name = trim($_POST["name"]);
    }
    
    // Validate price
    if (empty(trim($_POST["price"]))) {
        $price_err = "Please enter a price.";
    } elseif (!is_numeric($_POST["price"]) || $_POST["price"] <= 0) {
        $price_err = "Please enter a valid price.";
    } else {
        $price = trim($_POST["price"]);
    }
    
    // Validate category
    if (empty(trim($_POST["category_id"]))) {
        $category_err = "Please select a category.";
    } else {
        $category_id = trim($_POST["category_id"]);
    }
    
    // Get other form data
    $description = trim($_POST["description"]);
    $is_featured = isset($_POST["is_featured"]) ? 1 : 0;
    $is_available = isset($_POST["is_available"]) ? 1 : 0;
    
    // Handling image upload if there is a new image
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_dir = "../assets/images/";
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = "assets/images/" . $new_filename;
        } else {
            $error_message = "Error uploading image.";
        }
    }
    
    // Check input errors before updating
    if (empty($name_err) && empty($price_err) && empty($category_err) && empty($error_message)) {
        // Prepare an update statement
        $sql = "UPDATE menu_items SET name = ?, description = ?, price = ?, image = ?, category_id = ?, is_featured = ?, is_available = ? WHERE id = ?";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssdsiiii", $name, $description, $price, $image_path, $category_id, $is_featured, $is_available, $item_id);
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Menu item updated successfully!";
            } else {
                $error_message = "Error updating menu item: " . mysqli_error($conn);
            }
            
            // Close statement
            mysqli_stmt_close($stmt);
        }
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu Item | Lillies Food Shop</title>
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
        
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 5px;
            margin-top: 10px;
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
        <h1 class="mb-4">Edit Menu Item</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <?= $success_message ?>
                <a href="menu_management.php" class="alert-link">Back to Menu Management</a>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Edit Menu Item</h5>
            </div>
            <div class="card-body">
                <form action="<?= htmlspecialchars($_SERVER["REQUEST_URI"]) ?>" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select <?= !empty($category_err) ? 'is-invalid' : ''; ?>" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= ($category_id == $category['id']) ? 'selected' : '' ?>>
                                        <?= $category['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="invalid-feedback"><?= $category_err ?></span>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Item Name</label>
                            <input type="text" class="form-control <?= !empty($name_err) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?= $name ?>" required>
                            <span class="invalid-feedback"><?= $name_err ?></span>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= $description ?></textarea>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="price" class="form-label">Price (PHP)</label>
                            <input type="number" class="form-control <?= !empty($price_err) ? 'is-invalid' : ''; ?>" id="price" name="price" step="0.01" value="<?= $price ?>" required>
                            <span class="invalid-feedback"><?= $price_err ?></span>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="image" class="form-label">Image (Leave empty to keep current)</label>
                            <input type="file" class="form-control" id="image" name="image">
                            <?php if (!empty($image_path)): ?>
                                <div class="mt-2">
                                    <label>Current Image:</label>
                                    <img src="../<?= $image_path ?>" class="preview-image" alt="<?= $name ?>">
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-4 mb-3 d-flex align-items-end">
                            <div class="form-check me-4">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" <?= $is_featured ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_featured">
                                    Featured Item
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_available" name="is_available" <?= $is_available ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_available">
                                    Available
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="menu_management.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Menu Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 