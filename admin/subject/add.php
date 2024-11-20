<?php
session_start();
ob_start();

// Include the database connection function and sanitizeInput
require_once '../../functions.php'; // Adjust the path to your functions file as needed

// Page-specific settings
$title = 'Add Subject';

// Define paths
$dashboardPath = "../dashboard.php";
$logoutPath = "../logout.php";
$subjectPath = "add.php";
$studentPath = "../student/register.php";

// Include required components
require '../partials/header.php'; 
require '../partials/side-bar.php'; 

// Initialize error array
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs using sanitizeInput function
    $subject_code = sanitizeInput($_POST['subject_code']);
    $subject_name = sanitizeInput($_POST['subject_name']);
    
    // Validate input
    if (empty($subject_code)) {
        $errors[] = "Subject Code is required.";
    }

    if (empty($subject_name)) {
        $errors[] = "Subject Name is required.";
    }

    if (empty($errors)) {
        // Use your custom database connection function
        $con = dataBaseConnection();

        // Check for duplicates in the database
        $stmt = $con->prepare("SELECT * FROM subjects WHERE subject_code = ?");
        $stmt->bind_param("s", $subject_code);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "Subject Code already exists.";
        } else {
            // Insert into the database
            $stmt = $con->prepare("INSERT INTO subjects (subject_code, subject_name) VALUES (?, ?)");
            $stmt->bind_param("ss", $subject_code, $subject_name);
            $stmt->execute();

            // Redirect to avoid re-submission
            header("Location: add.php");
            exit();
        }

        $stmt->close();
        $con->close();
    }
}

// Use your custom database connection function to fetch all subjects from the database
$con = dataBaseConnection();
$stmt = $con->prepare("SELECT * FROM subjects");
$stmt->execute();
$result = $stmt->get_result();
$subjects = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$con->close();
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <?php 
    // Display errors, if any
    if (!empty($errors)) {
        echo '<div class="alert alert-danger">';
        echo '<h4>System Errors</h4>';  // Display 'System Errors' header
        echo '<ul>';
        foreach ($errors as $error) {
            echo "<li>$error</li>";  // Display each error as a list item
        }
        echo '</ul>';
        echo '</div>';
    }
    ?>

    <h1 class="h2">Add a New Subject</h1>

    <div class="mt-4 w-100">         
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Subject</li>
            </ol>
        </nav>            
    </div>

    <!-- Subject Form -->
    <div class="row mt-3">
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
                    <?php if (!empty($subjects)): ?>
                        <?php foreach ($subjects as $subject): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                                <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                <td>
                                    <!-- Edit Button (Sends Subject Code via POST to edit.php) -->
                                    <form method="POST" action="edit.php" class="d-inline">
                                        <input type="hidden" name="subject_code" value="<?php echo $subject['subject_code']; ?>">
                                        <button type="submit" class="btn btn-info btn-sm">Edit</button>
                                    </form>

                                    <!-- Delete Button (Leaves unchanged) -->
                                    <a href="delete.php?id=<?php echo $subject['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
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
