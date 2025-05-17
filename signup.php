<?php
// Start session and include database connection
session_start();
require_once 'config/db_connect.php';

// Initialize variables
$name = $email = $password = $confirm_password = "";
$name_err = $email_err = $password_err = $confirm_password_err = $terms_err = "";

// Check if we have a redirect parameter
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';
$redirect_param = !empty($redirect) ? '?redirect=' . $redirect : '';

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
    
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = ?";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = trim($_POST["email"]);
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);
                
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $email_err = "This email is already taken.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";     
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Passwords did not match.";
        }
    }
    
    // Validate terms agreement
    if (!isset($_POST["terms_agree"]) || $_POST["terms_agree"] != "on") {
        $terms_err = "You must agree to the terms of service.";
    }
    
    // Check input errors before inserting in database
    if (empty($name_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($terms_err)) {
        
        // IMPORTANT: First delete any records with ID=0 to prevent duplicate key error
        mysqli_query($conn, "DELETE FROM users WHERE id = 0");
        // Also ensure AUTO_INCREMENT is properly set and starts at 1
        mysqli_query($conn, "ALTER TABLE users AUTO_INCREMENT = 1");
        
        // COMPLETELY DIFFERENT APPROACH: Use direct query to avoid prepared statement issue
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $name_escaped = mysqli_real_escape_string($conn, $name);
        $email_escaped = mysqli_real_escape_string($conn, $email);
        $password_escaped = mysqli_real_escape_string($conn, $hashed_password);
        
        $direct_sql = "INSERT INTO users (name, email, password, is_admin) 
                       VALUES ('$name_escaped', '$email_escaped', '$password_escaped', 0)";
        
        if (mysqli_query($conn, $direct_sql)) {
            // Success - redirect to login
            $login_redirect = "login.php?registered=true";
            if (!empty($redirect)) {
                $login_redirect .= "&redirect=" . $redirect;
            }
            header("location: " . $login_redirect);
        } else {
            // Show the specific error
            echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
            
            // If error is about duplicate key for ID=0, fix it immediately
            if (strpos(mysqli_error($conn), "Duplicate entry '0'") !== false) {
                echo "<div class='alert alert-info'>Found duplicate entry for ID=0. Attempting to fix...</div>";
                
                // Delete the problematic record and try again
                $fix_result = mysqli_query($conn, "DELETE FROM users WHERE id = 0");
                if ($fix_result) {
                    echo "<div class='alert alert-success'>Successfully removed conflicting record. Please try signing up again.</div>";
                    
                    // Set AUTO_INCREMENT to 1
                    mysqli_query($conn, "ALTER TABLE users AUTO_INCREMENT = 1");
                    
                    // Fix ID column if needed
                    mysqli_query($conn, "ALTER TABLE users MODIFY id INT NOT NULL PRIMARY KEY AUTO_INCREMENT");
                } else {
                    echo "<div class='alert alert-danger'>Could not remove conflicting record: " . mysqli_error($conn) . "</div>";
                }
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
    <title>Sign Up | Lillies Food Shop</title>
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
                    <span class="blue">Lillies</span><br>
                    <span class="bold">Food Shop</span>
                </h1>
                <p class="subtitle">Create your account and start ordering!</p>
            </div>

            <?php if (!empty($redirect) && $redirect == 'checkout') { ?>
                <div class="alert alert-info">
                    Create an account to complete your order. Already have an account? <a href="login.php<?php echo $redirect_param; ?>">Log in here</a>.
                </div>
            <?php } ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . $redirect_param); ?>" method="post">
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" 
                           name="name" id="name" placeholder="Enter your full name" 
                           value="<?php echo $name; ?>" required>
                    <span class="invalid-feedback"><?php echo $name_err; ?></span>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" 
                           name="email" id="email" placeholder="Enter email" 
                           value="<?php echo $email; ?>" required>
                    <span class="invalid-feedback"><?php echo $email_err; ?></span>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" 
                           name="password" id="password" placeholder="Enter password" required>
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                    <small class="text-muted">Password must be at least 6 characters long.</small>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" 
                           name="confirm_password" id="confirm_password" placeholder="Confirm your password" required>
                    <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input <?php echo (!empty($terms_err)) ? 'is-invalid' : ''; ?>" 
                           name="terms_agree" id="terms_agree" required>
                    <label class="form-check-label" for="terms_agree">
                        I agree to the <a href="#">terms of service</a> and <a href="#">privacy policy</a>
                    </label>
                    <span class="invalid-feedback"><?php echo $terms_err; ?></span>
                </div>

                <button type="submit" class="btn btn-primary w-100">Create Account</button>
            </form>

            <div class="mt-4 text-center">
                <p>Already have an account?</p>
                <a href="login.php<?php echo $redirect_param; ?>" class="btn btn-outline-secondary">Log In Instead</a>
            </div>
        </div>
    </div>

    <?php include 'Layout/Footer/footer.php'; ?>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 