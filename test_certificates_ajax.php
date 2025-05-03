<?php
// Test script for certificates_ajax.php

// Include necessary files
require_once 'config.php';
require_once 'inc/functions.php';

// Simulate a POST request
$_POST['action'] = 'list';

// Include the target file
include 'inc/ajax/certificates_ajax.php';
