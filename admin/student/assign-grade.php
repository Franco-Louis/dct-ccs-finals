<?php
    ob_start();
    session_start();
    $title = 'Assign Grade to Subject';
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

    // Error array
    $arrErrors = [];

    // Check if student_id and subject_id are set
    if (isset($_POST['student_id'], $_POST['subject_id'])) {
        $student_id = sanitizeInput($_POST['student_id']);
        $subject_id = sanitizeInput($_POST['subject_id']);

        // Get student and subject details
        $studentSubjectDetails = getStudentSubjectDetails($student_id, $subject_id);

        if ($studentSubjectDetails) {
            $full_name = sanitizeInput($studentSubjectDetails['first_name'] . ' ' . $studentSubjectDetails['last_name']);
            $subject_code = sanitizeInput($studentSubjectDetails['subject_code']);
            $subject_name = sanitizeInput($studentSubjectDetails['subject_name']);
            $grade = sanitizeInput($studentSubjectDetails['grade']);
        } else {
            echo "No student or subject details found."; // For debugging
            redirectTo($studentPath);
            exit();
        }
    } else {
        echo "Missing student_id or subject_id."; // For debugging
        redirectTo($studentPath);
        exit();
    }

    function redirectTo($url) {
        header("Location: $url");
        exit;
    }

    // Check if grade assignment is triggered
    if (isset($_POST['btnAssignGrade'], $_POST['txtGrade'])) {
        $grade = sanitizeInput($_POST['txtGrade']);
        $arrErrors = validateGrade($grade);

        if (empty($arrErrors)) {
            handleGradeAssignment($student_id, $subject_id, $grade);
            redirectTo("attach-subject.php?student_id=" . $student_id);
        }
    }

    // Handle cancel
    if (isset($_POST['btnCancel'])) {
        redirectTo("attach-subject.php?student_id=" . $student_id);
    }
?>

<main class="container col-8 mt-4">
    <h2 class="mt-4">Assign Grade to Subject</h2>

    <div class="mt-5 mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="register.php" class="text-decoration-none">Register Student</a></li>
                <li class="breadcrumb-item">
                    <a href="attach-subject.php?student_id=<?php echo ($student_id); ?>" class="text-decoration-none">Attach Subject to Student</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Assign Grade to Subject</li>
            </ol>
        </nav>
    </div>

    <!-- Check if errors exist -->
    <?php if ($arrErrors): ?>
        <?= displayErrors($arrErrors); ?>
    <?php endif; ?>

    <!-- Form for grade assignment -->
    <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
        <h4>Selected Student and Subject Information</h4>

        <ul>
            <li><strong>Student ID:</strong> <?php echo ($student_id); ?></li>
            <li><strong>Name:</strong> <?php echo ($full_name); ?></li>
            <li><strong>Subject Code:</strong> <?php echo ($subject_code); ?></li>
            <li><strong>Subject Name:</strong> <?php echo ($subject_name); ?></li>
        </ul>

        <hr>

        <!-- Grade input field -->
        <div class="form-floating mb-3">
            <input type="number" class="form-control" id="txtGrade" name="txtGrade" placeholder="Grade" 
                value="<?php echo isset($_POST['txtGrade']) ? ($_POST['txtGrade']) : number_format(sanitizeInput($grade), 2); ?>">
            <label for="txtGrade">Grade</label>
        </div>

        <input type="hidden" name="student_id" value="<?php echo ($student_id); ?>">
        <input type="hidden" name="subject_id" value="<?php echo ($subject_id); ?>">

        <!-- Buttons should be here -->
        <div>
            <button name="btnCancel" type="submit" class="btn btn-secondary">Cancel</button>
            <button name="btnAssignGrade" type="submit" class="btn btn-primary">Assign Grade</button>
        </div>
    </form>
</main>

<?php require '../partials/footer.php'; ?>
