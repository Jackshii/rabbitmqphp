<?php 

session_start();

if (!isset($_SESSION['sessionToken'])) {
    header("Location: login.php");
    exit();
}

?>

<!-- HTML BELOW -->
<?php 
    $pageTitle = 'Saved Recipes';
    require_once('../head.php');
?>
    <!-- CARD BELOW -->
        <div class="container mt-5 mb-5">
            <div class="row justify-content-center">
                <div class="col-8 col-lg-6">
                    <div class="card rounded-0 shadow-sm">
                        <h3 class="card-header text-center"><?php echo $_SESSION['username']; ?>'s Saved Recipes!</h3>
                        <div class="card-body">
                            <h5 class="card-text text-center">LIST THEM OUT MAYBE TABLE</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php require_once('../footer.php'); ?>