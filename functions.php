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

function validateStudentData($student_id, $first_name, $last_name) {
    $arrErrors = [];


    // Validate Student ID
    if (empty($student_id)) {
        $arrErrors[] = "Student ID is required.";
    }


    // Validate First Name
    if (empty($first_name)) {
        $arrErrors[] = "First name is required.";
    }


    // Validate Last Name
    if (empty($last_name)) {
        $arrErrors[] = "Last name is required.";
    }


    return $arrErrors;
}


function checkDuplicateStudentData($student_id) {
    $arrErrors = [];
    $con = dataBaseConnection();


    // Check if the student ID already exists in the database
    $stmt = $con->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();


    if ($result->num_rows > 0) {
        $arrErrors[] = "Duplicate Student ID.";
    }


    $stmt->close();
    mysqli_close($con);


    return $arrErrors;
}


function getStudentData($student_id) {
    $con = dataBaseConnection();
    $stmt = $con->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student_data = $result->fetch_assoc();
    $stmt->close();
    mysqli_close($con);


    return $student_data;
}


function getAllStudents() {
    $con = dataBaseConnection();
    $stmt = $con->prepare("SELECT * FROM students ORDER BY student_id ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    $students = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    mysqli_close($con);

    return $students;
}


function getStudentCount() {
    $con = dataBaseConnection();
    $stmt = $con->prepare("SELECT COUNT(*) AS student_count FROM students");
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
    mysqli_close($con);
    
    return $data['student_count'];
}


function registerStudent($student_id, $first_name, $last_name) {
    $con = dataBaseConnection();
    $stmt = $con->prepare("INSERT INTO students (student_id, first_name, last_name) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $student_id, $first_name, $last_name);
    $stmt->execute();
    $stmt->close();
    mysqli_close($con);
}


function updateStudent($student_id, $first_name, $last_name) {
    $con = dataBaseConnection();
    $stmt = $con->prepare("UPDATE students SET first_name = ?, last_name = ? WHERE student_id = ?");
    $stmt->bind_param("sss", $first_name, $last_name, $student_id);
    $stmt->execute();
    $stmt->close();
    mysqli_close($con);
}    


function deleteStudentAndSubjects($student_id) {
    $con = dataBaseConnection();


    $stmt = $con->prepare("DELETE FROM students_subjects WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->close();


    $stmt = $con->prepare("DELETE FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->close();


    mysqli_close($con);
}


?>
