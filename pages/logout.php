<?php
require_once '../config/config.php';

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: login.php');
exit;
?> 