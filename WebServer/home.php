<?php 

session_start();

if (!isset($_SESSION['sessionToken'])) {
    header("Location: login.php");
    exit();
}

?>

<!-- HTML BELOW -->
<?php 
    $pageTitle = 'Dietary Restrictions';
    require_once('head.php');
?>

        <div class="container mt-5 mb-5">
            <div class="row justify-content-center">
                <div class="col-8 col-lg-6">
                    <div class="card rounded-0 shadow-sm">
                        <h3 class="card-header text-center">Welcome To PROJECT NAME</h3>
                        <div class="card-body">
                            <h5 class="card-text text-center">Hello, <?php echo $_SESSION['username']; ?>!</h5>
                            <!-- maybe info ab how the site works -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php require_once('footer.php'); ?>