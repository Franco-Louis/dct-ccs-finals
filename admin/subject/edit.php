<?php
session_start();
$title = 'Edit Subject';
$_SESSION['current_page'] = $_SERVER['REQUEST_URI'];

require '../partials/header.php';
require '../../functions.php';
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
                <input type="number" class="form-control bg-light" id="subject_code" name="subject_code" 
                       placeholder="Subject Code" value="" readonly>
                <label for="subject_code">Subject Code</label>
            </div>

            <!-- Subject Name -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control bg-light" id="subject_name" name="subject_name" 
                       placeholder="Subject Name" value="">
                <label for="subject_name">Subject Name</label>
            </div>

            <!-- Action Buttons -->
            <div>
                <button name="btnUpdateSubject" type="submit" class="btn btn-primary w-100">Update Subject</button>
            </div>
        </form>
    </div>
<?php
    include '../partials/footer.php';
?>

</body>
</html>