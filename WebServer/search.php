<?php 

session_start();

if (!isset($_SESSION['sessionToken'])) {
    header("Location: login.php");
    exit();
}

require ('../path.inc');
require ('../get_host_info.inc');
require ('../rabbitMQLib.inc');

$action = $_POST['action'] ?? '';
$recipes = [];
$searched = false;
$message = "";

if ($action === 'recipeSearch'){
    $searched = true;
    $request = array();
    $request['type'] = 'recipeSearch';
    $request['session_key'] = $_SESSION['sessionToken'];
    $request['query'] = trim($_POST['recipeQuery'] ?? ''); // ??????

    $phpClient = new rabbitMQClient("../apiBroker.ini", "dataServer");
    $response = $phpClient -> send_request ($request);

    if (is_array($response) && $response['status'] == 'success') {
        $recipes = $response['recipes'] ?? [];
        // HAVE ERIC SEND BACK THE LIST OF RECIPES
    } else {
        $message = "Failed";
    }
}

if ($action === 'fridgeSearch') {
    $searched = true;
    $request = array();
    $request['type'] = 'fridgeSearch';
    $request['session_key'] = $_SESSION['sessionToken'];

    $phpClient = new rabbitMQClient("../apiBroker.ini", "dataServer");
    $response = $phpClient -> send_request ($request);

    if (is_array($response) && $response['status'] == 'success') {
        $recipes = $response['recipes'] ?? [];
        // HAVE ERIC SEND BACK THE LIST OF RECIPES
    } else {
        $message = "Failed";
    }
}
?>

<!-- HTML BELOW -->
<?php 
    $pageTitle = 'Search';
    require_once('../head.php');
?>

    <!-- CARD BELOW -->
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-8 col-lg-6 mt-5">
                <div class="card rounded-0 shadow-sm mt-5">
                    <h3 class="card-header text-center">Search For A Recipe</h3>
                    <div class="card-body">
                        <!-- BUTTONS -->
                        <?php if ($action === '') { ?>
                            <form action="search.php" method="POST" >
                                <div class="text-center mt-4 mb-3">
                                    <button type="submit" name="action" value="searchByRecipe" class="btn btn-lg rounded-0 border-dark border-3 fs-5">Search By Recipe</button>
                                </div>
                                <div class="text-center mt-4 mb-3">    
                                    <button type="submit" name="action" value="fridgeSearch" class="btn btn-lg rounded-0 border-dark border-3 fs-5">Search By Fridge</button>
                                </div>                                
                            </form>
                        <?php } ?>

                        <!-- SEARCH BOX -->
                        <?php if ($action === 'searchByRecipe' || $action === 'recipeSearch') { ?>
                            <form action="search.php" method="POST" >
                                <div class="mb-3">
                                    <label for="recipeQuery" class="form-label">Recipe</label>
                                    <input type="text" name="recipeQuery" maxlength="30" class="form-control" id="recipeQuery" placeholder="ex. Lasagna" required>
                                </div>
                                <div class="text-center mt-4">
                                    <button type="submit" name="action" value="recipeSearch" class="contact-submit-button btn btn-primary border-dark">Search</button>
                                </div>
  
                            </form>
                        <?php } ?>

                        <!-- XXXXXXXXXXXXXXXXXXXX -->
                         <!-- DEBUG: remove later -->
                        <?php if ($searched) { ?>
                            <pre class="bg-light border mt-3 p-2 small"><?php echo htmlspecialchars(var_export($response ?? 'no response', true)); ?></pre>
                        <?php } ?>

                        <!-- RESULTS -->
                        <?php if ($searched) { ?>
                            <?php if ($message === "Failed") { ?>
                                <div class="alert alert-danger text-center mt-3">Search failed, try again.</div>
                            <?php } elseif (count($recipes) === 0) { ?>
                                <div class="alert alert-warning text-center mt-3">No recipes found.</div>
                            <?php } ?>

                            <div class="list-group mt-3">
                                <?php foreach ($recipes as $recipe) { ?>
                                    <a href="recipe.php?id=<?php echo urlencode($recipe['id']); ?>"
                                    class="list-group-item list-group-item-action d-flex align-items-center gap-3">
                                        <img src="<?php echo htmlspecialchars($recipe['image']); ?>" width="64" height="64" class="rounded" alt="">
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($recipe['label']); ?></div>
                                            <small class="text-muted"><?php echo (int)$recipe['calories']; ?> cal · <?php echo (int)$recipe['time']; ?> min</small>
                                        </div>
                                    </a>
                                <?php } ?>
                            </div>
                        <?php } ?>

                        <!-- BACK TO THE TWO BUTTONS -->
                        <?php if ($action !== '') { ?>
                            <div class="text-center mt-3">
                                <a href="search.php">← Start a new search</a>
                            </div>
                        <?php } ?>

                        <!-- XXXXXXXXXXXXXXXxx -->


                    </div>
                </div>
            </div>
        </div>
    </div>       

<?php require_once('../footer.php'); ?>