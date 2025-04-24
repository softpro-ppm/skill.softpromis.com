<?php
// Login fix utility
require_once 'config.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Display errors for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

function executeQuery($query, $params = []) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        echo "<p class='error'>Database error: {$e->getMessage()}</p>";
        return null;
    }
}

// Process form submission
$message = '';
$error = '';
$users = [];

// List users if admin is logged in
try {
    $users = executeQuery("SELECT id, name, email, role_id, status FROM users LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Error listing users: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $error = "Email and password are required";
        } else {
            try {
                // Check user
                $stmt = executeQuery(
                    "SELECT u.*, r.role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.email = ?", 
                    [$email]
                );
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user) {
                    if (password_verify($password, $user['password'])) {
                        // Generate token
                        $token = bin2hex(random_bytes(32));
                        
                        // Update user's token
                        executeQuery(
                            "UPDATE users SET token = ?, last_login = NOW() WHERE id = ?",
                            [$token, $user['id']]
                        );
            
                        // Prepare user data for session
                        $userData = [
                            'id' => $user['id'],
                            'email' => $user['email'],
                            'name' => $user['name'],
                            'role' => $user['role_name'],
                            'token' => $token
                        ];
            
                        // Set session
                        $_SESSION['user'] = $userData;
                        
                        $message = "Login successful! Session data has been updated.";
                    } else {
                        $error = "Invalid password";
                    }
                } else {
                    $error = "User with email {$email} not found";
                }
            } catch (Exception $e) {
                $error = "Login error: " . $e->getMessage();
            }
        }
    } else if (isset($_POST['logout'])) {
        // Clear session
        $_SESSION = [];
        session_destroy();
        session_start();
        $message = "You have been logged out.";
    } else if (isset($_POST['fix_permissions'])) {
        try {
            // Create roles table if not exists
            executeQuery("
                CREATE TABLE IF NOT EXISTS roles (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    role_name VARCHAR(50) NOT NULL,
                    description TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ");
            
            // Insert default roles if they don't exist
            $roles = [
                ['Administrator', 'Full system access'],
                ['Manager', 'Training center management access'],
                ['Trainer', 'Course and batch management access'],
                ['Student', 'Student portal access']
            ];
            
            foreach ($roles as $index => $role) {
                $roleId = $index + 1;
                $roleName = $role[0];
                $description = $role[1];
                
                $stmt = executeQuery("SELECT id FROM roles WHERE id = ?", [$roleId]);
                if (!$stmt->fetch()) {
                    executeQuery(
                        "INSERT INTO roles (id, role_name, description) VALUES (?, ?, ?)",
                        [$roleId, $roleName, $description]
                    );
                }
            }
            
            $message = "Roles have been fixed successfully.";
        } catch (Exception $e) {
            $error = "Error fixing permissions: " . $e->getMessage();
        }
    } else if (isset($_POST['fix_user'])) {
        $userId = $_POST['user_id'] ?? '';
        if (!empty($userId)) {
            try {
                // Update user role to Administrator and set active status
                executeQuery(
                    "UPDATE users SET role_id = 1, status = 'active' WHERE id = ?",
                    [$userId]
                );
                $message = "User permissions fixed successfully.";
            } catch (Exception $e) {
                $error = "Error fixing user: " . $e->getMessage();
            }
        }
    }
}

// Get current session data
$sessionData = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Fix Utility</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { border: 1px solid #ddd; border-radius: 5px; padding: 20px; margin-bottom: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow: auto; }
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        form { margin-bottom: 20px; }
        input[type="text"], input[type="password"], input[type="email"] { padding: 8px; width: 100%; margin-bottom: 10px; }
        input[type="submit"] { padding: 8px 15px; background: #4CAF50; color: white; border: none; cursor: pointer; }
        input[type="submit"]:hover { background: #45a049; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Login Fix Utility</h1>
        
        <div class="nav">
            <a href="index.php">Login Page</a> | 
            <a href="session_test.php">Session Test</a> | 
            <a href="db_test.php">Database Test</a>
        </div>
        
        <?php if ($message): ?>
            <p class="success"><?php echo $message; ?></p>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <div class="card">
            <h2>Current Session Status</h2>
            <?php if ($sessionData): ?>
                <p class="success">You are logged in as <?php echo htmlspecialchars($sessionData['name']); ?> (<?php echo htmlspecialchars($sessionData['role']); ?>)</p>
                <pre><?php print_r($sessionData); ?></pre>
                
                <form method="post" action="">
                    <input type="submit" name="logout" value="Logout">
                </form>
            <?php else: ?>
                <p class="error">You are not logged in</p>
                
                <h3>Login Form</h3>
                <form method="post" action="">
                    <div>
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div>
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div>
                        <input type="submit" name="login" value="Login">
                    </div>
                </form>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h2>Fix Permissions</h2>
            <p>This will create/repair the roles table and ensure all required roles exist.</p>
            <form method="post" action="">
                <input type="submit" name="fix_permissions" value="Fix Permissions">
            </form>
        </div>
        
        <div class="card">
            <h2>User Management</h2>
            <?php if (!empty($users)): ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role ID</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo $user['role_id']; ?></td>
                        <td><?php echo $user['status']; ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="submit" name="fix_user" value="Make Admin">
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No users found or database error.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 