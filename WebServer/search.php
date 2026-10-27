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

    if ($action === 'recipeSearch') {
        $searched = true;
        $request = array();
        $request['type'] = 'recipeSearch';
        $request['session_key'] = $_SESSION['sessionToken'];
        $request['query'] = trim($_POST['recipeQuery'] ?? '');

        $phpClient = new rabbitMQClient("../apiBroker.ini", "dataServer");
        $response = $phpClient -> send_request ($request);

        if (is_array($response) && $response['status'] == 'success') {
            $recipes = $response['recipes'] ?? [];
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
                        <?php
                            if ($action === '') { 
                        ?>
                            <form action="search.php" method="POST" >
                                <div class="text-center mt-4 mb-3">
                                    <button type="submit" name="action" value="searchByRecipe" class="btn btn-lg rounded-0 border-dark border-3 fs-5">Search By Recipe</button>
                                </div>
                                <div class="text-center mt-4 mb-3">    
                                    <button type="submit" name="action" value="fridgeSearch" class="btn btn-lg rounded-0 border-dark border-3 fs-5">Search By Fridge</button>
                                </div>                                
                            </form>
                        <?php  
                            }
                        ?>

                        <!-- RECIPE SEARCHBOX -->
                        <?php 
                            if ($action === 'searchByRecipe' || $action === 'recipeSearch') { 
                        ?>
                            <form action="search.php" method="POST">
                                <div class="mb-3">
                                    <label for="recipeQuery" class="form-label">Recipe</label>
                                    <input type="text" name="recipeQuery" maxlength="30" class="form-control" id="recipeQuery" placeholder="ex. Lasagna" required>
                                </div>
                                <div class="text-center mt-4">
                                    <button type="submit" name="action" value="recipeSearch" class="contact-submit-button btn btn-primary border-dark">Search</button>
                                </div>
                            </form>
                        <?php
                            }
                        ?>
                        
                         <!-- DEBUG -->
                        <?php 
                        if ($searched) { 
                        ?>
                            <pre class="bg-light border mt-3 p-2 small"><?php echo htmlspecialchars(var_export($response ?? 'no response', true)); ?></pre>
                        <?php 
                            } 
                        ?>

                        <!-- RESULTS -->
                        <?php
                            if ($searched === true) {
                                if ($message === "Failed") { 
                        ?>
                                <div class="alert alert-danger text-center mt-3">Error Searching, Try Again!</div>
                        <?php   
                                } elseif (count($recipes) === 0) { 
                        ?>
                                <div class="alert alert-warning text-center mt-3">No Recipes Found
                                    <p class="mt-1" style="font-size: 0.75em" >Try searching for another recipe</p>
                                </div>
                        <?php 
                                }
                            }
                        ?>

                        <!-- LIST OF RECIPES -->
                        <?php
                            foreach ($recipes as $recipe) {
                                $id = $recipe['id'] ?? '';
                                $name = $recipe['name'] ?? 'Recipe Name Not Available';
                                $image = $recipe['image'] ?? '';
                                $time = $recipe['totalTime'] ?? 0;
                                $cals = round($recipe['average_calories'] ?? 0);
                                if ($time > 0 ) {
                                    $time = $time . " Minutes";
                                } else {
                                    $time = 'Time N/A';
                                }
                        ?>
                            <div class="my-3">    
                                <a class="list-group-item list-group-item-action d-flex align-items-center gap-3" target="_blank" href="recipe.php?id=<?php echo urlencode($id); ?>">
                                    <img src="<?php echo htmlspecialchars($image); ?>" width="64" height="64" class="object-fit-cover flex-shrink-0 rounded-0" alt="Recipe Image">
                                    <div>
                                        <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($name); ?></h6>
                                        <p class="text-muted mb-0"><?php echo htmlspecialchars($cals); ?> Calories  -  <?php echo htmlspecialchars($time); ?></p>
                                    </div>
                                </a>
                            </div>
                        <?php 
                            }
                        ?>

                        <!-- BACK TO THE TWO BUTTONS -->
                        <?php
                            if ($action !== '') {
                        ?>
                            <div class="text-center mt-3">
                                <a href="search.php">Search Again</a>
                            </div>
                        <?php
                            }
                        ?>

                    </div>
                </div>
            </div>
        </div>
    </div>       

<?php require_once('../footer.php'); ?>