#!/usr/bin/php
<?php
function recipeSearch($info)
{
    $api_info = __DIR__ . '/api.ini';
    $api_creds= parse_ini_file($api_info);
    $api_id=$api_creds['api_ID'];
    $api_key=$api_creds['api_KEY'];
    $returnRecipesMatch= array();
    $ingred=$info['ingredients'];
    $diet= $info['diet'];
    $APICall="https://api.edamam.com/api/recipes/v2?type=public&q=" . urlencode($ingred) ."&app_id=". $api_id . "&app_key=". $api_key;
    if(!empty($diet))
        {
            $APICall= $APICall . "&diet=" . $diet;
        }
    $recipesReturn= file_get_contents($APICall);
    $recipesPHP=json_decode($recipesReturn,true);
    $recipesMatch=$recipesPHP['hits'];
    $returnRecipesMatch['recipeSearch']=$recipesMatch;
    return $returnRecipesMatch;
}
require('../path.inc');
require('../get_host_info.inc');
require('../rabbitMQLib.inc');
$server= new rabbitMQServer("../apiBroker.ini","dmzServer");
$server->process_requests('recipeSearch');
?>