#!/usr/bin/php
<?php
function recipeSearch($info)
{
    var_dump($info);
    $api_info = __DIR__ . '/api.ini';
    $api_creds= parse_ini_file($api_info);
    $api_id=$api_creds['api_ID'];
    $api_key=$api_creds['api_KEY'];
    $returnRecipesMatch= array();
    $condenseRecipes=array();
    $recipesMatch=array();
    if (is_array($info['ingredients']))
    {
    $recieve_ingred=$info['ingredients'];
    shuffle($recieve_ingred);
    $condenseIngreds=array_slice($recieve_ingred,0,5);
    $ingred=implode(',',$condenseIngreds);
    }
    else
    {
    $ingred=$info['ingredients'];
    }
    $diet= $info['diet'];
    


    if(!empty($info['id']))
    {
        $APICall="https://api.edamam.com/api/recipes/v2/".urldecode($info['id'])."?type=public&app_id=". $api_id . "&app_key=". $api_key;
    }

    else
    {
        $APICall="https://api.edamam.com/api/recipes/v2?type=public&q=" . urlencode($ingred) ."&app_id=". $api_id . "&app_key=". $api_key;
    
  
        if(!empty($diet))
            {
                foreach($diet as $dietType)
                {
                    $APICall= $APICall . "&diet=" . urlencode($dietType);
                }
            
            }
    }
    $recipesReturn= file_get_contents($APICall);
    $recipesPHP=json_decode($recipesReturn,true);
    if(isset($recipesPHP['_links']))
        {
            unset($recipesPHP['_links']); //remove api key from going out hopefully 
        }
    //return 1 recipe if recipe id is present hopefully :3
    if(!empty($info['id']))
        {
            $recipeInfo=$recipesPHP['recipe'];
            $condenseRecipes= array
            (
            'id' => str_replace('http://www.edamam.com/ontologies/edamam.owl#','',$recipeInfo['uri']),
            'name' => $recipeInfo['label'],
            'image' => $recipeInfo['image'],
            'url' => $recipeInfo['url'],
            'ingredients' =>$recipeInfo['ingredientLines'],
            'totalTime' => $recipeInfo['totalTime'],
            'average_calories'=> round($recipeInfo['calories']/$recipeInfo['yield']),
            );
            return $condenseRecipes;
        }

    $recipesMatch=$recipesPHP['hits'];

    //sort via type

    if($info['type']=="recipeSearch")
    {
        if(is_array($recipesMatch))
        {
        foreach(array_slice($recipesMatch,0,10) as $match)
        {
        $recipeInfo=$match['recipe'];
            
        $condenseRecipes[]=array
        (
            'id' => str_replace('http://www.edamam.com/ontologies/edamam.owl#','',$recipeInfo['uri']),
            'name' => $recipeInfo['label'],
            'image' => $recipeInfo['image'],
            'totalTime' => $recipeInfo['totalTime'],
            'average_calories'=> ($recipeInfo['calories']/$recipeInfo['yield']),
        );
        
        }
        }
    }


    else if($info['type']=="detailedSearch")
    {
        if(is_array($recipesMatch))
        {
        foreach(array_slice($recipesMatch,0,10) as $match)
        {
        $recipeInfo=$match['recipe'];

        $condenseRecipes[]=array
        (
            'id' => str_replace('http://www.edamam.com/ontologies/edamam.owl#','',$recipeInfo['uri']),
            'name' => $recipeInfo['label'],
            'image' => $recipeInfo['image'],
            'url' => $recipeInfo['url'],
            'ingredients' =>$recipeInfo['ingredientLines'],
            'diet'=>$recipeInfo['dietLabels'],
            'allergy'=>$recipeInfo['healthLabels'],
            'totalTime' => $recipeInfo['totalTime'],
            'average_calories'=> round($recipeInfo['calories']/$recipeInfo['yield']),
        );
        
        }
        }
    }



    $returnRecipesMatch['recipeSearch']=$condenseRecipes;
    
    return $returnRecipesMatch;
}
require('../path.inc');
require('../get_host_info.inc');
require('../rabbitMQLib.inc');
$server= new rabbitMQServer("../apiBroker.ini","dmzServer");
$server->process_requests('recipeSearch');
?>