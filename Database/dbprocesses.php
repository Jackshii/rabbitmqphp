#!/usr/bin/php
<?php
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');
require_once('../connection.php');
function getuser($sql, $session) {
    $id = $sql->query("select id from logininfo where sessionkey = '$session'");
    
    if ($userid = $id->fetch_assoc()) {
        return $userid['id'];
    }

    return null;
}

function allreq($request) {
    global $Host, $User, $Pass, $Name;
    $sql = new mysqli($Host, $User, $Pass, $Name);    
    $type = $request['type'];
    $session = $request['session_key'];

    $userId = getUser($sql, $session);

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
        //Favorite Personal Deliverable
    else if($type=='addFavorite'||$type=='removeFavorite'||$type=="returnFavorites")
    {
    $user=$sql->query("select username from logininfo where sessionkey='$sessionKey'");
    $userArray = $user->fetch_assoc();//convert table to array so we can index and find user
    $username= $userArray['username'];
    $recipe_id=$request['id'];
    
    if ($type == "addFavorite")
    {
        $recipe_name=$request['name'];
        $image=$request['image'];
        $url=$request['url'];
        $ingredients=json_encode($request['ingredients']);
        $diet= json_encode($request['diet']);
        $allergy=json_encode($request['healthLabels']);
        $cuisineType=json_encode($request['cuisineType']);
        $totalTime=$request['totalTime'];
        $average_calories=$request['average_calories'];

        //populate the table if info is there
        if($sql->query("insert into user_favorites (username, recipe_id, recipe_name, image, url, ingredients, diet, allergy, cuisineType, totalTime, average_calories) values 
        ('$username','$recipe_id','$recipe_name','$image','$url','$ingredients','$diet','$allergy','$cuisineType','$totalTime','$average_calories')"))
            {
                return['status'=>'success','message'=>'added to favorites'];
            }
        else
            {
                return['status'=>'error','message'=>'was not able to add to favorites'];
            }
    }


    if ($type=='removeFavorite')
    {  
        if($sql->query("delete from user_favorites where username ='$username' and recipe_id= '$recipe_id'"))
            {
                return['status'=>'success','message'=>'removed from favorites'];
            }
        else
            {
                 return['status'=>'error','message'=>'was not able to add to favorites'];
            }
    }
    if ($type=='returnFavorites')
    {
        $recipe_id_query= $sql->query("select recipe_id, recipe_name, image, url, ingredients, diet, allergy, cuisineType, totalTime, average_calories
         from user_favorites where username='$username'");
        $favorites=array();
        while($recipe=$recipe_id_query->fetch_assoc())
            {
                $oneRecipe=array();
                $oneRecipe['id']=$recipe['recipe_id'];
                $oneRecipe['name']=$recipe['recipe_name'];
                $oneRecipe['image']=$recipe['image'];
                $oneRecipe['url']=$recipe['url'];
                $oneRecipe['ingredients']=json_decode($recipe['ingredients'],true);
                $oneRecipe['diet']=json_decode($recipe['diet'],true);
                $oneRecipe['allergy']=json_decode($recipe['allergy'],true);
                $oneRecipe['cuisineType']=json_decode($recipe['cuisineType'],true);
                $oneRecipe['totalTime']=$recipe['totalTime'];
                $oneRecipe['average_calories']=$recipe['average_calories'];
                $favorites[]=$oneRecipe;
            }
            $return_array=array();
            $return_array['status']="success";
            $return_array['favorites']=$favorites;
            return $return_array;

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


