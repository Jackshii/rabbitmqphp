<?php
session_start();

if (isset($_SESSION['sessionToken'])) {
    header("Location: home.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $message = "";
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require ('../path.inc');
    require ('../get_host_info.inc');
    require ('../rabbitMQLib.inc');

    $username = $_POST['username'];
    $password = $_POST['password'];

    $creds = array();
    $creds['user'] = $username;
    $creds['password'] = $password;
    $creds['login_registration'] ='login';

    $phpClient = new rabbitMQClient("../authBroker.ini", "loginServer");
    $response = $phpClient -> send_request ($creds);
   
    if (is_array($response) && $response['status'] == 'success') {
        $message = "Success";
        $_SESSION['sessionToken'] = $response['session_key'];
        $_SESSION['username'] = $response['username'];
        header("Location: home.php");
        exit();
    } else {
        $message = "Failed";
    }
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
     <pre class="bg-light border m-3 p-2 small"><?php echo htmlspecialchars(var_export($response ?? 'no response', true)); ?></pre>
        <div class="container mt-5 mb-5">
            <div class="row justify-content-center">
                <div class="col-10 col-lg-6 mt-5">
                    <div class="card rounded-0 mt-5">
                        <h3 class="card-header text-center">Login Here</h3>
                        <div class="card-body">
                            <?php    
                                if ($message === "Failed"){
                            ?>        
                                <div class="d-flex justify-content-center">
                                    <div class="alert alert-danger mb-0 mt-3 text-center py-2 px-3 d-inline-block">Login Failed!</div>
                                </div>
                            <?php        
                                    }
                            ?>
                            <form action="login.php" method="POST">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" name="username" maxlength="30" class="form-control" id="username" placeholder="Your username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" maxlength="255" class="form-control" id="password" placeholder="Your password" required>
                                </div>  
                                <div class="text-center mt-4">
                                    <button type="submit" class="contact-submit-button btn btn-primary border-dark">Login</button>
                                </div>
                                <p class="ms-2 mt-4 mb-0">Don't have an account? Create one below:</p>
                                <a href="register.php" class="ms-2 mt-0 fs-6">Create account</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php require_once('../footer.php'); ?>