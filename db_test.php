<?php
// Basic database connection test
require_once 'config.php';

// Display errors for debugging
ini_set('display_errors', 1);
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
    // Get database connection
    $conn = getDBConnection();
    echo "<p style='color:green'>Database connection successful!</p>";
    
    // Test query
    $query = "SHOW TABLES";
    $stmt = $conn->query($query);
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h2>Database Tables:</h2>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    
    // Test training_partners table
    if (in_array('training_partners', $tables)) {
        echo "<h2>Testing training_partners table:</h2>";
        $stmt = $conn->query("SELECT COUNT(*) as count FROM training_partners");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Found {$result['count']} partners in the database.</p>";
        
        if ($result['count'] > 0) {
            echo "<h3>First 5 Partners:</h3>";
            $stmt = $conn->query("SELECT id, name, email, status FROM training_partners LIMIT 5");
            $partners = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Status</th></tr>";
            foreach ($partners as $partner) {
                echo "<tr>";
                echo "<td>{$partner['id']}</td>";
                echo "<td>{$partner['name']}</td>";
                echo "<td>{$partner['email']}</td>";
                echo "<td>{$partner['status']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p style='color:red'>Warning: training_partners table does not exist!</p>";
        
        echo "<h3>Creating training_partners table:</h3>";
        $createTable = "
        CREATE TABLE IF NOT EXISTS training_partners (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            phone VARCHAR(20),
            address TEXT,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP
        )";
        
        try {
            $conn->exec($createTable);
            echo "<p style='color:green'>Training partners table created successfully!</p>";
        } catch (PDOException $e) {
            echo "<p style='color:red'>Error creating table: " . $e->getMessage() . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
?> 