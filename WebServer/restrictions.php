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

<!-- DEBUG -->
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
                                    if (isset($_GET['saved'])) {
                                ?>
                                    <div class="d-flex justify-content-center">
                                        <div class="alert alert-success mt-3 text-center py-2 px-3 d-inline-block">Dietary Restrictions Saved!</div>
                                    </div>
                                <?php
                                    }
                                    elseif ($message === "Failed") {
                                ?>        
                                <div class="d-flex justify-content-center">
                                    <div class="alert alert-danger mt-3 text-center py-2 px-3 d-inline-block">Error Saving, Try Again!</div>
                                </div>
                                <?php
                                    }
                                    elseif ($message === "LoadFailed") {
                                ?>        
                                <div class="d-flex justify-content-center">
                                    <div class="alert alert-danger mt-3 text-center py-2 px-3 d-inline-block">Error Loading, Try Refreshing The Page!</div>
                                </div>
                                <?php
                                    }
                                ?>
                                <div class="fs-4 my-2 ms-4 d-flex justify-content-center">
                                    <div class="me-5">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="diet[]" value="balanced" id="dietBalanced" <?php if (in_array('balanced', $savedDiet)) echo 'checked'; ?>>
                                            <label class="form-check-label" for="dietBalanced">Balanced</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="diet[]" value="high-fiber" id="dietHighFiber" <?php if (in_array('high-fiber', $savedDiet)) echo 'checked'; ?>>
                                            <label class="form-check-label" for="dietHighFiber">High Fiber</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="diet[]" value="high-protein" id="dietHighProtein" <?php if (in_array('high-protein', $savedDiet)) echo 'checked'; ?>>
                                            <label class="form-check-label" for="dietHighProtein">High Protein</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="diet[]" value="low-carb" id="dietLowCarb" <?php if (in_array('low-carb', $savedDiet)) echo 'checked'; ?>>
                                            <label class="form-check-label" for="dietLowCarb">Low Carb</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="diet[]" value="low-fat" id="dietLowFat" <?php if (in_array('low-fat', $savedDiet)) echo 'checked'; ?>>
                                            <label class="form-check-label" for="dietLowFat">Low Fat</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary border-dark">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php require_once('../footer.php'); ?>