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
    require_once('head.php');
?>

    <!-- CARD BELOW -->
     <!-- have it so it shows two buttons, search by ingredients or fridge, and then it redirects them to those separate pages or should that be done on homepage???? -->
        

<?php require_once('footer.php'); ?>