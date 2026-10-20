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
        $request['fridgeInfo'] = $_POST['fridge'] ?? [];
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
        $savedFridge = $response['fridgeInfo'] ?? [];
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
                                    <div class="d-flex justify-content-center flex-wrap gap-5 mt-3">
                                        <div class="me-5">
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="fridge[]" value="butter" id="fridgeButter" <?php if (in_array('butter', $savedFridge)) echo 'checked'; ?>>
                                                <label class="form-check-label" for="fridgeButter">Butter</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="fridge[]" value="milk" id="fridgeMilk" <?php if (in_array('milk', $savedFridge)) echo 'checked'; ?>>
                                                <label class="form-check-label" for="fridgeMilk">Milk</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="fridge[]" value="cream" id="fridgeCream" <?php if (in_array('cream', $savedFridge)) echo 'checked'; ?>>
                                                <label class="form-check-label" for="fridgeCream">Cream</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="fridge[]" value="egg" id="fridgeEggs" <?php if (in_array('egg', $savedFridge)) echo 'checked'; ?>>
                                                <label class="form-check-label" for="fridgeEggs">Eggs</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="fridge[]" value="cheese" id="fridgeCheese" <?php if (in_array('cheese', $savedFridge)) echo 'checked'; ?>>
                                                <label class="form-check-label" for="fridgeCheese">Cheese</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="fridge[]" value="onion" id="fridgeOnion" <?php if (in_array('onion', $savedFridge)) echo 'checked'; ?>>
                                                <label class="form-check-label" for="fridgeOnion">Onion</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="fridge[]" value="garlic" id="fridgeGarlic" <?php if (in_array('garlic', $savedFridge)) echo 'checked'; ?>>
                                                <label class="form-check-label" for="fridgeGarlic">Garlic</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="fridge[]" value="lettuce" id="fridgeLettuce" <?php if (in_array('lettuce', $savedFridge)) echo 'checked'; ?>>
                                                <label class="form-check-label" for="fridgeLettuce">Lettuce</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="fridge[]" value="potato" id="fridgePotato" <?php if (in_array('potato', $savedFridge)) echo 'checked'; ?>>
                                                <label class="form-check-label" for="fridgePotato">Potato</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="fridge[]" value="corn" id="fridgeCorn" <?php if (in_array('corn', $savedFridge)) echo 'checked'; ?>>
                                                <label class="form-check-label" for="fridgeCorn">Corn</label>
                                            </div>
                                        </div>
                                        <div class="me-5">
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="fridge[]" value="chicken" id="fridgeChicken" <?php if (in_array('chicken', $savedFridge)) echo 'checked'; ?>>
                                                <label class="form-check-label" for="fridgeChicken">Chicken</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="fridge[]" value="beef" id="fridgeBeef" <?php if (in_array('beef', $savedFridge)) echo 'checked'; ?>>
                                                <label class="form-check-label" for="fridgeBeef">Beef</label>
                                            </div>
                                             <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="fridge[]" value="pork" id="fridgePork" <?php if (in_array('pork', $savedFridge)) echo 'checked'; ?>>
                                                <label class="form-check-label" for="fridgePork">Pork</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="fridge[]" value="bacon" id="fridgeBacon" <?php if (in_array('bacon', $savedFridge)) echo 'checked'; ?>>
                                                <label class="form-check-label" for="fridgeBacon">Bacon</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="fridge[]" value="fish" id="fridgeFish" <?php if (in_array('fish', $savedFridge)) echo 'checked'; ?>>
                                                <label class="form-check-label" for="fridgeFish">Fish</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="fridge[]" value="olive oil" id="fridgeOliveOil" <?php if (in_array('olive oil', $savedFridge)) echo 'checked'; ?>>
                                                <label class="form-check-label" for="fridgeOliveOil">Olive Oil</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="fridge[]" value="soy sauce" id="fridgeSoySauce" <?php if (in_array('soy sauce', $savedFridge)) echo 'checked'; ?>>
                                                <label class="form-check-label" for="fridgeSoySauce">Soy Sauce</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="fridge[]" value="chili oil" id="fridgeChilliOil" <?php if (in_array('chili oil', $savedFridge)) echo 'checked'; ?>>
                                                <label class="form-check-label" for="fridgeChilliOil">Chilli Oil</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="fridge[]" value="rice" id="fridgeRice" <?php if (in_array('rice', $savedFridge)) echo 'checked'; ?>>
                                                <label class="form-check-label" for="fridgeRice">Rice</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" name="fridge[]" value="beans" id="fridgeBeans" <?php if (in_array('beans', $savedFridge)) echo 'checked'; ?>>
                                                <label class="form-check-label" for="fridgeBeans">Beans</label>
                                            </div>
                                        </div>
                                    </div>
                                <div class="text-center mt-4">
                                    <button type="submit" class="contact-submit-button btn btn-primary border-dark">Save</button>
                                </div>
                                <!-- FIX ABOVE LAYOUT -->
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php require_once('../footer.php'); ?>