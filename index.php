<?php

session_start();
ob_start();
require('functions.php');
checkUserSessionIsActive();

if (isset($_POST['login'])) {
   
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);
    $errors = validateLoginCredentials($email, $password);
    
    // Check login credentials
    session_start(); // Ensure session is started

    if (checkLoginCredentials($email, $password)) {
        $_SESSION['email'] = $email;
        header("location: admin/dashboard.php");
    } else if (empty($errors)) {
        $errors[] = 'Invalid email or password';
    }
}    
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Login</title>
</head>

<body class="bg-secondary-subtle">
    <div class="d-flex align-items-center justify-content-center vh-100">
        <div class="col-3">
            <!-- Server-Side Validation Messages should be placed here -->
            <?php 
            if (!empty($errors)) {
                echo "<div class='alert alert-danger'>";
                echo "<h4>System Errors</h4>";  // Display 'System Errors' title
                echo "<ul>";
                foreach ($errors as $error) {
                    echo "<li>$error</li>";  // Display each error as a list item
                }
                echo "</ul>";
                echo "</div>";
            }
            ?>
            
            <div class="card">
                <div class="card-body">
                    <h1 class="h3 mb-4 fw-normal">Login</h1>
                    <form method="post" action="">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="email" name="email" placeholder="user1@example.com">
                            <label for="email">Email address</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                            <label for="password">Password</label>
                        </div>
                        <div class="form-floating mb-3">
                            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
