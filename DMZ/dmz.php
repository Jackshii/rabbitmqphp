#!/usr/bin/php
<?php
$api_info = __DIR__ . '/api.ini';
$api_creds= parse_ini_file($api_info);
$api_id=$api_creds['api_ID'];
$api_key=$api_creds['api_KEY'];
function recipeSearch($info)
{
    $ingred=$info['ingredients'];
    $APICall="https://api.edamam.com/api/recipes/v2?type=public&q=" $ingred ."&app_id=". $api_id . "&app_key=". $api_key;
    $recipesReturn= file_get_contents($APICall);
    $recipesPHP=json_decode($recipeReturn,true);
    $recipesMatch=$recipesPHP['hits']
    return array ('recipes'=> $recipesMatch)
}
require('../path.inc');
require('../get_host_info.inc');
require('../rabbitMQLib.inc');
$server= new rabbitMQsServer()
$server->process_requests('recipeSearch')
?>