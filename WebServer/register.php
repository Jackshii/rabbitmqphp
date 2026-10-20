<?php
    session_start();
    if (isset($_SESSION['sessionToken'])) {
        header("Location: home.php");
        exit();
    }

    $message = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require ('../path.inc');
        require ('../get_host_info.inc');
        require ('../rabbitMQLib.inc');

        $username = $_POST['username'];
        $password = $_POST['password'];

        $creds = array();
        $creds['user'] = $username;
        $creds['password'] = $password;
        $creds['login_registration'] ='registration'; //possible get rid of

        $phpClient = new rabbitMQClient("../authBroker.ini", "registrationServer");
        $response = $phpClient -> send_request ($creds);

        if (is_array($response) && $response['status'] == 'success') {
            $message = "Success";
        } elseif (is_array($response) && $response['status'] == 'duplicate') {
            $message = "Duplicate";
        } else {
            $message = "Failed";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Register</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    </head>
    <body class="d-flex flex-column min-vh-100">
        
        <!-- CARD BELOW -->
        <div class="container mt-5 mb-5">
            <div class="row justify-content-center">
                <div class="col-10 col-lg-6 mt-5">
                    <div class="card rounded-0 mt-5">
                        <h3 class="card-header text-center">Create Account</h3>
                        <div class="card-body">
                            <?php
                                if ($message === "Success") {
                            ?>
                                <div class="d-flex justify-content-center">
                                    <div class="alert alert-success mb-0 mt-3 text-center py-2 px-3 d-inline-block">Registration Successful!
                                        <div>
                                            <a class="text-decoration-underline" href="login.php">Login here</a>
                                        </div>
                                    </div>
                                </div>
                            <?php    
                                }
                                elseif ($message === "Duplicate") {
                            ?>        
                                <div class="d-flex justify-content-center">
                                    <div class="alert alert-danger mb-0 mt-3 text-center py-2 px-3 d-inline-block">
                                        User Already Exists!
                                        <a class="text-decoration-underline" href="index.php">Back to Home!</a>
                                    </div>
                                </div>
                            <?php        
                                }
                                elseif ($message === "Failed") {
                            ?>        
                                <div class="d-flex justify-content-center">
                                    <div class="alert alert-danger mb-0 mt-3 text-center py-2 px-3 d-inline-block">Registration Failed!</div>
                                </div>
                            <?php
                                }
                            ?>
                            <form action="register.php" method="POST">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" maxlength="30" class="form-control" name="username" id="username" placeholder="Your username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" maxlength="255" class="form-control" name="password" id="password" placeholder="Your password" required>
                                </div>
                                <div class="text-center mt-4">
                                    <button type="submit" class="contact-submit-button btn btn-primary border-dark">Create Account</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php require_once('../footer.php'); ?>