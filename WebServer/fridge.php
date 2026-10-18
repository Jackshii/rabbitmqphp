<?php 

session_start();
if (!isset($_SESSION['sessionToken'])) {
    header("Location: login.php");
    exit();
}

require ('../path.inc');
require ('../get_host_info.inc');
require ('../rabbitMQLib.inc');

$message = "";
$savedFridge = [];

$phpClient = new rabbitMQClient("../apiBroker.ini", "dataServer");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $request = array();
    $request['type'] = 'setFridge';
    $request['session_key'] = $_SESSION['sessionToken'];
    $request['ingredientInfo'] = $_POST['fridge'] ?? [];

    $response = $phpClient -> send_request ($request);

    if (is_array($response) && $response['status'] == 'success') {
        header("Location: fridge.php?saved=1");
        exit();
    } else {
        $message = "Failed";
        $savedFridge = $_POST['fridge'] ?? [];
    }
} else {
    $request = array();
    $request['type'] = 'getFridge';
    $request['session_key'] = $_SESSION['sessionToken'];

    $response = $phpClient -> send_request ($request);

    if (is_array($response) && $response['status'] == 'success') {
    $savedFridge = $response['ingredientInfo'] ?? [];
    }
}
?>

<!-- HTML BELOW -->
<?php 
    $pageTitle = 'My Fridge';
    require_once('../head.php');
?>
<!-- DEBUG: remove later -->
<pre class="bg-light border m-3 p-2 small"><?php echo htmlspecialchars(var_export($response ?? 'no response', true)); ?></pre>

        <!-- CARD BELOW -->
        <div class="container mt-5 mb-5">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8 mt-5">
                    <div class="card rounded-0">
                        <h3 class="card-header text-center">Enter Ingredients In Your Fridge Below</h3>
                        <div class="card-body">
                            <form action="fridge.php" method="POST">
                                <?php if (isset($_GET['saved'])) { ?>
                                    <div class="d-flex justify-content-center">
                                        <div class="alert alert-success mb-0 mt-3 text-center py-2 px-3 d-inline-block">Fridge Ingredients Saved!</div>
                                    </div>
                                <?php } elseif ($message === "Failed") { ?>
                                    <div class="d-flex justify-content-center">
                                        <div class="alert alert-danger mb-0 mt-3 text-center py-2 px-3 d-inline-block">Error, Try Again!</div>
                                    </div>
                                <?php } ?>
                                <div class="row mt-3">
                                    <div class="col-md-6 d-flex justify-content-center">
                                        <div class="me-5">
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="fridge[]" value="butter" id="fridgeButter" <?php if (in_array('butter', $savedFridge)) echo 'checked'; ?>>
                                                <label class="form-check-label" for="fridgeButter">Butter</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="fridge[]" value="milk" id="fridgeMilk" <?php if (in_array('milk', $savedFridge)) echo 'checked'; ?>>
                                                <label class="form-check-label" for="fridgeMilk">Milk</label>
                                            </div>
                                            
                                            <!-- add all from left colm here -->
                                        </div>
                                    </div>
                                    <div class="col-md-6 d-flex justify-content-center">
                                        <div class="me-5">
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="fridge[]" value="chicken" id="fridgeChicken" <?php if (in_array('chicken', $savedFridge)) echo 'checked'; ?>>
                                                <label class="form-check-label" for="fridgeChicken">Chicken</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="fridge[]" value="beef" id="fridgeBeef" <?php if (in_array('beef', $savedFridge)) echo 'checked'; ?>>
                                                <label class="form-check-label" for="fridgeBeef">Beef</label>
                                            </div>
                                            <!-- add all from right colm here -->
                                        </div>
                                    </div>
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