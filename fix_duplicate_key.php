<?php
// Database connection
require_once 'config/db_connect.php';

$success_messages = [];
$error_messages = [];
$warning_messages = [];
$info_messages = [];

// 1. Check for users with ID=0
$check_zero = mysqli_query($conn, "SELECT * FROM users WHERE id = 0");
if (!$check_zero) {
    $error_messages[] = "Error checking for ID=0: " . mysqli_error($conn);
} else {
    if (mysqli_num_rows($check_zero) > 0) {
        $info_messages[] = "Found users with ID=0. Attempting to fix...";
        
        // Get data from the record with ID=0 so we can recreate it with proper ID
        $user_data = mysqli_fetch_assoc($check_zero);
        $name = mysqli_real_escape_string($conn, $user_data['name']);
        $email = mysqli_real_escape_string($conn, $user_data['email']);
        $password = mysqli_real_escape_string($conn, $user_data['password']);
        $is_admin = (int)$user_data['is_admin'];
        $created_at = $user_data['created_at'];
        
        // Delete the problematic record
        $delete_result = mysqli_query($conn, "DELETE FROM users WHERE id = 0");
        if (!$delete_result) {
            $error_messages[] = "Error deleting record with ID=0: " . mysqli_error($conn);
        } else {
            $success_messages[] = "Successfully deleted record with ID=0.";
            
            // Re-insert the user with proper auto-increment ID
            $reinsert_sql = "INSERT INTO users (name, email, password, is_admin, created_at) 
                            VALUES ('$name', '$email', '$password', $is_admin, '$created_at')";
            $reinsert_result = mysqli_query($conn, $reinsert_sql);
            
            if (!$reinsert_result) {
                $warning_messages[] = "Failed to re-insert user: " . mysqli_error($conn);
            } else {
                $new_id = mysqli_insert_id($conn);
                $success_messages[] = "Successfully re-inserted user with new ID: $new_id";
            }
        }
    } else {
        $info_messages[] = "No records found with ID=0.";
    }
}

// 2. Make sure AUTO_INCREMENT is properly set
$check_auto_increment = mysqli_query($conn, "SHOW COLUMNS FROM users WHERE Field = 'id'");
if (!$check_auto_increment) {
    $error_messages[] = "Error checking AUTO_INCREMENT: " . mysqli_error($conn);
} else {
    $column_info = mysqli_fetch_assoc($check_auto_increment);
    
    if (strpos($column_info['Extra'], 'auto_increment') === false) {
        $info_messages[] = "AUTO_INCREMENT not set on ID column. Attempting to fix...";
        
        // Alter the table to set AUTO_INCREMENT
        $alter_sql = "ALTER TABLE users MODIFY id INT NOT NULL PRIMARY KEY AUTO_INCREMENT";
        $alter_result = mysqli_query($conn, $alter_sql);
        
        if (!$alter_result) {
            $error_messages[] = "Error setting AUTO_INCREMENT: " . mysqli_error($conn);
        } else {
            $success_messages[] = "Successfully set AUTO_INCREMENT on ID column.";
        }
    } else {
        $info_messages[] = "AUTO_INCREMENT is already set on ID column.";
    }
    
    // Make sure AUTO_INCREMENT is starting from at least 1 (not 0)
    $auto_increment_sql = "ALTER TABLE users AUTO_INCREMENT = 1";
    $auto_increment_result = mysqli_query($conn, $auto_increment_sql);
    
    if (!$auto_increment_result) {
        $warning_messages[] = "Error setting AUTO_INCREMENT start value: " . mysqli_error($conn);
    } else {
        $success_messages[] = "Set AUTO_INCREMENT to start at 1.";
    }
}

