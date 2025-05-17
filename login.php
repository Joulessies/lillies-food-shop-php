<?php
// Start session and include database connection
session_start();
require_once 'config/db_connect.php';

// Initialize variables
$email = $password = "";
$email_err = $password_err = $login_err = "";

// Check if we have a redirect parameter
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';
$redirect_param = !empty($redirect) ? '?redirect=' . $redirect : '';

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
                            // Password is correct, start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["name"] = $name;
                            $_SESSION["email"] = $email;
                            
                            // Set admin status in session if user is admin
                            if ($is_admin == 1) {
                                $_SESSION["is_admin"] = true;
                            }
                            
                            // If remember me is checked, store cookies
                            if (isset($_POST['remember']) && $_POST['remember'] == 'on') {
                                setcookie("user_login", $email, time() + (10 * 365 * 24 * 60 * 60));
                                setcookie("user_password", $password, time() + (10 * 365 * 24 * 60 * 60));
                            } else {
                                // If not checked, clear cookies
                                setcookie("user_login", "");
                                setcookie("user_password", "");
                            }
                            
                            // Redirect based on user type and parameter
                            if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] === true) {
                                header("location: admin/dashboard.php");
                            } else if (!empty($redirect) && $redirect == 'checkout') {
                                header("location: checkout.php");
                            } else {
                                header("location: index.php");
                            }
                            exit;
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
    <title>Log In | Lillies Food Shop</title>
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
                <p class="subtitle">Welcome back! Please log in to continue.</p>
            </div>

            <?php if (!empty($login_err)) { ?>
                <div class="alert alert-danger"><?php echo $login_err; ?></div>
            <?php } ?>

            <?php if (!empty($redirect) && $redirect == 'checkout') { ?>
                <div class="alert alert-info">
                    Please log in to complete your order. New customer? You can sign up below.
                </div>
            <?php } ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . $redirect_param); ?>" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" 
                           name="email" id="email" placeholder="Enter email" 
                           value="<?php echo isset($_COOKIE['user_login']) ? $_COOKIE['user_login'] : $email; ?>" required>
                    <span class="invalid-feedback"><?php echo $email_err; ?></span>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" 
                           name="password" id="password" placeholder="Enter password" 
                           value="<?php echo isset($_COOKIE['user_password']) ? $_COOKIE['user_password'] : ''; ?>" required>
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember" 
                           <?php echo isset($_COOKIE['user_login']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>

                <button type="submit" class="btn btn-primary w-100">Log In</button>
            </form>

            <div class="mt-4 text-center">
                <p class="mb-2">Don't have an account?</p>
                <a href="signup.php<?php echo $redirect_param; ?>" class="btn btn-outline-primary">Create Account</a>
            </div>
        </div>
    </div>

    <?php include 'Layout/Footer/footer.php'; ?>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 