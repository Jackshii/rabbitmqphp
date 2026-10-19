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
    if (is_array($info['ingredients']))
    {
    $ingred=implode(' ',$info['ingredients']);
    }
    else
    {
    $ingred=$info['ingredients'];
    }
    $diet= $info['diet'];
    $APICall="https://api.edamam.com/api/recipes/v2?type=public&q=" . urlencode($ingred) ."&app_id=". $api_id . "&app_key=". $api_key;
    if(!empty($diet))
        {
            foreach($diet as $dietType)
            {
                $APICall= $APICall . "&diet=" . urlencode($dietType);
            }
            
        }
    $recipesReturn= file_get_contents($APICall);
    $recipesPHP=json_decode($recipesReturn,true);
    $recipesMatch=$recipesPHP['hits'];
    if(is_array($recipeMatch))
    {
    foreach(array_slice($recipeMatch,0,10) as $recipeMatch)
    {
        $recipeInfo=$recipeMatch['recipe'];

        $condenseRecipes[]=array
        (
            'name' => $recipeInfo['label'],
            'image' => $recipeInfo['image'],
            'url' => $recipeInfo['url'],
            'ingredients' =>$recipeInfo['ingredientLines'],
            'diet'=>$recipeInfo['dietLabels'],
            'allergy'=>$recipeInfo['healthLabels'],
        );
        
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