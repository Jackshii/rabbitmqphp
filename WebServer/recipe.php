<?php
    session_start();
    if (!isset($_SESSION['sessionToken'])) {
        header("Location: login.php");
        exit();
    }

    require ('../path.inc');
    require ('../get_host_info.inc');
    require ('../rabbitMQLib.inc');

    $id = $_GET['id'] ?? '';

    // ID CHECK
    $idCheck = str_starts_with($id, 'recipe_') && strlen($id) == 39 && ctype_xdigit(substr($id, 7));
    if (!$idCheck ) {
        header('Location: search.php');
        exit();
    }

    $action = $_POST['action'] ?? '';
    $recipe = [];
    $reviews = [];
    $loadFailed = false;
    $isFavorite = false;
    $error = $_GET['error'] ?? '';

    $phpClient = new rabbitMQClient("../apiBroker.ini", "dataServer");

    // FAVORITE SECTION
    if ($action === 'favorited') {
        $request = array();
        $request['type'] = 'setFavorite';
        $request['session_key'] = $_SESSION['sessionToken'];
        $request['id'] = $id;

        $response = $phpClient -> send_request ($request);

        if (is_array($response) && $response['status'] == 'success') {
            header("Location: recipe.php?id=" . $id . "&saved=true");
        } else {
            header("Location: recipe.php?id=" . $id . "&error=save");
        }
        exit();
    }

    // UNFAVORITE SECTION
    if ($action === 'unfavorited') {
        $request = array();
        $request['type'] = 'removeFavorite';
        $request['session_key'] = $_SESSION['sessionToken'];
        $request['id'] = $id;

        $response = $phpClient -> send_request ($request);

        if (is_array($response) && $response['status'] == 'success') {
            header("Location: recipe.php?id=" . $id . "&unsave=true");
        } else {
            header("Location: recipe.php?id=" . $id . "&error=unsave");
        }
        exit();
    }


    // RATE AND REVIEW SECTION
    if ($action === 'review') {
        $rating = (int)($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');

        if ($rating < 1 || $rating > 5) {
            header("Location: recipe.php?id=" . $id . "&error=rating");
            exit();
        }

        $request = array();
        $request['type'] = 'setReview';
        $request['session_key'] = $_SESSION['sessionToken'];
        $request['id'] = $id;
        $request['rating'] = $rating;
        $request['comment'] = $comment;

        $response = $phpClient -> send_request ($request);

        if (is_array($response) && $response['status'] == 'success') {
            header("Location: recipe.php?id=" . $id . "&reviewed=true");
        } else {
            header("Location: recipe.php?id=" . $id . "&error=review");
        }
        exit();
    }

    // DETAILED SEARCH
    $request = array();
    $request['type'] = 'detailedSearch';
    $request['session_key'] = $_SESSION['sessionToken'];
    $request['id'] = $id;

    $response = $phpClient -> send_request ($request);

    if (is_array($response) && $response['status'] == 'success') {
        $recipe = $response['recipes'] ?? [];
        $reviews = $response['reviews'] ?? [];
        $isFavorite = !empty($response['isfavorites']);
    } else {
        $loadFailed = true;
    }

    $name = $recipe['name'] ?? 'Recipe Name Not Found';
    $image = $recipe['image'] ?? '';
    $url = $recipe['url'] ?? '';
    $ingredients = $recipe['ingredients'] ?? [];
    $cuisines = $recipe['cuisineType'] ?? [];
    $diets = $recipe['diet'] ?? [];
    $allergies = $recipe['allergy'] ?? [];
    $totalCals = round($recipe['calories'] ?? 0);
    $avgCals = round($recipe['average_calories'] ?? 0);
    $servings = (int)($recipe['yield'] ?? 0);

    $time = (int)($recipe['totalTime'] ?? 0);
    if ($time > 0 ) {
        $time = $time . " minutes";
    } else {
        $time = "N/A";
    }

    // SET PAGE TITLE
    $pageTitle = $recipe['name'] ?? 'Recipe Details';
?>

<!-- HTML BELOW -->
<?php
    require_once('../head.php');
?>

<!-- DEBUG -->
<pre class="bg-light border m-3 p-2 small">id received: <?php echo htmlspecialchars(var_export($id, true)); ?>
valid: <?php echo $id ? 'NO' : 'yes'; ?></pre>
<pre class="bg-light border m-3 p-2 small"><?php echo htmlspecialchars(var_export($response ?? 'no response', true)); ?></pre>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">

                <!-- LOAD CHECK -->
                <?php 
                    if ($loadFailed) { 
                ?>
                    <div class="d-flex justify-content-center">
                        <div class="alert alert-danger mt-3 text-center py-2 px-3 d-inline-block">Couldn't Find This Recipe, Try Again</div>
                    </div>
                    <div class="text-center"><a href="search.php">Search Again</a></div>

                <?php 
                    } else { 
                ?>

                <!-- MESSAGES -->
                <?php 
                    if (isset($_GET['saved'])) { 
                ?>
                    <div class="d-flex justify-content-center">
                        <div class="alert alert-success mt-3 text-center py-2 px-3 d-inline-block">Recipe Saved!</div>
                    </div>
                <?php 
                    } elseif (isset($_GET['unsave'])) { 
                ?>
                    <div class="d-flex justify-content-center">
                        <div class="alert alert-success mt-3 text-center py-2 px-3 d-inline-block">Recipe Unsaved!</div>
                    </div>
                <?php 
                    } elseif (isset($_GET['reviewed'])) { 
                ?>
                    <div class="d-flex justify-content-center">
                        <div class="alert alert-success mt-3 text-center py-2 px-3 d-inline-block">Rating Saved!</div>
                    </div>
                <?php  
                    } elseif ($error === 'save') { 
                ?>
                    <div class="d-flex justify-content-center">
                        <div class="alert alert-danger mt-3 text-center py-2 px-3 d-inline-block">Save Failed, Try Again</div>
                    </div>
                <?php 
                    } elseif ($error === 'unsave') {
                ?>
                    <div class="d-flex justify-content-center">
                        <div class="alert alert-danger mt-3 text-center py-2 px-3 d-inline-block">Unsave Failed, Try Again</div>
                    </div>
                <?php
                    } elseif ($error === 'review' || $error === 'rating') { 
                ?>
                    <div class="d-flex justify-content-center">
                        <div class="alert alert-danger mt-3 text-center py-2 px-3 d-inline-block">Rating Failed, Try Again</div>
                    </div>
                <?php 
                    } 
                ?>

                <!-- RECIPE CARD -->
                <div class="card rounded-0 shadow-sm">

                    <!-- TITLE AND SAVE BUTTON -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0"><?php echo htmlspecialchars($name); ?></h3>
                        <form action="recipe.php?id=<?php echo urlencode($id); ?>" method="POST">
                            <?php
                                if ($isFavorite) {
                            ?>
                                <button class="btn rounded-0 btn-warning text-nowrap" type="submit" name="action" value="unfavorited">&#9733;  Unsave Recipe</button>
                            <?php
                                } else {
                            ?>
                                <button class="btn rounded-0 btn-warning text-nowrap" type="submit" name="action" value="favorited">&#9734;  Save Recipe</button>
                            <?php
                                }
                            ?>
                        </form>
                    </div>

                    <div class="card-body">
                        <div class="row g-4">

                            <!-- IMAGE -->
                            <div class="col-md-5">
                                <?php
                                    if ($image !== '') { 
                                ?>
                                    <img src="<?php echo htmlspecialchars($image); ?>" class="img-fluid rounded-0" alt="Recipe Image">
                                <?php 
                                    } else {
                                ?>
                                    <div class="bg-light border rounded-0 d-flex align-items-center justify-content-center text-muted" style="height: 250px;">No Image Available</div>
                                <?php
                                    }
                                ?>

                                <!-- EXTERNAL LINK -->
                                <?php
                                    if ($url !== '') { 
                                ?>  
                                    <div class="text-center">
                                        <a href="<?php echo htmlspecialchars($url); ?>" target="_blank" class="btn btn-success mt-3 rounded-0">View Original Recipe Here</a>
                                    </div>
                                <?php 
                                    } else {
                                ?>
                                    <div class="text-center">
                                        <p class="text-muted mt-3">No Link Available</p>
                                    </div>
                                <?php
                                    }
                                ?>
                            </div>

                            <!-- DETAILS -->
                            <div class="col-md-7">
                                <p class="mb-1"><strong>Time:</strong> <?php echo htmlspecialchars($time); ?></p>
                                <p class="mb-1"><strong>Total Calories:</strong> <?php echo htmlspecialchars($totalCals); ?> Cals</p>
                                <?php
                                    if ($servings > 0) {
                                ?>
                                    <p class="mb-1"><strong>Average Calories:</strong> <?php echo $avgCals; ?> Cals Per Serving</p>
                                    <p class="mb-1"><strong>Servings:</strong> <?php echo $servings; ?></p>
                                <?php
                                    }
                                ?>

                                <!-- CUISINE -->
                                <?php
                                    if (count($cuisines) > 0) {
                                ?>
                                    <p class="mb-1"><strong>Cuisine Type:</strong> <?php echo htmlspecialchars(ucwords((implode(', ', $cuisines)))) ?></p>
                                <?php              
                                    }
                                ?>

                                <!-- DIET -->
                                <?php
                                    if (count($diets) > 0) {
                                ?>
                                    <p class="mb-1"><strong>Diet Types:</strong> <?php echo htmlspecialchars((implode(', ', $diets))) ?></p>
                                <?php              
                                    }
                                ?>

                                <!-- ALLERGIES -->
                                <?php
                                    if (count($allergies) > 0) {
                                ?>
                                    <p class="mb-1"><strong>Allergy Info:</strong> <?php echo htmlspecialchars((implode(', ', $allergies))) ?></p>
                                <?php              
                                    }
                                ?>                     
                            </div>
                        </div>

                        <!-- INGREDIENTS -->
                        <h5 class="mt-4">Ingredients</h5>
                        <?php 
                            if (count($ingredients) > 0) { 
                        ?>
                            <ul class="list-group list-group-flush">
                        <?php
                                foreach ($ingredients as $value) { 
                        ?>
                                    <li class="list-group-item"><?php echo htmlspecialchars($value); ?></li>
                        <?php 
                                } 
                        ?>
                            </ul>
                        <?php 
                            } else { 
                        ?>
                            <p class="text-muted">No Ingredients Available</p>
                        <?php 
                            }
                        ?>
                    </div>
                </div>

                <!-- RATING AND REVIEWS FORM -->
                <div class="card rounded-0 shadow-sm mt-4">
                    <h4 class="card-header">Post A Review</h4>
                    <div class="card-body">
                        <form action="recipe.php?id=<?php echo urlencode($id); ?>" method="POST">
                            <div class="mb-3">
                                <label for="rating" class="form-label">Your Rating:</label>
                                <select name="rating" id="rating" class="form-select w-auto" required>
                                    <option value="">Choose here</option>
                                    <option value="1">1 - Horrible</option>
                                    <option value="2">2 - Bad</option>
                                    <option value="3">3 - Average</option>
                                    <option value="4">4 - Great</option>
                                    <option value="5">5 - Excellent</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="comment">Comment (Optional)</label>
                                <textarea name="comment" id="comment" class="form-control" rows="3" maxlength="500"></textarea>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-primary rounded-0" type="submit" name="action" value="review">Submit Rating</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- RATING AND REVIEWS FROM OTHER USERS -->
                <div class="card rounded-0 shadow-sm mt-4">
                    <h4 class="card-header">All Reviews</h4>
                    <div class="card-body">
                        <?php
                            if (count($reviews) === 0) {
                        ?>
                            <div class="text-muted">No Reviews Yet
                                <p class="text-muted" style="font-size: 0.85em">Be The First To Leave A Review!</p>
                            </div>
                        <?php
                            } else {
                                foreach ($reviews as $review) {
                                    $reviewUsername = $review['username'] ?? 'Unknown';
                                    $reviewRating = (int)($review['rating'] ?? 0);
                                    $reviewComment = $review['review'] ?? '';
                        ?>
                                <div class="border-bottom py-2">
                                    <h5 class="fw-bold"><?php echo htmlspecialchars($reviewUsername)?></h5>
                                    <h6><?php echo htmlspecialchars($reviewRating)?> / 5 &#9733;</h6>
                        <?php
                                    if ($reviewComment !== '') {
                        ?>
                                    <p><?php echo htmlspecialchars($reviewComment)?></p>
                        <?php
                                    }
                        ?>
                                </div> 
                        <?php
                                }
                            }
                        ?>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="search.php">Search Again</a>
                </div>

                <?php 
                    } 
                ?>

            </div>
        </div>
    </div>

<?php require_once('../footer.php'); ?>