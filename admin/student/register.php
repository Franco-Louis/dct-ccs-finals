<?php
    ob_start();
    session_start();
    $title = 'Register Student';
    $_SESSION['current_page'] = $_SERVER['REQUEST_URI'];
       
    // Include the required functions
    require '../../functions.php';
    guard();
    
    // Define paths for navigation links
    $dashboardPath = "../dashboard.php";
    $logoutPath = "../logout.php";
    $subjectPath = "../subject/add.php";
    $studentPath = "register.php";

    // Include header and sidebar
    require '../partials/header.php';
    require '../partials/side-bar.php';

    // Initialize the error array
    $arrErrors = [];

    // Handle form submission for student registration
    if (isset($_POST['btnRegister'])) {
        // Sanitize and capture form data
        $student_id = sanitizeInput($_POST['txtStudentID']);
        $first_name = sanitizeInput($_POST['txtFirstName']);
        $last_name = sanitizeInput($_POST['txtLastName']);

        // Validate the student data
        $arrErrors = array_merge($arrErrors, validateStudentData($student_id, $first_name, $last_name));

        // Check for duplicate student data
        $arrErrors = array_merge($arrErrors, checkDuplicateStudentData($student_id));

        // If no errors, proceed to register the student
        if (empty($arrErrors)) {
            registerStudent($student_id, $first_name, $last_name);

            // Reset form fields after successful registration
            $student_id = '';
            $first_name = '';
            $last_name = '';
        }
    }

    // Fetch all students for display
    $students = getAllStudents();
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Register a New Student</h1>

    <!-- Breadcrumb Navigation -->
    <div class="mt-5 mb-3 w-100">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Register Student</li>
            </ol>
        </nav>
    </div>

    <!-- Display Errors if any -->
    <?php if (!empty($arrErrors)): ?>
        <?= displayErrors($arrErrors); ?>
    <?php endif; ?>

    <!-- Registration Form -->
    <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
        <div class="form-floating mb-3">
            <input type="number" class="form-control" id="txtStudentID" name="txtStudentID" placeholder="Student ID" value="<?= htmlspecialchars($student_id ?? '') ?>">
            <label for="txtStudentID">Student ID</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="txtFirstName" name="txtFirstName" placeholder="First Name" value="<?= htmlspecialchars($first_name ?? '') ?>">
            <label for="txtFirstName">First Name</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="txtLastName" name="txtLastName" placeholder="Last Name" value="<?= htmlspecialchars($last_name ?? '') ?>">
            <label for="txtLastName">Last Name</label>
        </div>

        <button name="btnRegister" type="submit" class="btn btn-primary w-100">Register Student</button>
    </form>

    <!-- Student List Table -->
    <div class="mt-3">
        <div class="border border-secondary-1 p-5 mb-4">
            <h5>Student List</h5>
            <hr>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Option</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($students) > 0): ?>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?= sanitizeInput($student['student_id']) ?></td>
                                <td><?= sanitizeInput($student['first_name']) ?></td>
                                <td><?= sanitizeInput($student['last_name']) ?></td>
                                <td>
                                    <!-- Edit Student -->
                                    <form method="POST" action="edit.php" class="d-inline">
                                        <input type="hidden" name="student_id" value="<?php echo $student['student_id'] ?>">
                                        <button type="submit" name="btnEdit" class="btn btn-primary btn-sm">Edit</button>
                                    </form>

                                    <!-- Delete Student -->
                                    <form method="POST" action="delete.php" class="d-inline">
                                        <input type="hidden" name="student_id" value="<?php echo $student['student_id'] ?>">
                                        <button type="submit" name="btnDelete" class="btn btn-danger btn-sm">Delete</button>
                                    </form>

                                    <!-- Attach Subject -->
                                    <form method="GET" action="attach-subject.php" class="d-inline">
                                        <input type="hidden" name="student_id" value="<?php echo $student['student_id'] ?>">
                                        <button type="submit" class="btn btn-warning btn-sm">Attach Subject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No students found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require '../partials/footer.php'; ?>