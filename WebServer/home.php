<?php 

session_start();

if (!isset($_SESSION['sessionToken'])) {
    header("Location: login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>ProjectName Home</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    </head>
    <body class="d-flex flex-column min-vh-100">
        <header>
            <nav class="navbar navbar-expand-md text-black shadow-sm border-bottom bg-white">
                <div class="container-fluid px-5">
                    <a class="navbar-brand" href="home.php">
                        PROJECT NAME
                        <!-- Will Use for Logo Top Left Later If Needed -->
                        <!-- <img src="XXXXXXXXX" alt="XXXXXXXXX" class="img-fluid logo"> -->
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end" id="mainNav">
                        <ul class="navbar-nav ms-auto gap-2 text-center">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-dark fs-5" href="#" data-bs-toggle="dropdown">TEMP 1</a>
                                <!-- SUBMENU if needed later -->
                                <ul class="dropdown-menu play-dropdown text-center">
                                    <li><a class="dropdown-item text-dark" href="XXXXXXXXX">TEMP</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-dark small" href="XXXX">TEMP</a></li>
                                    <li><a class="dropdown-item text-dark small" href="XXXXXXXX">TEMP</a></li>
                                    <li><a class="dropdown-item text-dark small" href="XXXXXXX">TEMP</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark fs-5" href="XXXXXXXXX">TEMP 2</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark fs-5" href="XXXXXXXXX">TEMP 3</a>
                            </li>
                            <!-- LOGOUT BUTTON -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-dark fs-5" href="xxxxxx" data-bs-toggle="dropdown"><?php echo $_SESSION['username'] ?></a>
                                <ul class="dropdown-menu play-dropdown text-center">
                                    <li><a class="dropdown-item text-dark" href="logout.php">Logout</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

        <div class="container mt-5 mb-5">
            <div class="row justify-content-center">
                <div class="col-8 col-lg-6">
                    <div class="card rounded-0 shadow-sm">
                        <h3 class="card-header text-center">Welcome To PROJECT NAME</h3>
                        <div class="card-body">
                            <h5 class="card-text text-center">Hello, <?php echo $_SESSION['username']; ?>!</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER IF NEEDED LATER
        <footer class="text-center text-lg-start text-white shadow-lg mt-auto" style="background-color: #3f4348">
            <div class="text-center p-3 pt-2">
                <p class="mb-0 text-white">XXXXXXXXXX</p>
                <a class="mb-0 me-2 text-white footer-link" href="XXXXXXXXX" target="_blank">XXXXXXXXX</a>
            </div>
        </footer> -->

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    </body>
</html>