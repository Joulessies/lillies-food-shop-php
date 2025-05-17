<?php
// Start session and include database connection
session_start();
require_once 'config/db_connect.php';

// Check if user is logged in as admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] !== true) {
    header("location: index.php");
    exit;
}

// Process admin removal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_admin"])) {
    $admin_id = $_POST["admin_id"];
    $current_admin_id = $_SESSION["id"];
    
    // Don't allow admin to delete themselves
    if ($admin_id == $current_admin_id) {
        $error_message = "You cannot delete your own admin account.";
    } else {
        // Check if there are other admins before deleting
        $check_other_admins = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE is_admin = 1 AND id != $admin_id");
        $admin_count = mysqli_fetch_assoc($check_other_admins);
        
        if ($admin_count['count'] == 0) {
            $error_message = "Cannot delete this admin as it appears to be the only admin user. Please create another admin user first.";
        } else {
            // Delete the admin
            $delete = mysqli_query($conn, "DELETE FROM users WHERE id = $admin_id AND is_admin = 1");
            
            if ($delete) {
                $success_message = "Successfully removed admin user.";
            } else {
                $error_message = "Error removing admin: " . mysqli_error($conn);
            }
        }
    }
}

// Process direct removal of specific admin by email (without form)
$admin_email_to_remove = "admin@lilliesfoodshop.com";
if (isset($_GET['remove_specific']) && $_GET['remove_specific'] == 'yes') {
    // Check if admin exists
    $result = mysqli_query($conn, "SELECT id FROM users WHERE email = '$admin_email_to_remove'");
    
    if (mysqli_num_rows($result) > 0) {
        $admin_row = mysqli_fetch_assoc($result);
        $admin_id = $admin_row['id'];
        $current_admin_id = $_SESSION["id"];
        
        // Don't allow admin to delete themselves
        if ($admin_id == $current_admin_id) {
            $error_message = "You cannot delete your own admin account.";
        } else {
            // Check if there are other admins before deleting
            $check_other_admins = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE is_admin = 1 AND id != $admin_id");
            $admin_count = mysqli_fetch_assoc($check_other_admins);
            
            if ($admin_count['count'] == 0) {
                $error_message = "Cannot delete this admin as it appears to be the only admin user. Please create another admin user first.";
            } else {
                // Delete the admin
                $delete = mysqli_query($conn, "DELETE FROM users WHERE id = $admin_id");
                
                if ($delete) {
                    $success_message = "Successfully removed admin user with email: $admin_email_to_remove";
                } else {
                    $error_message = "Error removing admin: " . mysqli_error($conn);
                }
            }
        }
    } else {
        $error_message = "No admin user found with email: $admin_email_to_remove";
    }
}

// Get admin users
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
    <title>Admin Tools | Lillies Food Shop</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Admin User Management</h1>
        
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
        
        <div class="card mb-4">
            <div class="card-header">
                <h5>Admin Users</h5>
            </div>
            <div class="card-body">
                <?php if (count($admins) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admins as $admin): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($admin['name']); ?></td>
                                <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($admin['created_at'])); ?></td>
                                <td>
                                    <?php if ($admin['id'] != $_SESSION["id"]): ?>
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this admin user? This action cannot be undone.');">
                                        <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                        <button type="submit" name="delete_admin" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash me-1"></i> Delete
                                        </button>
                                    </form>
                                    <?php else: ?>
                                    <span class="badge bg-info text-dark">Current User</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p>No admin users found.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="admin/user_management.php" class="btn btn-primary">Back to User Management</a>
            
            <!-- Quick Remove Specific Admin Link -->
            <a href="?remove_specific=yes" class="btn btn-warning" onclick="return confirm('Are you sure you want to remove admin@lilliesfoodshop.com? This action cannot be undone.');">
                Remove admin@lilliesfoodshop.com
            </a>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 