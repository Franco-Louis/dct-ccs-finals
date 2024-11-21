<?php
ob_start();
session_start();
$title = 'Edit Subject';
$_SESSION['current_page'] = $_SERVER['REQUEST_URI'];

require '../partials/header.php';
require '../../functions.php';
require '../partials/side-bar.php';
guard();
// Initialize error array
$errors = [];

// Check for subject code in the POST request
if (isset($_POST['subject_code'])) {
    // Sanitize subject code input
    $subject_code = sanitizeInput($_POST['subject_code']);
    
    // Establish database connection
    $con = dataBaseConnection();

    // Prepare the SQL query to fetch subject by code
    $stmt = $con->prepare("SELECT * FROM subjects WHERE subject_code = ?");
    $stmt->bind_param("s", $subject_code);
    $stmt->execute();
    $result = $stmt->get_result();

    // If no subject is found, redirect to add page
    if ($result->num_rows === 0) {
        header("Location: add.php");
        exit();
    }

    // Fetch the subject data
    $subject = $result->fetch_assoc();
    $stmt->close();
    mysqli_close($con);
} else {
    header("Location: add.php");
    exit();
}

// Handle form submission for updating subject
if (isset($_POST['btnUpdateSubject'])) {
    // Sanitize subject name input
    $subject_name = sanitizeInput($_POST['subject_name']);

    // Validate if subject name is empty
    if (empty($subject_name)) {
        $errors[] = "Subject Name cannot be empty.";
    } else {
        // Update the subject name in the database
        $con = dataBaseConnection();
        $stmt = $con->prepare("UPDATE subjects SET subject_name = ? WHERE subject_code = ?");
        $stmt->bind_param("ss", $subject_name, $subject_code);
        $stmt->execute();
        $stmt->close();
        mysqli_close($con);

        // Redirect to add page after successful update
        header("Location: add.php");
        exit();
    }
}
?>

<div class="container justify-content-between align-items-center col-8 mt-4">
    <h2 class="mt-4">Edit Subject</h2>
    <div class="mt-4 w-100">         
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="add.php" class="text-decoration-none">Add Subject</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Subject</li>
            </ol>
        </nav>            
    </div>

    <!-- Display errors using the centralized error handler -->
    <?php 
    if (!empty($errors)) {
        echo displayErrors($errors);
    }
    ?>

    <!-- Edit Subject Form -->
    <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
        <!-- Subject Code -->
        <div class="form-floating mb-3">
            <input type="number" class="form-control bg-light" id="subject_code" name="subject_code" 
                   placeholder="Subject Code" value="<?= htmlspecialchars($subject['subject_code']); ?>" readonly>
            <label for="subject_code">Subject Code</label>
        </div>
        
        <!-- Subject Name -->
        <div class="form-floating mb-3">
            <input type="text" class="form-control bg-light" id="subject_name" name="subject_name" 
                   placeholder="Subject Name" value="<?= isset($subject['subject_name']) ? htmlspecialchars($subject['subject_name']) : ''; ?>">
            <label for="subject_name">Subject Name</label>
        </div>

        <!-- Update Button -->
        <button type="submit" name="btnUpdateSubject" class="btn btn-primary w-100">Update Subject</button>
    </form>
</div>

<?php include '../partials/footer.php'; ?>
