<?php
    ob_start(); // Start the output buffer
    session_start();
    $title = 'Detach Subject from Student';
    $_SESSION['current_page'] = $_SERVER['REQUEST_URI'];

    require '../../functions.php';
    guard(); 
    
    // Paths
    $dashboardPath = "../dashboard.php";
    $logoutPath = "../logout.php";
    $subjectPath = "../subject/add.php";
    $studentPath = "register.php";

    require '../partials/header.php';
    require '../partials/side-bar.php';

    // Check if student_id and subject_id are provided in the POST data
    if (!isset($_POST['student_id']) || !isset($_POST['subject_id'])) {
        header("Location: register.php");
        exit();
    }

    // Sanitize input from POST parameters
    $student_id = sanitizeInput($_POST['student_id']);
    $subject_id = sanitizeInput($_POST['subject_id']);

    // Fetch student subject details from the database
    $studentSubjectDetails = getStudentSubjectDetails($student_id, $subject_id);

    if (!$studentSubjectDetails) {
        header("Location: register.php");
        exit();  // If no details found, redirect
    }

    // Extract student and subject details
    $first_name = sanitizeInput($studentSubjectDetails['first_name']);
    $last_name = sanitizeInput($studentSubjectDetails['last_name']);
    $subject_code = sanitizeInput($studentSubjectDetails['subject_code']);
    $subject_name = sanitizeInput($studentSubjectDetails['subject_name']);

    // Detach subject when form is submitted
    if (isset($_POST['btnConfirmDetach'])) {
        $result = detachSubjectFromStudent($student_id, $subject_id);
        
        if ($result) {
            $_SESSION['message'] = "Subject successfully detached from student.";
            header("Location: attach-subject.php?student_id=" . $student_id);
            exit();  // Redirect back to the attach-subject page
        } else {
            $_SESSION['message'] = "There was an error detaching the subject.";
            header("Location: dettach-subject.php?student_id=" . $student_id . "&subject_id=" . $subject_id);
            exit();  // Redirect back to the detachment form with an error
        }
    }

    // Cancel action redirects to the attach-subject page
    if (isset($_POST['btnCancel'])) {
        header("Location: attach-subject.php?student_id=" . $student_id);
        exit();
    }
?>

<main class="container justify-content-between align-items-center col-8 mt-4">
    <h2 class="mt-4">Detach Subject from Student</h2>
    <div class="mt-5 mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="register.php" class="text-decoration-none">Register Student</a></li>
                <li class="breadcrumb-item"><a href="attach-subject.php?student_id=<?= sanitizeInput($student_id); ?>" class="text-decoration-none">Attach Subject to Student</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detach Subject from Student</li>
            </ol>
        </nav>
    </div>

    <!-- Check if no form submission has occurred and display the form -->
    <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
        <p>Are you sure you want to detach the following subject from this student?</p>

        <ul>
            <li><strong>Student ID:</strong> <?= sanitizeInput($student_id); ?></li>
            <li><strong>First Name:</strong> <?= sanitizeInput($first_name); ?></li>
            <li><strong>Last Name:</strong> <?= sanitizeInput($last_name); ?></li>
            <li><strong>Subject Code:</strong> <?= sanitizeInput($subject_code); ?></li>
            <li><strong>Subject Name:</strong> <?= sanitizeInput($subject_name); ?></li>
        </ul>

        <input type="hidden" name="student_id" value="<?= $student_id; ?>">
        <input type="hidden" name="subject_id" value="<?= $subject_id; ?>">

        <div>
            <button name="btnCancel" type="submit" class="btn btn-secondary">Cancel</button>
            <button name="btnConfirmDetach" type="submit" class="btn btn-danger">Detach Subject</button>
        </div>
    </form>
</main>

<?php require '../partials/footer.php'; ?>
