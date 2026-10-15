#!/usr/bin/php
<?php
require('../path.inc');
require('../get_host_info.inc');
require('../rabbitMQLib.inc');
$testClient=new rabbitMQClient("../apiBroker.ini","dmzServer");
$info_array=array();
$info_array['type']="recipeSearch";
$info_array['ingredients']="garlic chicken";
$info_array['diet']="balanced";
echo "sending to rabbit queue";
$return_array=$testClient->send_request($info_array);
echo "response recieve";
print_r($return_array);

$first_match=$return_array['recipeSearch'][0];
$recipe_info=$first_match['recipe'];
$recipeArray=array();
$recipeArray['recipe_name']=$recipe_info['label'];
$recipeArray['recipe_uri'] = $recipe_info['uri'];
$recipeArray['recipe_image'] = $recipe_info['image'];
$recipeArray['recipe_dietLabels']=$recipe_info['dietLabels'];
$recipeArray['recipe_healthLabels']=$recipe_info['healthLabels'];
echo $recipeArray['recipe_name'];
?>