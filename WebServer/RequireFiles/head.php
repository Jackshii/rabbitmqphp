<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo htmlspecialchars($pageTitle) ?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    </head>
    <body class="d-flex flex-column min-vh-100">
        <header>
            <nav class="navbar navbar-expand-md text-black shadow-sm border-bottom bg-white">
                <div class="container-fluid px-3">
                    <a class="navbar-brand" href="home.php">Project DORA</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end" id="mainNav">
                        <ul class="navbar-nav ms-auto gap-2 text-center">
                            <li class="nav-item">
                                <a class="nav-link text-dark text-nowrap <?php if ($pageTitle === 'Search'){ ?>fw-bold<?php } ?>" href="search.php">Search</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark text-nowrap <?php if ($pageTitle === 'Recommendations'){ ?>fw-bold<?php } ?>" href="recommend.php">Recommendations</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark text-nowrap <?php if ($pageTitle === 'My Recipes'){ ?>fw-bold<?php } ?>" href="favorites.php">My Recipes</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark text-nowrap <?php if ($pageTitle === 'My Fridge'){ ?>fw-bold<?php } ?>" href="fridge.php">My Fridge</a>
                            </li>
                             <li class="nav-item">
                                <a class="nav-link text-dark text-nowrap <?php if ($pageTitle === 'Dietary Restrictions'){ ?>fw-bold<?php } ?>" href="restrictions.php">Dietary Restrictions</a>
                            </li>

                            <!-- LOGOUT BUTTON -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-dark text-nowrap text-decoration-underline" data-bs-toggle="dropdown"><?php echo htmlspecialchars($_SESSION['username']) ?></a>
                                <ul class="dropdown-menu play-dropdown text-center start-50 translate-middle-x">
                                    <li><a class="dropdown-item text-dark px-1" href="logout.php" style=".play-dropdown {min-width: 0; width: max-content;}">Logout</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>