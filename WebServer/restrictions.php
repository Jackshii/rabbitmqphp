<?php 

// Eric needs to make a db that holds a users session token along with their diet restrictions, an array would be nice as it is only 5 values that are on or off. Also make sure SQL statements are prepared statements using the ? so no injection can take place. Also I just noticed you may need to just use the session key to do a lookup of the user that the session belongs to and then store the username or userID with their diets. Since our sessions keys will change with login logout but username wont. Damn we have a lot to do
// Jackie needs to create a restrictionServer in authBroker.ini and make Rabbit work somehow IDK
// Jack needs to be able to append these diet restrictions from the user to the api call. HINT: the values that i have set being sent to the DB are the exact one the API call for (i think)

session_start();
if (!isset($_SESSION['sessionToken'])) {
    header("Location: login.php");
    exit();
}

require ('../path.inc');
require ('../get_host_info.inc');
require ('../rabbitMQLib.inc');

$message = "";
$savedDiet = [];

$phpClient = new rabbitMQClient("../apiBroker.ini", "dataServer");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $request = array();
    $request['type'] = 'setDiet';
    $request['session_key'] = $_SESSION['sessionToken'];
    $request['dietInfo'] = $_POST['diet'] ?? [];

    $response = $phpClient -> send_request ($request);

    if (is_array($response) && $response['status'] == 'success') {
        header("Location: restrictions.php?saved=1");
        exit();
    } else {
        $message = "Failed";
        $savedDiet = $_POST['diet'] ?? [];
    }
} else {
    $request = array();
    $request['type'] = 'getDiet';
    $request['session_key'] = $_SESSION['sessionToken'];

    $response = $phpClient -> send_request ($request);

    if (is_array($response) && $response['status'] == 'success') {
    $savedDiet = $response['dietInfo'] ?? [];
    } else {
        $message = "LoadFailed";
    }
}
?>

<!-- HTML BELOW -->
<?php 
    $pageTitle = 'Dietary Restrictions';
    require_once('../head.php');
?>
<!-- DEBUG: remove later -->
<pre class="bg-light border m-3 p-2 small"><?php echo htmlspecialchars(var_export($response ?? 'no response', true)); ?></pre>

    <!-- CARD BELOW -->
        <div class="container mt-5 mb-5">
            <div class="row justify-content-center">
                <div class="col-10 col-lg-6 mt-5">
                    <div class="card rounded-0 mt-5">
                        <h3 class="card-header text-center">Enter Dietary Restrictions Below</h3>
                        <div class="card-body">
                            <form action="restrictions.php" method="POST">
                                <?php
                                    if (isset($_GET['saved'])){
                                ?>
                                    <div class="d-flex justify-content-center">
                                        <div class="alert alert-success mb-0 mt-3 text-center py-2 px-3 d-inline-block">Dietary Restrictions Saved!</div>
                                    </div>
                                <?php    }
                                    elseif ($message === "Failed"){
                                ?>        
                                    <div class="d-flex justify-content-center">
                                        <div class="alert alert-danger mb-0 mt-3 text-center py-2 px-3 d-inline-block">Error Saving, Try Again!</div>
                                    </div>
                                <?php
                                    } elseif ($message === "LoadFailed"){
                                ?>        
                                    <div class="d-flex justify-content-center">
                                        <div class="alert alert-danger mb-0 mt-3 text-center py-2 px-3 d-inline-block">Error Loading, Try Refreshing The Page!</div>
                                    </div>
                                <?php
                                    } else
                                ?>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="diet[]" value="balanced" id="dietBalanced" <?php if (in_array('balanced', $savedDiet)) echo 'checked'; ?>>
                                    <label class="form-check-label text-center fs-4 my-3" for="dietBalanced">Balanced</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="diet[]" value="high-fiber" id="dietHighFiber" <?php if (in_array('high-fiber', $savedDiet)) echo 'checked'; ?>>
                                    <label class="form-check-label text-center fs-4 my-3" for="dietHighFiber">High Fiber</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="diet[]" value="high-protein" id="dietHighProtein" <?php if (in_array('high-protein', $savedDiet)) echo 'checked'; ?>>
                                    <label class="form-check-label text-center fs-4 my-3" for="dietHighProtein">High Protein</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="diet[]" value="low-carb" id="dietLowCarb" <?php if (in_array('low-carb', $savedDiet)) echo 'checked'; ?>>
                                    <label class="form-check-label text-center fs-4 my-3" for="dietLowCarb">Low Carb</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="diet[]" value="low-fat" id="dietLowFat" <?php if (in_array('low-fat', $savedDiet)) echo 'checked'; ?>>
                                    <label class="form-check-label text-center fs-4 my-3" for="dietLowFat">Low Fat</label>
                                </div>
                                <div class="text-center mt-4">
                                    <button type="submit" class="contact-submit-button btn btn-primary border-dark">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php require_once('../footer.php'); ?>