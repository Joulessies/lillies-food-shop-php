<?php
// Start the output buffering to catch any errors
ob_start();

echo "<h1>Database Connection Check</h1>";

// Test database connection directly
$server = 'localhost';
$username = 'root';
$password = '';
$database = 'lillies_food_shop';

echo "<h2>1. Testing Direct Connection</h2>";
$conn = new mysqli($server, $username, $password);
if ($conn->connect_error) {
    echo "<p style='color: red;'>Connection failed: " . $conn->connect_error . "</p>";
} else {
    echo "<p style='color: green;'>MySQL Connection: Success</p>";
    
    // Check if database exists
    $result = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$database'");
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>Database '$database' exists.</p>";
        
        // Select the database
        if ($conn->select_db($database)) {
            echo "<p style='color: green;'>Database selection: Success</p>";
            
            // Check users table
            $result = $conn->query("SHOW TABLES LIKE 'users'");
            if ($result->num_rows > 0) {
                echo "<p style='color: green;'>Table 'users' exists.</p>";
                
                // Check users table structure
                $result = $conn->query("DESCRIBE users");
                
                echo "<h3>Users Table Structure:</h3>";
                echo "<table border='1' cellpadding='5'>";
                echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
                
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['Field']}</td>";
                    echo "<td>{$row['Type']}</td>";
                    echo "<td>{$row['Null']}</td>";
                    echo "<td>{$row['Key']}</td>";
                    echo "<td>{$row['Default']}</td>";
                    echo "<td>{$row['Extra']}</td>";
                    echo "</tr>";
                }
                
                echo "</table>";
                
                // Count users
                $result = $conn->query("SELECT COUNT(*) as total FROM users");
                $row = $result->fetch_assoc();
                echo "<p>Total users in database: {$row['total']}</p>";
                
                // Check for ID=0 issues
                $result = $conn->query("SELECT * FROM users WHERE id = 0");
                if ($result->num_rows > 0) {
                    echo "<p style='color: red;'>WARNING: Found records with ID=0 which can cause duplicate key issues!</p>";
                } else {
                    echo "<p style='color: green;'>No users with ID=0 found. Good!</p>";
                }
                
                // Check for auto_increment setting
                $result = $conn->query("SHOW TABLE STATUS LIKE 'users'");
                $row = $result->fetch_assoc();
                echo "<p>Current AUTO_INCREMENT value: {$row['Auto_increment']}</p>";
                
            } else {
                echo "<p style='color: red;'>Table 'users' does not exist.</p>";
            }
        } else {
            echo "<p style='color: red;'>Cannot select database '$database': " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color: red;'>Database '$database' does not exist.</p>";
    }
}

echo "<h2>2. Testing Connection Through Config</h2>";
// Test the connection through the config file
require_once 'config/db_connect.php';

if (!isset($conn) || $conn->connect_error) {
    echo "<p style='color: red;'>Connection through config failed</p>";
} else {
    echo "<p style='color: green;'>Connection through config: Success</p>";
}

echo "<h2>3. Test Query</h2>";
// Try a test query
$test_query = "SELECT 1";
$result = $conn->query($test_query);
if ($result) {
    echo "<p style='color: green;'>Test query: Success</p>";
} else {
    echo "<p style='color: red;'>Test query failed: " . $conn->error . "</p>";
}

// Add fixes for common issues
echo "<h2>4. Suggested Fixes</h2>";
echo "<ul>";
echo "<li><a href='fix_duplicate_key.php' class='btn btn-warning'>Run Fix for Duplicate Key Issue</a></li>";
echo "<li><a href='direct_signup.php' class='btn btn-primary'>Try Simplified Signup Form</a></li>";
echo "</ul>";

// Get any PHP errors
$errors = ob_get_contents();
ob_end_clean();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Check | Lillies Food Shop</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        h2 {
            margin-top: 30px;
            color: #444;
        }
        .btn {
            margin: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php echo $errors; ?>
        
        <div class="mt-4">
            <a href="index.php" class="btn btn-secondary">Back to Home</a>
            <a href="direct_signup.php" class="btn btn-success">Try Alternative Signup</a>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 