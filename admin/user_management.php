<?php
// Start session and include database connection
session_start();
require_once '../config/db_connect.php';

// Explicitly select the database
if (!mysqli_select_db($conn, 'lillies_food_shop')) {
    die("Error: Could not select database. " . mysqli_error($conn));
}

// Check if user is logged in as admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] !== true) {
    header("location: index.php");
    exit;
}

// Process user deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_user"])) {
    $user_id = $_POST["user_id"];
    
    // Don't allow admin to delete themselves
    if ($user_id == $_SESSION["id"]) {
        $error_message = "You cannot delete your own admin account.";
    } else {
        $sql = "DELETE FROM users WHERE id = ? AND is_admin = 0";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $success_message = "User deleted successfully!";
            } else {
                $error_message = "Error deleting user: " . mysqli_error($conn);
            }
            
            mysqli_stmt_close($stmt);
        }
    }
}

// Process test user creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_test_user"])) {
    // Generate random test user
    $rand = rand(1000, 9999);
    $test_name = "Test User " . $rand;
    $test_email = "testuser" . $rand . "@example.com";
    $test_password = password_hash("password123", PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, 0)";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "sss", $test_name, $test_email, $test_password);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Test user created successfully! Email: " . $test_email;
        } else {
            $error_message = "Error creating test user: " . mysqli_stmt_error($stmt);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Error preparing statement: " . mysqli_error($conn);
    }
}

// Pagination variables
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;
$offset = ($page - 1) * $records_per_page;

// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_condition = '';
if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $search_condition = " AND (name LIKE '%{$search}%' OR email LIKE '%{$search}%')";
}

// Get total number of users (excluding admins)
$total_users = 0;
$sql = "SELECT COUNT(*) as total FROM users WHERE is_admin = 0" . $search_condition;
$result = mysqli_query($conn, $sql);
if ($result && $row = mysqli_fetch_assoc($result)) {
    $total_users = $row['total'];
}
$total_pages = ceil($total_users / $records_per_page);

// Get users with pagination
$users = [];
$sql = "SELECT id, name, email, created_at FROM users WHERE is_admin = 0" . $search_condition . " ORDER BY created_at DESC LIMIT ?, ?";

// Try alternative approach without prepared statement for pagination
$sql_alt = "SELECT id, name, email, created_at FROM users WHERE is_admin = 0" . $search_condition . " ORDER BY created_at DESC LIMIT $offset, $records_per_page";
$direct_query_result = mysqli_query($conn, $sql_alt);
if ($direct_query_result) {
    while ($row = mysqli_fetch_assoc($direct_query_result)) {
        $users[] = $row;
    }
} else {
    $error_message = "Error with direct query: " . mysqli_error($conn);
}

// Original prepared statement approach as fallback
if (empty($users) && $stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "ii", $offset, $records_per_page);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    } else {
        $error_message = "Error executing query: " . mysqli_stmt_error($stmt);
    }
    
    mysqli_stmt_close($stmt);
} else if (empty($users)) {
    $error_message = "Error preparing statement: " . mysqli_error($conn);
}

// Debug database connection
if (mysqli_connect_errno()) {
    $error_message = "Failed to connect to MySQL: " . mysqli_connect_error();
}

// Check if users table exists
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
if (mysqli_num_rows($table_check) == 0) {
    $error_message = "Users table does not exist in the database!";
    
    // Try to create the users table
    $create_table_sql = "CREATE TABLE users (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        is_admin TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (mysqli_query($conn, $create_table_sql)) {
        $success_message = "Users table created successfully. Please add users to see them listed.";
        
        // Create a default admin user
        $admin_password = password_hash("admin123", PASSWORD_DEFAULT);
        $insert_admin = "INSERT INTO users (name, email, password, is_admin) VALUES ('Admin', 'admin@example.com', '$admin_password', 1)";
        if (mysqli_query($conn, $insert_admin)) {
            $success_message .= " Default admin created: admin@example.com / admin123";
        }
    } else {
        $error_message .= " Failed to create table: " . mysqli_error($conn);
    }
} else {
    // Check table structure
    $column_check = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'id'");
    if (mysqli_num_rows($column_check) == 0) {
        $error_message = "Users table structure is incorrect. Missing 'id' column.";
    }
    
    // Check if any users exist at all
    $all_users_check = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
    if ($row = mysqli_fetch_assoc($all_users_check)) {
        $debug_info = "Total users (including admins): " . $row['total'];
    }
    
    // Debug SQL query directly
    $debug_query = "SELECT id, name, email, created_at FROM users WHERE is_admin = 0" . $search_condition . " ORDER BY created_at DESC LIMIT 0, 10";
    $direct_result = mysqli_query($conn, $debug_query);
    $debug_info .= "<br>Direct query results: " . mysqli_num_rows($direct_result) . " rows";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management | Lillies Food Shop</title>
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
        
        .user-image {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e9ecef;
            color: #6c757d;
            border-radius: 50%;
            font-weight: 600;
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
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="menu_management.php">Menu Items</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="user_management.php">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_admins.php">Admins</a>
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
            <h1>User Management</h1>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($debug_info)): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?php echo $debug_info; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="card mb-4">
            <div class="card-body">
                <form action="user_management.php" method="get" class="row g-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search me-1"></i> Search
                            </button>
                        </div>
                    </div>
                    <?php if (!empty($search)): ?>
                    <div class="col-md-4">
                        <a href="user_management.php" class="btn btn-secondary">Clear Search</a>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Users List</h5>
                    <span class="badge bg-primary rounded-pill"><?php echo $total_users; ?> Users</span>
                </div>
            </div>
            <div class="card-body">
                <?php if (count($users) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-image me-3">
                                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                        </div>
                                        <span><?php echo htmlspecialchars($user['name']); ?></span>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="delete_user" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash me-1"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center mt-4">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo ($page - 1); ?><?php echo (!empty($search)) ? '&search=' . urlencode($search) : ''; ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?><?php echo (!empty($search)) ? '&search=' . urlencode($search) : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo ($page + 1); ?><?php echo (!empty($search)) ? '&search=' . urlencode($search) : ''; ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
                
                <?php else: ?>
                <div class="text-center py-4">
                    <i class="bi bi-people" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="mt-3">No users found.</p>
                    <?php if (!empty($search)): ?>
                    <a href="user_management.php" class="btn btn-primary">Clear Search</a>
                    <?php endif; ?>
                    
                    <!-- Add Test User Button -->
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="mt-3">
                        <button type="submit" name="add_test_user" class="btn btn-success">
                            <i class="bi bi-person-plus me-1"></i> Create Test User
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 