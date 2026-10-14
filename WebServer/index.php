<?php
session_start();
if (isset($_SESSION['sessionToken'])) {
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>ProjectNameTemp</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    </head>
    <body class="d-flex flex-column min-vh-100">

    <!-- CARD BELOW -->
        <div class="container mt-5 mb-5">
            <div class="row justify-content-center">
                <div class="col-8 col-lg-6 mt-5">
                    <div class="card rounded-0 shadow-sm mt-5">
                        <h3 class="card-header text-center">Welcome To PROJECT NAME</h3>
                        <div class="card-body">
                            <h5 class="card-text text-center text-decoration-underline">Please Login Or Register Below</h5>
                             <div class="text-center mt-4 mb-3">
                                <a href="login.php" class="btn btn-lg rounded-0 border-dark border-3 fs-5">Login</a>
                            </div>
                             <div class="text-center mt-4 mb-2">
                                <a href="register.php" class="btn btn-lg rounded-0 border-dark border-3 fs-5">Register</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php require_once('../footer.php'); ?>