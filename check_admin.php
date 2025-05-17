<?php
// Simple script to check if admin user exists and can be authenticated

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lillies_food_shop";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "<h1>Admin User Check</h1>";

// Find user with admin@lilliesfoodshop.com email
$email = "admin@lilliesfoodshop.com";
$plain_password = "admin123";

// First, check if the user exists
$sql = "SELECT id, name, email, password, is_admin FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    echo "<p style='color:green'>Admin user found in the database!</p>";
    echo "<p>User ID: " . $user['id'] . "</p>";
    echo "<p>Name: " . $user['name'] . "</p>";
    echo "<p>Email: " . $user['email'] . "</p>";
    echo "<p>Is Admin: " . ($user['is_admin'] ? "Yes" : "No") . "</p>";
    
    // Check if password matches
    if (password_verify($plain_password, $user['password'])) {
        echo "<p style='color:green'>Password verification successful!</p>";
    } else {
        echo "<p style='color:red'>Password verification failed! Updating password...</p>";
        
        // Update the password
        $new_hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET password = '$new_hashed_password', is_admin = 1 WHERE email = '$email'";
        
        if (mysqli_query($conn, $update_sql)) {
            echo "<p style='color:green'>Password updated successfully!</p>";
        } else {
            echo "<p style='color:red'>Error updating password: " . mysqli_error($conn) . "</p>";
        }
    }
} else {
    echo "<p style='color:red'>Admin user not found in the database!</p>";
    echo "<p>Creating admin user now...</p>";
    
    // Create a hashed password
    $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
    
    // SQL to create admin user
    $create_sql = "INSERT INTO users (name, email, password, is_admin) 
                  VALUES ('Admin User', '$email', '$hashed_password', 1)";
    
    if (mysqli_query($conn, $create_sql)) {
        echo "<p style='color:green'>Admin user created successfully!</p>";
        echo "<p>Email: $email<br>Password: $plain_password</p>";
    } else {
        echo "<p style='color:red'>Error creating admin user: " . mysqli_error($conn) . "</p>";
    }
}

// Show login form for testing
echo "<h2>Test Login</h2>";
echo "<form method='post' action='check_admin.php'>";
echo "<label>Email: <input type='email' name='test_email' value='$email'></label><br>";
echo "<label>Password: <input type='password' name='test_password' value='$plain_password'></label><br>";
echo "<button type='submit' name='test_login'>Test Login</button>";
echo "</form>";

// Process test login
if (isset($_POST['test_login'])) {
    $test_email = $_POST['test_email'];
    $test_password = $_POST['test_password'];
    
    $login_sql = "SELECT id, name, email, password, is_admin FROM users WHERE email = '$test_email'";
    $login_result = mysqli_query($conn, $login_sql);
    
    if (mysqli_num_rows($login_result) == 1) {
        $user = mysqli_fetch_assoc($login_result);
        if (password_verify($test_password, $user['password'])) {
            echo "<p style='color:green'>Login successful!</p>";
            echo "<p>This means the credentials should work on the main login page.</p>";
            echo "<p><a href='admin/index.php'>Go to Admin Login Page</a></p>";
        } else {
            echo "<p style='color:red'>Login failed: Incorrect password</p>";
        }
    } else {
        echo "<p style='color:red'>Login failed: User not found</p>";
    }
}

// Close connection
mysqli_close($conn);
?> 