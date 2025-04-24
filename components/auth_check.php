<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $base_url . 'pages/login.php');
    exit;
}
?> 