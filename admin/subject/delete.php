<?php
ob_start();
session_start();

$title = 'Delete Subject';
require '../../functions.php';

$dashboardPath = "../dashboard.php";
$logoutPath = "../logout.php";
$subjectPath = "add.php";
$studentPath = "../student/register.php";

require '../partials/header.php';
require '../partials/side-bar.php';

// Initialize subject_code and check if it's set
$subject_code = isset($_POST['subject_code']) ? $_POST['subject_code'] : null;
$subject = null;

if ($subject_code) {
    // Establish database connection
    $con = dataBaseConnection();

    // Sanitize the subject code to prevent SQL injection
    $subject_code = $con->real_escape_string($subject_code);

    // Fetch subject by subject_code
    $query = "SELECT * FROM subjects WHERE subject_code = '$subject_code'";
    $result = $con->query($query);

    // Check if a subject was found
    if ($result && $result->num_rows > 0) {
        $subject = $result->fetch_assoc();
    } else {
        // Redirect if no subject is found
        redirectTo('add.php');
    }

    $con->close(); // Close the database connection
} else {
    // Redirect if no subject_code is passed
    redirectTo('add.php');
}

// Handle deletion confirmation
if (isset($_POST['btnConfirmDelete'])) {
    // Establish database connection
    $con = dataBaseConnection();

    // Sanitize the subject_code before deletion
    $subject_code = $con->real_escape_string($subject_code);

    // Delete the subject by subject_code
    $deleteQuery = "DELETE FROM subjects WHERE subject_code = '$subject_code'";
    if ($con->query($deleteQuery)) {
        // Redirect to add.php after successful deletion
        header("Location: add.php");
    } else {
        // Handle the error if deletion fails
        echo "Error deleting subject: " . $con->error;
    }

    $con->close(); // Close the database connection
} elseif (isset($_POST['btnCancel'])) {
    // Redirect to add.php if the user cancels
    header("Location: add.php");
}

?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Delete Subject</h1>
    <div class="mt-5 mb-3 w-100">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="add.php" class="text-decoration-none">Add Subject</a></li>
                <li class="breadcrumb-item active" aria-current="page">Delete Subject</li>
            </ol>
        </nav>
    </div>

    <?php
    // Check if subject exists and display form
    if ($subject) {
    ?>
        <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
            <p>Are you sure you want to delete the following subject record?</p>

            <ul>
                <li><strong>Subject Code:</strong> <?php echo $subject['subject_code']; ?> </li>
                <li><strong>Subject Name:</strong> <?php echo $subject['subject_name']; ?> </li>
            </ul>

            <input type="hidden" name="subject_code" value="<?php echo ($subject['subject_code']); ?>">

            <div>
                <button name="btnCancel" type="submit" class="btn btn-secondary">Cancel</button>
                <button name="btnConfirmDelete" type="submit" class="btn btn-primary">Delete Subject Record</button>
            </div>
        </form>
    <?php
    } else {
        // If no subject was found, display an error message
        echo "<p class='alert alert-warning'>No subject found to delete.</p>";
    }
    ?>
</main>

<?php require '../partials/footer.php'; ?>
