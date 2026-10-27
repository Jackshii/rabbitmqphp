<?php
    session_start();
    if (!isset($_SESSION['sessionToken'])) {
        header("Location: login.php");
        exit();
    }
?>

<!-- HTML BELOW -->
<?php 
    $pageTitle = 'Home';
    require_once('../head.php');
?>
        <!-- CARD BELOW -->
        <div class="container mt-5 mb-5">
            <div class="row justify-content-center">
                <div class="col-8 col-lg-6">
                    <div class="card rounded-0 shadow-sm">
                        <h3 class="card-header text-center">Welcome To Project DORA</h3>
                        <div class="card-body">
                            <h5 class="card-text text-center mb-3">Hello, <span class="text-decoration-underline"><?php echo htmlspecialchars($_SESSION['username']); ?>!</span></h5>
                            <h4 class="mb-0 mt-2">How To Use Our Site</h4>
                            <hr class="border-2 mx-auto mt-0" style="width: 100%;">
                            <ol class="list-unstyled list-group-numbered">
                                <li class="list-group-item mb-1">Set your dietary restrictions / goals</li>
                                <li class="list-group-item mb-1">Select the ingredients in your fridge</li>
                                <li class="list-group-item mb-1">Search for recipes
                                    <ul class="list-unstyled list-group mb-1">
                                        <li class="mb-1 mx-2">- Search by recipe name</li>
                                        <li class="mb-1 mx-2">- Search by items in your fridge</li>
                                    </ul>
                                </li>
                                <li class="list-group-item mb-1">Save recipes for later</li>
                                <li class="list-group-item mb-1">Leave reviews on recipes</li>
                                <li class="list-group-item mb-0">Check out recommended recipes for you</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php require_once('../footer.php'); ?>