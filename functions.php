<?php

function dataBaseConnection() {
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $dbname = 'dct-ccs-finals';

    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

function validateLoginCredentials($email, $password) {
    $arrErrors = [];
    
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

// Function to display error messages
function displayErrors($errors) {
    $output = "<div class='alert alert-danger'>";
    $output .= "<strong>System Errors:</strong>";
    $output .= "<ul class='mb-0'>";
    
    foreach ($errors as $error) {
        $output .= "<li>" . htmlspecialchars($error) . "</li>";
    }

    $output .= "</ul>";
    $output .= "</div>";

    return $output;
}

function checkLoginCredentials($email, $password) {
    // Establish database connection
    $con = dataBaseConnection();

    // Sanitize the inputs to prevent SQL injection
    $email = $con->real_escape_string($email);
    $password = $con->real_escape_string($password);

    // Prepare the SQL query
    $strSql = "
        SELECT * FROM users
        WHERE email = '$email'
        AND password = '$password'
    ";

    // Execute the query
    if ($rsLogin = $con->query($strSql)) {
        // Check if the result contains any rows
        if ($rsLogin->num_rows > 0) {
            // Valid credentials
            $rsLogin->free_result(); // Free result set
            $con->close(); // Close the connection
            return true;
        } else {
            // Invalid credentials
            $rsLogin->free_result(); // Free result set
            $con->close(); // Close the connection
            return false;
        }
    } else {
        // Query failed
        $con->close(); // Close the connection
        return false;
    }
}



?>
