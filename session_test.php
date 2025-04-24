<?php
// Session test file to show current session state
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Session Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; }
        .success { color: green; }
        .error { color: red; }
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Session Diagnostic Information</h1>
    
    <h2>Session Status</h2>
    <p>
        <?php if (isset($_SESSION) && !empty($_SESSION)): ?>
            <span class="success">Session is active</span>
        <?php else: ?>
            <span class="error">No active session data</span>
        <?php endif; ?>
    </p>
    
    <h2>Session ID</h2>
    <p><?php echo session_id(); ?></p>
    
    <h2>User Login Status</h2>
    <p>
        <?php if (isset($_SESSION['user']) && !empty($_SESSION['user'])): ?>
            <span class="success">User is logged in</span>
        <?php else: ?>
            <span class="error">User is not logged in</span>
        <?php endif; ?>
    </p>
    
    <h2>User Session Data</h2>
    <?php if (isset($_SESSION['user']) && !empty($_SESSION['user'])): ?>
        <table>
            <tr>
                <th>Property</th>
                <th>Value</th>
            </tr>
            <?php foreach ($_SESSION['user'] as $key => $value): ?>
                <tr>
                    <td><?php echo htmlspecialchars($key); ?></td>
                    <td>
                        <?php 
                        if ($key === 'password' || $key === 'token') {
                            echo '[HIDDEN]';
                        } else {
                            echo htmlspecialchars($value);
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p class="error">No user data in session</p>
    <?php endif; ?>
    
    <h2>All Session Data</h2>
    <pre><?php print_r($_SESSION); ?></pre>
    
    <h2>Cookies</h2>
    <pre><?php print_r($_COOKIE); ?></pre>
    
    <h2>User Agent</h2>
    <p><?php echo htmlspecialchars($_SERVER['HTTP_USER_AGENT'] ?? 'Not available'); ?></p>
    
    <h2>Session Configuration</h2>
    <table>
        <tr>
            <th>Setting</th>
            <th>Value</th>
        </tr>
        <tr>
            <td>session.cookie_lifetime</td>
            <td><?php echo ini_get('session.cookie_lifetime'); ?></td>
        </tr>
        <tr>
            <td>session.gc_maxlifetime</td>
            <td><?php echo ini_get('session.gc_maxlifetime'); ?></td>
        </tr>
        <tr>
            <td>session.use_cookies</td>
            <td><?php echo ini_get('session.use_cookies'); ?></td>
        </tr>
        <tr>
            <td>session.cookie_httponly</td>
            <td><?php echo ini_get('session.cookie_httponly'); ?></td>
        </tr>
        <tr>
            <td>session.cookie_secure</td>
            <td><?php echo ini_get('session.cookie_secure'); ?></td>
        </tr>
    </table>
    
    <p>
        <a href="index.php">Go to Login Page</a>
    </p>
</body>
</html> 