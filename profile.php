<?php
// Start session and include database connection
session_start();
require_once 'config/db_connect.php';

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Initialize variables
$name = $_SESSION["name"];
$email = $_SESSION["email"];
$new_password = $confirm_password = "";
$name_err = $password_err = $confirm_password_err = "";
$success_message = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } elseif (strlen(trim($_POST["name"])) < 3) {
        $name_err = "Name must have at least 3 characters.";
    } else {
        $name = trim($_POST["name"]);
    }
    
    // Validate new password if provided
    if (!empty(trim($_POST["new_password"]))) {
        if (strlen(trim($_POST["new_password"])) < 6) {
            $password_err = "Password must have at least 6 characters.";
        } else {
            $new_password = trim($_POST["new_password"]);
        }
        
        // Validate confirm password
        if (empty(trim($_POST["confirm_password"]))) {
            $confirm_password_err = "Please confirm password.";     
        } else {
            $confirm_password = trim($_POST["confirm_password"]);
            if (empty($password_err) && ($new_password != $confirm_password)) {
                $confirm_password_err = "Passwords did not match.";
            }
        }
    }
    
    // Check input errors before updating in database
    if (empty($name_err) && empty($password_err) && empty($confirm_password_err)) {
        
        // Check if we're updating only name or both name and password
        if (empty($new_password)) {
            // Prepare an update statement for just the name
            $sql = "UPDATE users SET name = ? WHERE id = ?";
            
            if ($stmt = mysqli_prepare($conn, $sql)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "si", $param_name, $param_id);
                
                // Set parameters
                $param_name = $name;
                $param_id = $_SESSION["id"];
                
                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    // Update the session variable
                    $_SESSION["name"] = $name;
                    $success_message = "Your profile has been updated.";
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
                
                // Close statement
                mysqli_stmt_close($stmt);
            }
        } else {
            // Prepare an update statement for both name and password
            $sql = "UPDATE users SET name = ?, password = ? WHERE id = ?";
            
            if ($stmt = mysqli_prepare($conn, $sql)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "ssi", $param_name, $param_password, $param_id);
                
                // Set parameters
                $param_name = $name;
                $param_password = password_hash($new_password, PASSWORD_DEFAULT);
                $param_id = $_SESSION["id"];
                
                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    // Update the session variable
                    $_SESSION["name"] = $name;
                    $success_message = "Your profile has been updated.";
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
                
                // Close statement
                mysqli_stmt_close($stmt);
            }
        }
    }
    
    // Close connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Lillies Food Shop</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
    <!-- Auth CSS -->
    <link rel="stylesheet" href="styles/auth.css">
</head>
<body>
    <?php include 'Layout/Navigation/navigation.php'; ?>

    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h1 class="logo">
                    <span class="blue">My</span><br>
                    <span class="bold">Profile</span>
                </h1>
                <p class="subtitle">Update your account information</p>
            </div>

            <?php if (!empty($success_message)) { ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php } ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" 
                           name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" required>
                    <span class="invalid-feedback"><?php echo $name_err; ?></span>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" name="email" id="email" 
                           value="<?php echo htmlspecialchars($email); ?>" disabled>
                    <small class="text-muted">Email cannot be changed</small>
                </div>

                <hr class="my-4">
                <h5>Change Password</h5>
                <p class="text-muted small mb-3">Leave blank to keep your current password</p>

                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" 
                           name="new_password" id="new_password">
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                    <small class="text-muted">Password must be at least 6 characters long.</small>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" 
                           name="confirm_password" id="confirm_password">
                    <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                </div>

                <button type="submit" class="btn btn-primary w-100">Update Profile</button>
            </form>

            <div class="mt-4 text-center">
                <a href="index.php" class="btn btn-link">Back to Home</a>
            </div>
        </div>
    </div>

    <?php include 'Layout/Footer/footer.php'; ?>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 