// 3. Check for any other ID collisions
$result = mysqli_query($conn, "SELECT id, COUNT(*) as count FROM users GROUP BY id HAVING count > 1");
if ($result && mysqli_num_rows($result) > 0) {
    $info_messages[] = "Found duplicate IDs. Attempting to fix...";
    
    while ($row = mysqli_fetch_assoc($result)) {
        $duplicate_id = $row['id'];
        $info_messages[] = "Found duplicate ID: $duplicate_id";
        
        // Get all records with this ID
        $duplicate_records = mysqli_query($conn, "SELECT * FROM users WHERE id = $duplicate_id");
        $first = true;
        
        while ($record = mysqli_fetch_assoc($duplicate_records)) {
            if ($first) {
                // Skip the first record (keep it)
                $first = false;
                continue;
            }
            
            // Delete and reinsert the duplicates
            $name = mysqli_real_escape_string($conn, $record['name']);
            $email = mysqli_real_escape_string($conn, $record['email']);
            $password = mysqli_real_escape_string($conn, $record['password']);
            $is_admin = (int)$record['is_admin'];
            $created_at = $record['created_at'];
            
            // Delete duplicate
            mysqli_query($conn, "DELETE FROM users WHERE id = $duplicate_id AND email = '$email'");
            
            // Reinsert with new ID
            $reinsert = mysqli_query($conn, "INSERT INTO users (name, email, password, is_admin, created_at) 
                                     VALUES ('$name', '$email', '$password', $is_admin, '$created_at')");
            
            if ($reinsert) {
                $new_id = mysqli_insert_id($conn);
                $success_messages[] = "Fixed duplicate ID $duplicate_id: Record with email $email now has ID $new_id";
            } else {
                $error_messages[] = "Failed to fix duplicate ID $duplicate_id: " . mysqli_error($conn);
            }
        }
    }
}

// 4. More aggressive fix: Recreate the table if needed
if (!empty($error_messages)) {
    $info_messages[] = "Attempting more aggressive fix...";
    
    // Backup existing users
    $users_backup = [];
    $result = mysqli_query($conn, "SELECT * FROM users");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['id'] != 0) { // Skip ID=0 that's causing problems
                $users_backup[] = $row;
            }
        }
    }
    
    // Drop and recreate the table
    $drop_table = mysqli_query($conn, "DROP TABLE IF EXISTS users");
    if ($drop_table) {
        $create_table = mysqli_query($conn, "CREATE TABLE users (
            id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            is_admin TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        if ($create_table) {
            $success_messages[] = "Successfully recreated users table with proper structure.";
            
            // Reinsert all users
            $reinserted = 0;
            foreach ($users_backup as $user) {
                $name = mysqli_real_escape_string($conn, $user['name']);
                $email = mysqli_real_escape_string($conn, $user['email']);
                $password = mysqli_real_escape_string($conn, $user['password']);
                $is_admin = (int)$user['is_admin'];
                $created_at = $user['created_at'];
                
                $insert = mysqli_query($conn, "INSERT INTO users (name, email, password, is_admin, created_at) 
                                      VALUES ('$name', '$email', '$password', $is_admin, '$created_at')");
                if ($insert) {
                    $reinserted++;
                }
            }
            
            $success_messages[] = "Successfully reinserted $reinserted users into the fixed table.";
        } else {
            $error_messages[] = "Failed to recreate users table: " . mysqli_error($conn);
        }
    } else {
        $error_messages[] = "Failed to drop users table: " . mysqli_error($conn);
    }
}

// 3. Check if signup.php is using the correct SQL
$info_messages[] = "The signup.php file was modified to use: INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, 0)";
$info_messages[] = "This should properly utilize AUTO_INCREMENT by not specifying an ID.";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Database Issues | Lillies Food Shop</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h2><i class="bi bi-wrench me-2"></i> Fixing Duplicate Primary Key Issue</h2>
            </div>
            <div class="card-body">
                <!-- Success Messages -->
                <?php if (!empty($success_messages)): ?>
                    <div class="alert alert-success">
                        <?php foreach ($success_messages as $message): ?>
                            <p><i class="bi bi-check-circle me-2"></i> <?php echo $message; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Error Messages -->
                <?php if (!empty($error_messages)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($error_messages as $message): ?>
                            <p><i class="bi bi-exclamation-triangle me-2"></i> <?php echo $message; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Warning Messages -->
                <?php if (!empty($warning_messages)): ?>
                    <div class="alert alert-warning">
                        <?php foreach ($warning_messages as $message): ?>
                            <p><i class="bi bi-exclamation-circle me-2"></i> <?php echo $message; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Info Messages -->
                <?php if (!empty($info_messages)): ?>
                    <div class="alert alert-info">
                        <?php foreach ($info_messages as $message): ?>
                            <p><i class="bi bi-info-circle me-2"></i> <?php echo $message; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <div class="mt-4 d-flex gap-2">
                    <a href="signup.php" class="btn btn-primary">
                        <i class="bi bi-person-plus me-2"></i> Try signing up again
                    </a>
                    <a href="admin/user_management.php" class="btn btn-secondary">
                        <i class="bi bi-people me-2"></i> Return to user management
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 