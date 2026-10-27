<?php 
    session_start();
    if (!isset($_SESSION['sessionToken'])) {
        header("Location: login.php");
        exit();
    }

    require ('../path.inc');
    require ('../get_host_info.inc');
    require ('../rabbitMQLib.inc');

    $recipes = [];
    $failed = false;

    $request = array();
    // ask eric if hes storing this data or just caching it
    $request['type'] = 'getFavorites';
    $request['session_key'] = $_SESSION['sessionToken'];

    $phpClient = new rabbitMQClient("../apiBroker.ini", "dataServer");
    $response = $phpClient -> send_request ($request);

    if (is_array($response) && $response['status'] == 'success') {
        $recipes = $response['favorites'] ?? [];
    } else {
        $failed = true;
    }
?>

<!-- HTML BELOW -->
<?php 
    $pageTitle = 'My Recipes';
    require_once('../head.php');
?>

    <!-- DEBUG -->
    <pre class="bg-light border m-3 p-2 small"><?php var_dump($response ?? 'no response'); ?></pre>

        <!-- CARD BELOW -->
        <div class="container mt-5 mb-5">
            <div class="row justify-content-center">
                <div class="col-8 col-lg-6 mt-5">
                    <div class="card rounded-0 shadow-sm mt-5">
                        <h3 class="card-header text-center"><?php echo htmlspecialchars($_SESSION['username']); ?>'s Saved Recipes!</h3>
                        <div class="card-body">

                            <?php
                                if (!$failed) {
                                    if (count($recipes) === 0) { 
                            ?>
                                    <div class="alert alert-warning text-center mt-3">No Saved Recipes Found!
                                        <p class="mt-1" style="font-size: 0.75em" >Click The Star Button On Your Favorite Recipe To Add It</p>
                                    </div>
                            <?php 
                                    } else {
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
                                    }
                                } else {
                            ?>
                                <div class="d-flex justify-content-center">
                                    <div class="alert alert-danger mb-0 mt-3 text-center py-2 px-3 w-auto d-inline-block">Failed To Load Favorites, Try Again!</div>
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