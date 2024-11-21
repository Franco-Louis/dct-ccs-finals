<?php
    session_start();
    $title = 'Dashboard';

    // Define paths
    $dashboardPath = "dashboard.php";
    $logoutPath = "logout.php";
    $subjectPath = "subject/add.php";
    $studentPath = "student/register.php";

    // Database connection
    require 'partials/header.php';
    require 'partials/side-bar.php';
    require '../functions.php';

    // Function to get the count of subjects
    function getSubjectCount($conn) {
        $query = "SELECT COUNT(*) AS total_subjects FROM subjects";
        $result = $conn->query($query);
        $data = $result->fetch_assoc();
        return $data['total_subjects'];
    }

    // Function to get the count of students
    function getStudentCount($conn) {
        $query = "SELECT COUNT(*) AS total_students FROM students";
        $result = $conn->query($query);
        $data = $result->fetch_assoc();
        return $data['total_students'];
    }

    // Function to calculate the average grade for each student, and determine passed/failed count
    function getPassFailCount($conn) {
        // Query to get all students and their grades for each subject
        $query = "SELECT student_id, AVG(grade) AS average_grade
                  FROM students_subjects
                  GROUP BY student_id";
        $result = $conn->query($query);
        
        $passed = 0;
        $failed = 0;

        while ($row = $result->fetch_assoc()) {
            // Check if the average grade is greater or equal to 75 (pass) or less (fail)
            if ($row['average_grade'] >= 75) {
                $passed++;
            } else {
                $failed++;
            }
        }

        return ['passed' => $passed, 'failed' => $failed];
    }

    $con = dataBaseConnection(); // Use $con instead of $conn

    // Get data
    $subjectCount = getSubjectCount($con);
    $studentCount = getStudentCount($con);
    $result = getPassFailCount($con);
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">    
    <h1 class="h2">Dashboard</h1>        

    <div class="row mt-5">
        <!-- Number of Subjects Card -->
        <div class="col-12 col-xl-3">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white border-primary">Number of Subjects:</div>
                <div class="card-body text-primary">
                    <h5 class="card-title"><?= $subjectCount; ?></h5>
                </div>
            </div>
        </div>

        <!-- Number of Students Card -->
        <div class="col-12 col-xl-3">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white border-primary">Number of Students:</div>
                <div class="card-body text-success">
                    <h5 class="card-title"><?= $studentCount; ?></h5>
                </div>
            </div>
        </div>

        <!-- Number of Failed Students Card -->
        <div class="col-12 col-xl-3">
            <div class="card border-danger mb-3">
                <div class="card-header bg-danger text-white border-danger">Number of Failed Students:</div>
                <div class="card-body text-danger">
                    <h5 class="card-title"><?= $result['failed']; ?></h5>
                </div>
            </div>
        </div>

        <!-- Number of Passed Students Card -->
        <div class="col-12 col-xl-3">
            <div class="card border-success mb-3">
                <div class="card-header bg-success text-white border-success">Number of Passed Students:</div>
                <div class="card-body text-success">
                    <h5 class="card-title"><?= $result['passed']; ?></h5>
                </div>
            </div>
        </div>
    </div>    
</main>

<?php require 'partials/footer.php'; ?>
