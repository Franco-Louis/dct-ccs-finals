<?php   
    ob_start();
    session_start();
    $title = 'Delete Student';
    $_SESSION['current_page'] = $_SERVER['REQUEST_URI'];

    require '../../functions.php';
    guard();
    
    // Define navigation paths
    $pathDashboard = "../dashboard.php";
    $pathLogout = "../logout.php";
    $pathSubjects = "../subject/add.php";
    $pathStudents = "register.php";

    require '../partials/header.php';
    require '../partials/side-bar.php';

    // Redirect function to avoid redundancy
    function redirectToRegister() {
        header("Location: register.php");
        exit();
    }

    // Ensure student ID is passed and valid
    if (isset($_POST['student_id'])) {
        $student_id = sanitizeInput($_POST['student_id']);
        $student = getStudentData($student_id);

        // If student doesn't exist, redirect to register page
        if (!$student) {
            redirectToRegister();
        }
    } else {
        redirectToRegister();
    }

    // Delete student and associated subjects upon confirmation
    if (isset($_POST['btnConfirmDelete'])) {
        deleteStudentAndSubjects($student_id);
        redirectToRegister();
    }

    // Cancel the deletion and redirect to the registration page
    if (isset($_POST['btnCancel'])) {
        redirectToRegister();
    }
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Delete Student</h1>
    <div class="mt-5 mb-3 w-100">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="register.php" class="text-decoration-none">Register Student</a></li>
                <li class="breadcrumb-item active" aria-current="page">Delete Student</li>
            </ol>
        </nav>
    </div>

    <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
        <p>Are you sure you want to delete the following student record?</p>

        <ul>
            <li><strong>Student ID:</strong> <?= sanitizeInput($student['student_id']); ?> </li>
            <li><strong>First Name:</strong> <?= sanitizeInput($student['first_name']); ?> </li>
            <li><strong>Last Name:</strong> <?= sanitizeInput($student['last_name']); ?> </li>
        </ul>

        <input type="hidden" name="student_id" value="<?= sanitizeInput($student['student_id']); ?>">

        <div>
            <button name="btnCancel" type="submit" class="btn btn-secondary">Cancel</button>
            <button name="btnConfirmDelete" type="submit" class="btn btn-primary">Delete Student Record</button>
        </div>
    </form>
</main>

<?php require '../partials/footer.php'; ?>
