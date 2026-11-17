<?php
    session_start();
    if (!isset($_SESSION['sessionToken'])) {
        header("Location: login.php");
        exit();
    }

    require('../path.inc');
    require('../get_host_info.inc');
    require('../rabbitMQLib.inc');

    $message = "";
    $savedRecommendations = [];

    $phpClient = new rabbitMQClient("../apiBroker.ini", "dataServer");

    $request = array();
    $request['type'] = 'recommendationsRequest';
    $request['session_key'] = $_SESSION['sessionToken'];
    $response = $phpClient->send_request($request);

    if (is_array($response) && $response['status'] == 'success') {
        $savedRecommendations = $response['recipes'] ?? [];
    } else {
        $message = "Failed";
    }
?>

<!-- HTML BELOW -->
<?php
    $pageTitle = 'Recommendations';
    require_once('../head.php');
?>

    <!-- DEBUG -->
    <pre class="bg-light border m-3 p-2 small"><?php var_dump($response ?? 'no response'); ?></pre>

        <!-- CARD BELOW -->
        <div class="container mt-5 mb-5">
            <div class="row justify-content-center">
                <div class="col-8 col-lg-6 mt-5">
                    <div class="card rounded-0 shadow-sm mt-5">
                        <h3 class="card-header text-center">Recommendations For <?php echo htmlspecialchars($_SESSION['username']); ?></h3>
                        <div class="card-body">

                            <!-- RESULTS -->
                            <?php
                                if ($message === "Failed") {
                            ?>
                                <div class="d-flex justify-content-center">
                                    <div class="alert alert-danger mt-3 text-center py-2 px-3 d-inline-block">Could Not Load Recommendations, Try Again!</div>
                                </div>
                            <?php
                                } elseif (count($savedRecommendations) === 0) {
                            ?>
                                <div class="d-flex justify-content-center">
                                    <div class="alert alert-warning mt-3 text-center py-2 px-3 d-inline-block">No Recommendations Found!
                                        <p class="my-1" style="font-size: 0.75em" >Try Favoriting Some Recipes</p>
                                    </div>
                                </div>
                            <?php
                                }
                            ?>

                            <!-- LIST OF RECIPES -->
                            <?php
                                foreach ($savedRecommendations as $recipe) {
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
                        </div>
                    </div>
                </div>
            </div>
        </div>

 <?php require_once('../footer.php'); ?>