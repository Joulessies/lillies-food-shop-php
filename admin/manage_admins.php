<?php
// Start session and include database connection
session_start();
require_once '../config/db_connect.php';

// Check if user is logged in as admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] !== true) {
    header("location: index.php");
    exit;
}

// Process admin promotion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["promote_to_admin"])) {
    $user_id = $_POST["user_id"];
    
    // Update user to admin
    $sql = "UPDATE users SET is_admin = 1 WHERE id = ?";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "User promoted to admin successfully!";
        } else {
            $error_message = "Error promoting user: " . mysqli_error($conn);
        }
        
        mysqli_stmt_close($stmt);
    }
}

// Process admin demotion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["demote_admin"])) {
    $admin_id = $_POST["admin_id"];
    
    // Don't allow admin to demote themselves
    if ($admin_id == $_SESSION["id"]) {
        $error_message = "You cannot demote your own admin account.";
    } else {
        // Check if this is the last admin
        $admin_count_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE is_admin = 1");
        $admin_count = mysqli_fetch_assoc($admin_count_result);
        
        if ($admin_count['count'] <= 1) {
            $error_message = "Cannot demote the last admin. Create another admin first.";
        } else {
            // Demote admin to regular user
            $sql = "UPDATE users SET is_admin = 0 WHERE id = ?";
            
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "i", $admin_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    $success_message = "Admin demoted to regular user successfully!";
                } else {
                    $error_message = "Error demoting admin: " . mysqli_error($conn);
                }
                
                mysqli_stmt_close($stmt);
            }
        }
    }
}

// Get regular users (non-admins)
$users = [];
$sql = "SELECT id, name, email, created_at FROM users WHERE is_admin = 0 ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}

// Get admins
$admins = [];
$sql = "SELECT id, name, email, created_at FROM users WHERE is_admin = 1 ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $admins[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Management | Lillies Food Shop</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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
        
        .admin-badge {
            background-color: #17a2b8;
            color: white;
            padding: 0.3rem 0.6rem;
            border-radius: 2rem;
            font-size: 0.8rem;
            margin-right: 0.5rem;
        }
        
        .current-user {
            background-color: #28a745;
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
                        <a class="nav-link" href="user_management.php">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="manage_admins.php">Admins</a>
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
        <div class="row">
            <div class="col-md-12 mb-4">
                <h1 class="mb-4">Admin Management</h1>
                
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
            </div>
        </div>
        
        <div class="row">
            <!-- Current Admins -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Current Admins</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($admins) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($admins as $admin): ?>
                                            <tr>
                                                <td>
                                                    <?php echo htmlspecialchars($admin['name']); ?>
                                                    <?php if ($admin['id'] == $_SESSION["id"]): ?>
                                                        <span class="badge bg-success">You</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                                <td>
                                                    <?php if ($admin['id'] != $_SESSION["id"]): ?>
                                                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to remove admin privileges from this user?');">
                                                            <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                                            <button type="submit" name="demote_admin" class="btn btn-sm btn-warning">
                                                                <i class="bi bi-arrow-down-circle me-1"></i> Remove Admin
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="text-muted">Current User</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-center py-3">No admin users found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Regular Users -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Regular Users</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($users) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td>
                                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to grant admin privileges to this user?');">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <button type="submit" name="promote_to_admin" class="btn btn-sm btn-success">
                                                            <i class="bi bi-arrow-up-circle me-1"></i> Make Admin
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-center py-3">No regular users found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 