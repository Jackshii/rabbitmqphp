#!/usr/bin/php
<?php
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');
require_once('../connection.php');
function getU($sql, $session) {
    $id = $sql->query("select id from logininfo where sessionkey = '$session'");
    
    if ($userid = $id->fetch_assoc()) {
        return $userid['id'];
    }

    return null;
}

function allreq($request) {
    global $Host, $User, $Pass, $Name;
    $db = new mysqli($Host, $User, $Pass, $Name);    
    $type = $request['type'];
    $session = $request['session_key'];

    $userId = getU($sql, $session);

    if ($type == 'getDiet') {
        $result = $sql->query("select diet from userdiet where userid = '$userId'");
        
        $getD = [];
        while ($dietinfo = $result->fetch_assoc()) {
            $getD[] = $dietinfo['diet'];
        }

        $dietA = array();
        $dietA['status'] = "success";
        $dietA['dietInfo'] = $getD;
        return $dietA;

    } 
    else if ($type == 'setDiet') {
        $info = $request['dietInfo'];
        $sql->query("delete from userdiet where userid = '$userId'");

        foreach ($info as $diet) {
            $dietL = trim($diet);
            if (!empty($dietL)) {
                $sql->query("insert into userdiet (userid, diet) values ('$userId', '$dietL')");
            }
        }
        
        $result = $sql->query("select diet from userdiet where userid = '$userId'");
        
        $setD = [];
        while ($dietinfo = $result->fetch_assoc()) {
            $setD[] = $dietinfo['diet'];
        }

        $dietA = array();
        $dietA['status'] = "success";
        $dietA['dietInfo'] = $setD;
        return $dietA;

    } 
    else if ($type == 'getFridge') {
        $result = $sql->query("select ingredient from userfridge where userid = '$userId'");
        
        $getF = [];
        while ($fridgeinfo = $result->fetch_assoc()) {
            $getF[] = $fridgeinfo['ingredient'];
        }

        $fridgeA = array();
        $fridgeA['status'] = "success";
        $fridgeA['fridgeInfo'] = $getF;
        return $fridgeA;

    } 
    else if ($type == 'setFridge') {
        $info = $request['fridgeInfo'];
        $sql->query("delete from userfridge where userid = '$userId'");
        
        foreach ($info as $ingredient) {
            $ingredientL = trim($ingredient);
            if (!empty($ingredientL)) {
                $sql->query("insert into userfridge (userid, ingredient) values ('$userId', '$ingredientL')");
            }
        }
        
        $result = $sql->query("select ingredient from userfridge where userid = '$userId'");
        $updateF = [];
        while ($ingredientinfo = $result->fetch_assoc()) {
            $updateF[] = $ingredientinfo['ingredient'];
        }

        $fridgeA = array();
        $fridgeA['status'] = "success";
        $fridgeA['message'] = "Fridge updated";
        $fridgeA['fridgeInfo'] = $updateF;
        return $fridgeA;
    }
    else if ($type == 'recipeSearch' || $type == 'fridgeSearch'|| $type == 'detailedSearch') {
        var_dump($request);
        $dietResult = $sql->query("select diet from userdiet where userid = '$userId'");
        $diets = [];
        while ($dietinfo = $dietResult->fetch_assoc()) {
            $diets[] = $dietinfo['diet'];
        }
        var_dump($diets);
        $searchQ = '';
        if ($type == 'recipeSearch') {
        $searchQ = $request['query'];
                var_dump($searchQ);
        }
        else if ($type == 'fridgeSearch') {
            $fridgeResult = $sql->query("select ingredient from userfridge where userid = '$userId'");
          $fridgeItems = [];
          foreach ($fridgeResult as $row) {
            $fridgeItems[] = $row['ingredient'];
            }
                
                var_dump($fridgeItems);
                $searchQ = $fridgeItems;
        }
        else if ($type == 'detailedSearch') {
       $searchQuery = $request['recipe_id'];
        }
        
        $request = new rabbitMQClient("../apiBroker.ini", "dmzServer");
        $infoA = array();
        if ($type == 'detailedSearch') {
            $infoA['type'] = "detailedSearch"; 
            $infoA['id']   = $searchQuery;
            }
        else{
        $infoA['type'] = "recipeSearch";
        $infoA['ingredients'] = $searchQuery;
        $infoA['diet'] = $diets;
        }
        var_dump($infoA);

        $getrecipe = $request->send_request($infoA);

        var_dump($getrecipe);

        
        $recipeA = array();
        $recipeA['status'] = "success";
        if ($type == "detailedSearch") {
            $recipeA['recipes']= $getrecipe;
            return $recipeA;
        }
        else{
        $recipeA["recipes"] = $getrecipe['recipeSearch'];
        return $recipeA;
        }
    }
    else {
        $errorA = array();
        $errorA['status'] = "error";
        $errorA['message'] = "error: " . $type;
        return $errorA;
    }
}

$server = new rabbitMQServer("../apiBroker.ini", "dataServer");
$server->process_requests('allreq');
?>


