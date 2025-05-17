<?php
// Start session and include database connection
session_start();
require_once 'config/db_connect.php';

$message = "";
$message_type = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($password)) {
        $message = "Please fill all required fields.";
        $message_type = "danger";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
        $message_type = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please provide a valid email.";
        $message_type = "danger";
    } else {
        // Check if email exists
        $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '" . mysqli_real_escape_string($conn, $email) . "'");
        if (mysqli_num_rows($check_email) > 0) {
            $message = "This email is already registered.";
            $message_type = "danger";
        } else {
            // Create account using direct query
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (name, email, password, is_admin) VALUES (
                '" . mysqli_real_escape_string($conn, $name) . "',
                '" . mysqli_real_escape_string($conn, $email) . "',
                '" . mysqli_real_escape_string($conn, $hashed_password) . "',
                0
            )";
            
            if (mysqli_query($conn, $sql)) {
                $message = "Account created successfully! You can now <a href='login.php'>log in</a>.";
                $message_type = "success";
            } else {
                $message = "Error creating account: " . mysqli_error($conn);
                $message_type = "danger";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Signup | Lillies Food Shop</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Create Account</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                                <?php echo $message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="form-text">Password must be at least 6 characters long.</div>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">I agree to the terms of service</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Create Account</button>
                        </form>
                        
                        <div class="mt-3 text-center">
                            <p>Already have an account? <a href="login.php">Log in</a></p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="index.php" class="btn btn-outline-secondary">Back to Home</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 