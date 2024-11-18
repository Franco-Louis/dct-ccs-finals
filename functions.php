<?php

function dataBaseConnection() {
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $dbname = 'dct_ccs_finals';

    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

function validateLoginCredentials($email, $password) {
    $arrErrors = [];
    
    // Sanitize input data
    $email = sanitizeInput($email);
    $password = sanitizeInput($password);

    // Validate email
    if ($email === '') {
        $arrErrors[] = 'Email is required.';
    } elseif (!isValidEmail($email)) {
        $arrErrors[] = 'Invalid email.';
    }

    // Validate password
    if ($password === '') {
        $arrErrors[] = 'Password is required.';
    }

    return $arrErrors;
}

// Helper function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(stripslashes(trim($input)));
}

// Helper function to validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

?>
