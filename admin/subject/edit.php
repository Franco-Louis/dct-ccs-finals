<?php
ob_start();
session_start();
$title = 'Edit Subject';

require '../partials/header.php';
require '../../functions.php';
require '../partials/side-bar.php';

$dashboardPath = "../dashboard.php";
$logoutPath = "../logout.php";
$subjectPath = "add.php";
$studentPath = "../student/register.php";

$subject = null;

// Check if the subject_code is set in the POST request
if (isset($_POST['subject_code'])) {
    $subject_code = sanitizeInput($_POST['subject_code']);

    // Fetch subject by subject_code from the database
    $con = dataBaseConnection();
    $stmt = $con->prepare("SELECT * FROM subjects WHERE subject_code = ?");
    $stmt->bind_param("s", $subject_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $subject = $result->fetch_assoc();
    $stmt->close();
    mysqli_close($con);

    if (!$subject) {
        header("Location: add.php");
        exit();
    }
} else {
    header("Location: add.php");
    exit();
}

if (isset($_POST['btnUpdateSubject'])) {
    $subject_name = sanitizeInput($_POST['subject_name']);
    $arrErrors = [];

    // Validate Subject Name
    if (empty($subject_name)) {
        $arrErrors[] = "Subject name is required.";
    }

    // If no errors, update the subject
    if (empty($arrErrors)) {
        $con = dataBaseConnection();
        $stmt = $con->prepare("UPDATE subjects SET subject_name = ? WHERE subject_code = ?");
        $stmt->bind_param("ss", $subject_name, $subject_code);
        $stmt->execute();
        $stmt->close();
        mysqli_close($con);

        // After successful update, redirect to add page
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

    <!-- Edit Subject Form -->
    <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
        <!-- Subject Code -->
        <div class="form-floating mb-3">
            <input type="number" class="form-control bg-light" id="txtSubjectCode" name="subject_code" value="<?= htmlspecialchars($subject['subject_code']); ?>" readonly>
            <label for="txtSubjectCode">Subject Code</label>
        </div>

        <!-- Subject Name -->
        <div class="form-floating mb-3">
            <input type="text" class="form-control bg-light" id="subject_name" name="subject_name" placeholder="Enter Subject Name" value="<?= isset($_POST['subject_name']) ? htmlspecialchars($_POST['subject_name']) : (isset($subject['subject_name']) ? htmlspecialchars($subject['subject_name']) : ''); ?>">
            <label for="subject_name">Subject Name</label>
        </div>

        <!-- Action Buttons -->
        <div>
            <button name="btnUpdateSubject" type="submit" class="btn btn-primary w-100">Update Subject</button>
        </div>
    </form>
</div>

<?php
// Include footer
include '../partials/footer.php';
?>
