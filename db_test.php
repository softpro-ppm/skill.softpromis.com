<?php
// Basic database connection test
require_once 'config.php';

// Display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Database Connection Test</h1>";

// Function definition check
echo "<h2>Function Check:</h2>";
echo "<ul>";
echo "<li>getDBConnection exists: " . (function_exists('getDBConnection') ? "Yes" : "No") . "</li>";
echo "<li>startSecureSession exists: " . (function_exists('startSecureSession') ? "Yes" : "No") . "</li>";
echo "<li>isLoggedIn exists: " . (function_exists('isLoggedIn') ? "Yes" : "No") . "</li>";
echo "</ul>";

// Configuration check
echo "<h2>Configuration Check:</h2>";
echo "<ul>";
echo "<li>DB_HOST: " . (defined('DB_HOST') ? "Defined" : "Not defined") . "</li>";
echo "<li>DB_USER: " . (defined('DB_USER') ? "Defined" : "Not defined") . "</li>";
echo "<li>DB_NAME: " . (defined('DB_NAME') ? "Defined" : "Not defined") . "</li>";
echo "<li>DB_PASS: " . (defined('DB_PASS') ? "Defined (not showing value)" : "Not defined") . "</li>";
echo "</ul>";

try {
    // Connect to database
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "<p style='color:green'>✓ Database connection successful!</p>";
    echo "<p>Connected to: " . DB_HOST . " / " . DB_NAME . "</p>";
    
    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green'>✓ Users table exists</p>";
        
        // Count users
        $countStmt = $pdo->query("SELECT COUNT(*) FROM users");
        $userCount = $countStmt->fetchColumn();
        echo "<p>Total users in database: $userCount</p>";
        
        // List users
        $usersStmt = $pdo->query("SELECT user_id, username, email, full_name, status FROM users LIMIT 10");
        if ($usersStmt->rowCount() > 0) {
            echo "<h2>Users in Database:</h2>";
            echo "<table border='1' cellpadding='5' cellspacing='0'>";
            echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Full Name</th><th>Status</th></tr>";
            
            while ($user = $usersStmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($user['user_id'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($user['username'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($user['email'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($user['full_name'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($user['status'] ?? 'N/A') . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        } else {
            echo "<p style='color:red'>✗ No users found in the database!</p>";
        }
        
        // Check for admin user
        $adminStmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $adminStmt->execute(['admin@softpro.com']);
        
        if ($admin = $adminStmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<p style='color:green'>✓ Admin user found with email admin@softpro.com</p>";
            echo "<p>Admin user details:</p>";
            echo "<ul>";
            foreach ($admin as $key => $value) {
                if ($key !== 'password' && $key !== 'token') {
                    echo "<li><strong>$key</strong>: " . htmlspecialchars($value) . "</li>";
                } else {
                    echo "<li><strong>$key</strong>: [REDACTED]</li>";
                }
            }
            echo "</ul>";
        } else {
            echo "<p style='color:red'>✗ Admin user with email admin@softpro.com not found!</p>";
        }
    } else {
        echo "<p style='color:red'>✗ Users table does not exist!</p>";
    }
    
    // Check roles table
    $stmt = $pdo->query("SHOW TABLES LIKE 'roles'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green'>✓ Roles table exists</p>";
        
        // List roles
        $rolesStmt = $pdo->query("SELECT * FROM roles");
        if ($rolesStmt->rowCount() > 0) {
            echo "<h2>Roles in Database:</h2>";
            echo "<table border='1' cellpadding='5' cellspacing='0'>";
            echo "<tr><th>ID</th><th>Role Name</th><th>Description</th></tr>";
            
            while ($role = $rolesStmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($role['role_id']) . "</td>";
                echo "<td>" . htmlspecialchars($role['role_name']) . "</td>";
                echo "<td>" . htmlspecialchars($role['description'] ?? 'N/A') . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        }
    } else {
        echo "<p style='color:red'>✗ Roles table does not exist!</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    
    // Check if it's a connection issue or database doesn't exist
    if ($e->getCode() == 1049) {
        echo "<p>The database '" . DB_NAME . "' doesn't exist. Please create it first.</p>";
        
        // Try to connect without database name to check if server is accessible
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST,
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            echo "<p style='color:green'>✓ MySQL server connection is working. Just need to create the database.</p>";
        } catch (PDOException $e2) {
            echo "<p style='color:red'>✗ Cannot connect to MySQL server: " . $e2->getMessage() . "</p>";
        }
    }
}
?> 