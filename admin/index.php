<?php
// Start session and include database connection
session_start();
require_once '../config/db_connect.php';

// Check if user is already logged in as admin
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] === true) {
    header("location: dashboard.php");
    exit;
}

// Initialize variables
$email = $password = "";
$email_err = $password_err = $login_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }
    
    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Check input errors before authenticating
    if (empty($email_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT id, name, email, password, is_admin FROM users WHERE email = ?";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = $email;
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if email exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $name, $email, $hashed_password, $is_admin);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Check if the user is an admin
                            if ($is_admin != 1) {
                                $login_err = "You do not have admin privileges.";
                            } else {
                                // Password is correct and user is admin, start a new session
                                session_start();
                                
                                // Store data in session variables
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["name"] = $name;
                                $_SESSION["email"] = $email;
                                $_SESSION["is_admin"] = true;
                                
                                // Redirect to admin dashboard
                                header("location: dashboard.php");
                                exit;
                            }
                        } else {
                            // Password is not valid
                            $login_err = "Invalid email or password.";
                        }
                    }
                } else {
                    // Email doesn't exist
                    $login_err = "Invalid email or password.";
                }
            } else {
                $login_err = "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
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
    <title>Admin Login | Lillies Food Shop</title>
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
        .admin-login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        
        .admin-login-box {
            width: 100%;
            max-width: 420px;
            padding: 2rem;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .admin-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .admin-logo {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }
        
        .blue {
            color: #0078ff;
        }
        
        .bold {
            font-weight: 800;
        }
    </style>
</head>
<body>
    <div class="admin-login-container">
        <div class="admin-login-box">
            <div class="admin-header">
                <h1 class="admin-logo">
                    <span class="blue">Lillies</span><br>
                    <span class="bold">Admin Panel</span>
                </h1>
                <p class="text-muted">Enter your credentials to access the admin panel</p>
            </div>

            <?php if (!empty($login_err)) { ?>
                <div class="alert alert-danger"><?php echo $login_err; ?></div>
            <?php } ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" 
                           name="email" id="email" placeholder="Enter admin email" 
                           value="<?php echo $email; ?>" required>
                    <span class="invalid-feedback"><?php echo $email_err; ?></span>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" 
                           name="password" id="password" placeholder="Enter password" required>
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </div>

                <button type="submit" class="btn btn-primary w-100">Log In</button>
            </form>

            <div class="mt-4 text-center">
                <a href="../index.php" class="text-decoration-none">Return to Website</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 