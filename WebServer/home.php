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
                        <h3 class="card-header text-center">Welcome To PROJECT NAME</h3>
                        <div class="card-body">
                            <h5 class="card-text text-center">Hello, <?php echo $_SESSION['username']; ?>!</h5>
                            <h4 class="text-decoration-underlined">How To Use Our Site</h4>
                            <ol class="list-group list-group-numbered">
                                <li class="list-group-item">Create an account</li>
                                <li class="list-group-item">Login with your username and password</li>
                                <li class="list-group-item">Set your dietary restriction / goals</li>
                                <li class="list-group-item">Select the ingredients in your fridge</li>
                                <li class="list-group-item">Search for recipes
                                    <ul class="list-group">
                                        <li class="list-group-item">Search by recipe name</li>
                                        <li class="list-group-item">Search by items in your fridge</li>
                                    </ul>
                                </li>
                                <li class="list-group-item">Save recipes for later</li>
                                <li class="list-group-item">Leave reviews on recipes</li>
                                <li class="list-group-item">Check out recommended recipes for you</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php require_once('../footer.php'); ?>