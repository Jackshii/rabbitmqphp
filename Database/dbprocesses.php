#!/usr/bin/php
<?php
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');
require_once('../connection.php');

function getuser($sqlconnection, $session) {
    $id = $sqlconnection->query("select id from logininfo where sessionkey = '$session'");
    $userid = $id->fetch_assoc();
    return $userid['id'];
}

function allreq($request) {
    global $sqlconnection;   
    $type = $request['type'];
    $session = $request['session_key'];

    $userId = getUser($sqlconnection, $session);

    if ($type == 'getDiet') {
        $result = $sqlconnection->query("select diet from userdiet where userid = '$userId'");
        
        $getD = array();
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
        $sqlconnection->query("delete from userdiet where userid = '$userId'");

        foreach ($info as $diet) {
            $sqlconnection->query("insert into userdiet (userid, diet) values ('$userId', '$diet')");
        }
        
        $result = $sqlconnection->query("select diet from userdiet where userid = '$userId'");
        
        $setD = array();
        while ($dietinfo = $result->fetch_assoc()) {
            $setD[] = $dietinfo['diet'];
        }

        $dietA = array();
        $dietA['status'] = "success";
        $dietA['dietInfo'] = $setD;
        return $dietA;
    } 
    else if ($type == 'getFridge') {
        $result = $sqlconnection->query("select ingredient from userfridge where userid = '$userId'");
        
        $getF = array();
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
        $sqlconnection->query("delete from userfridge where userid = '$userId'");
        
        foreach ($info as $ingredient) {
            $sqlconnection->query("insert into userfridge (userid, ingredient) values ('$userId', '$ingredient')");            
        }
        $result = $sqlconnection->query("select ingredient from userfridge where userid = '$userId'");
        $updateF = array();
        while ($ingredientinfo = $result->fetch_assoc()) {
            $updateF[] = $ingredientinfo['ingredient'];
        }

        $fridgeA = array();
        $fridgeA['status'] = "success";
        $fridgeA['message'] = "Fridge updated";
        $fridgeA['fridgeInfo'] = $updateF;
        return $fridgeA;
    }
    else if ($type == 'recipeSearch' || $type == 'fridgeSearch' || $type == 'detailedSearch' || $type == 'recommendationsRequest') {
        var_dump($request);
        
        $dietResult = $sqlconnection->query("select diet from userdiet where userid = '$userId'");
        $diets = array();
        while ($dietinfo = $dietResult->fetch_assoc()) {
            $diets[] = $dietinfo['diet'];
        }
        var_dump($diets);

        $finduser = $sqlconnection->query("select username from logininfo where id = '$userId'");
        $getuser = $finduser->fetch_assoc();
        $setuser = $getuser['username'];

        $searchQ = array();
        if ($type == 'recipeSearch') {
            $searchQ = $request['query'];
            var_dump($searchQ);
        }
        else if ($type == 'fridgeSearch') {
            $fridgeResult = $sqlconnection->query("select ingredient from userfridge where userid = '$userId'");
            $fridgeItems = array();
            while ($fridgeinfo = $fridgeResult->fetch_assoc()) {
                $fridgeItems[] = $fridgeinfo['ingredient'];
            }
            var_dump($fridgeItems);
            $searchQ = $fridgeItems;
        }
        else if ($type == 'detailedSearch') {
            $searchQ = $request['id'];
        }
        else if ($type == 'recommendationsRequest') {
            $favResult = $sqlconnection->query("select cuisineType from user_favorites where username = '$setuser'");
            $favCuisine = array();
            while ($favRow = $favResult->fetch_assoc()) {
                $favCuisine[] = $favRow['cuisineType'];
            }
            $searchQ = $favCuisine;
        }
        
        $client = new rabbitMQClient("../apiBroker.ini", "dmzServer");
        $infoA = array();
        
        if ($type == 'detailedSearch') {
            $infoA['type'] = "detailedSearch"; 
            $infoA['id'] = $searchQ;
        }
        else if ($type == 'recommendationsRequest') {
            $infoA['type'] = "recommendations";
            $infoA['cuisineType'] = $searchQ;
            $infoA['diet'] = $diets;
        }
        else {
            $infoA['type'] = "recipeSearch";
            $infoA['ingredients'] = $searchQ;
            $infoA['diet'] = $diets;
        }
        var_dump($infoA);

        $getrecipe = $client->send_request($infoA);
        var_dump($getrecipe);
        
        $recipeA = array();
        $recipeA['status'] = "success";
        
        if ($type == "detailedSearch") {
            $recipeA['recipes'] = $getrecipe;
            
           
            $recipe_id = $request['id'];
            $recipe_name = $getrecipe['name'];
            $image = $getrecipe['image'];
            $url = $getrecipe['url'];
            $ingredients = json_encode($getrecipe['ingredients']);
            $diet = json_encode($getrecipe['diet']);
            $allergy = json_encode($getrecipe['healthLabels']);
            $cuisineType = json_encode($getrecipe['cuisineType']);
            $totalTime = $getrecipe['totalTime'];
            $yield=$getrecipe['yield'];
            $calories=$getrecipe['calories'];
            $average_calories = $getrecipe['average_calories'];
            $sqlconnection->query("insert into temp (username, recipe_id, recipe_name, image, url, ingredients, diet, allergy, cuisineType, totalTime, average_calories, yield, cals) values ('$setuser', '$recipe_id', '$recipe_name', '$image', '$url', '$ingredients', '$diet', '$allergy', '$cuisineType', '$totalTime', '$average_calories', '$yield', '$calories')");
            $checkfav = $sqlconnection->query("select * from user_favorites where username = '$setuser' and recipe_id = '$recipe_id'");
            if ($checkfav->num_rows > 0) {
                $is_fav = 1;
                } 
            else {
                 $is_fav = 0;
                }
                $recipeA['isfavorites'] = $is_fav;
            $result = $sqlconnection->query("select username, recipeID, review, rating from ratereview where recipeID = '$recipe_id'");
        $allreviews = array();
        if ($result->num_rows > 0) {
            while ($numrating = $result->fetch_assoc()) {
                $allreviews[] = $numrating;
            }
        }
        $recipeA['reviews'] = $allreviews;
        return $recipeA;
        }
        else if ($type == "recommendationsRequest") {
            $recipeA["recipes"] = $getrecipe['recipeSearch'];
            var_dump($request);
           return $recipeA;
        }
        else {
            $recipeA["recipes"] = $getrecipe['recipeSearch'];
            return $recipeA;
        }
    }
    // Favorite Personal Deliverable
    else if ($type == 'setFavorite' || $type == 'removeFavorite' || $type == "getFavorites") {
        $user = $sqlconnection->query("select username from logininfo where sessionkey='$session'");
        $userArray = $user->fetch_assoc();
        $username = $userArray['username'];
        $recipe_id = $request['id'];
        
        if ($type == "setFavorite") {
            var_dump($request);
            if ($sqlconnection->query("insert user_favorites (username, recipe_id, recipe_name, image, url, ingredients, diet, allergy, cuisineType, totalTime, average_calories, yield, cals) select '$username', recipe_id, recipe_name, image, url, ingredients, diet, allergy, cuisineType, totalTime, average_calories, yield, cals from temp where recipe_id = '$recipe_id'")) {
            $sqlconnection->query("update temp set isfav = 1 where username = '$username' and recipe_id = '$recipe_id'");    
            return ['status' => 'success', 'message' => 'added to favorites'];
            } else {
                return ['status' => 'error', 'message' => 'was not able to add to favorites'];
            }
        }

        if ($type == 'removeFavorite') {  
            if ($sqlconnection->query("delete from user_favorites where username ='$username' and recipe_id= '$recipe_id'")) {
                $sqlconnection->query("update temp set isfav = 0 where username = '$username' and recipe_id = '$recipe_id'");
                return ['status' => 'success', 'message' => 'removed from favorites'];
            } else {
                return ['status' => 'error', 'message' => 'was not able to remove from favorites'];
            }
        }
        
        if ($type == 'getFavorites') {
            $recipe_id_query = $sqlconnection->query("select recipe_id, recipe_name, image, url, ingredients, diet, allergy, cuisineType, totalTime, average_calories from user_favorites where username='$username'");
            $favorites = array();
            while ($recipe = $recipe_id_query->fetch_assoc()) {
                $oneRecipe = array();
                $oneRecipe['id'] = $recipe['recipe_id'];
                $oneRecipe['name'] = $recipe['recipe_name'];
                $oneRecipe['image'] = $recipe['image'];
                $oneRecipe['totalTime'] = $recipe['totalTime'];
                $oneRecipe['average_calories'] = $recipe['average_calories'];
                $favorites[] = $oneRecipe;
            }
            $return_array = array();
            $return_array['status'] = "success";
            $return_array['favorites'] = $favorites;
            return $return_array;
        }
    }
    else if ($type == 'setReview') {
	 var_dump($request);
        $userQ = $sqlconnection->query("select username from logininfo where id = '$userId'");
        $getU = $userQ->fetch_assoc();
        $setU = $getU['username'];
        $recipeID = $request['id'];
        $review = $request['comment'];
        $rating = $request['rating'];
        $sqlconnection->query("delete from ratereview where username = '$setU' and  recipeID = '$recipeID'"); 
        $sqlconnection->query("insert into ratereview (username, recipeID, review, rating) values ('$setU', '$recipeID','$review','$rating')");
        var_dump($request);
        $ratingA = array();
        $ratingA['status'] = "success";
        $ratingA['message'] = "reviews updated";
        $ratingA["RecipeID"] = $recipeID;
        $ratingA["rating"] = $review;
        $ratingA["comment"] = $review;
        return $ratingA;
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


