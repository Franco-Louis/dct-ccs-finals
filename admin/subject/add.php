<?php
session_start();
ob_start();

// Database connection
$host = 'localhost';
$dbname = 'dct-ccs-finals';
$username = 'root';  // Your MySQL username (default is 'root' for localhost)
$password = '';  // Your MySQL password (default is empty for localhost)

try {
    // Create a PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If the connection fails, show the error message
    echo "Connection failed: " . $e->getMessage();
    exit();
}

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

// Initialize error array
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_code = trim($_POST['subject_code']);
    $subject_name = trim($_POST['subject_name']);
    
    // Validate input
    if (empty($subject_code) || empty($subject_name)) {
        $errors[] = "Both Subject Code and Subject Name are required.";
    } else {
        // Check for duplicates in the database
        $stmt = $pdo->prepare("SELECT * FROM subjects WHERE subject_code = :subject_code");
        $stmt->execute(['subject_code' => $subject_code]);
        $duplicate = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($duplicate) {
            $errors[] = "Subject with the same code already exists.";
        } else {
            // Insert into the database
            $stmt = $pdo->prepare("INSERT INTO subjects (subject_code, subject_name) VALUES (:subject_code, :subject_name)");
            $stmt->execute(['subject_code' => $subject_code, 'subject_name' => $subject_name]);

            // Redirect to avoid re-submission
            header("Location: add.php");
            exit();
        }
    }
}

// Fetch all subjects from the database
$stmt = $pdo->query("SELECT * FROM subjects");
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    <?php if (!empty($subjects)): ?>
                        <?php foreach ($subjects as $subject): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                                <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                <td>
                                    <!-- Edit Button (Sends Subject ID via POST to edit.php) -->
                                    <form method="POST" action="edit.php" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $subject['id']; ?>">
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
