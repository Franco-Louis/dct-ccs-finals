<?php
session_start();
ob_start();

// Page-specific settings
$title = 'Subject';
$_SESSION['current_page'] = $_SERVER['REQUEST_URI'];

// Define paths
$dashboardPath = "../dashboard.php";
$logoutPath = "../logout.php";
$subjectPath = "add.php";
$studentPath = "../student/register.php";

// Include required components
require '../partials/header.php'; 
require '../partials/side-bar.php'; 

// Initialize subjects array in session if not already done
if (!isset($_SESSION['subjects'])) {
    $_SESSION['subjects'] = [];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_code = trim($_POST['subject_code']);
    $subject_name = trim($_POST['subject_name']);
    
    // Validate input
    if (empty($subject_code) || empty($subject_name)) {
        $errors[] = "Both Subject Code and Subject Name are required.";
    } else {
        // Check for duplicates
        $duplicate = false;
        foreach ($_SESSION['subjects'] as $subject) {
            if ($subject['subject_code'] == $subject_code) {
                $duplicate = true;
                break;
            }
        }

        if ($duplicate) {
            $errors[] = "Subject with the same code already exists.";
        } else {
            // Add to session
            $_SESSION['subjects'][] = [
                'subject_code' => $subject_code,
                'subject_name' => $subject_name,
            ];

            // Redirect to avoid re-submission
            header("Location: add.php");
            exit();
        }
    }
}
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <?php 
    // Display errors, if any
    if (!empty($errors)) {
        echo '<div class="alert alert-danger">';
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
        echo '</div>';
    }
    ?>

    <h1 class="h2">Add a New Subject</h1>

    <!-- Subject Form -->
    <div class="row mt-5">
        <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
            <!-- Subject Code -->
            <div class="form-floating mb-3">
                <input type="number" class="form-control bg-light" id="subject_code" name="subject_code" 
                       placeholder="Subject Code" value="">
                <label for="subject_code">Subject Code</label>
            </div>

            <!-- Subject Name -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control bg-light" id="subject_name" name="subject_name" 
                       placeholder="Subject Name" value="">
                <label for="subject_name">Subject Name</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">Add Subject</button>
        </form>

        <!-- Subject List -->
        <div class="border border-secondary-1 p-5">
            <h5>Subject List</h5>
            <hr>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($_SESSION['subjects'])): ?>
                        <?php foreach ($_SESSION['subjects'] as $index => $subject): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                                <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                <td>
                                    <!-- Edit Button -->
                                    <a href="edit.php?index=<?php echo $index; ?>" class="btn btn-info btn-sm">Edit</a>

                                    <!-- Delete Button -->
                                    <a href="delete.php?index=<?php echo $index; ?>" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">No subjects found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php
// Include footer
include '../partials/footer.php';
?>
