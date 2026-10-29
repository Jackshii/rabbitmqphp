#!/usr/bin/php
<?php
require('../path.inc');
require('../get_host_info.inc');
require('../rabbitMQLib.inc');
$testClient=new rabbitMQClient("../apiBroker.ini","dmzServer");
$info_array=array();
$info_array['type']="detailSearch";
$info_array['ingredients']="garlic chicken";
$info_array['diet']=array("high-protein","low-carb");
echo "sending to rabbit queue";
$return_array=$testClient->send_request($info_array);
echo "response recieve";
print_r($return_array);
if(!empty($return_array['recipeSearch']))
{
$recipe_info=$return_array['recipeSearch'][0]; //index the return array so you can pick the recipe based on index. (prob need to create a for loop for a certain amount of time or base on number of matches)
$recipeArray=array();
$recipeArray['recipe_name']=$recipe_info['name'];// just gives you the title of recipe.
$recipeArray['recipe_url'] = $recipe_info['url'];// give you the uri of instrction of recipe.
$recipeArray['recipe_image'] = $recipe_info['image'];//give website link to image.
$recipeArray['recipe_dietLabels']=implode(",",$recipe_info['diet']);///break the array and make it into a comma list.
$recipeArray['recipe_healthLabels']=implode(",",$recipe_info['allergy']); //same as the one above but just for allergies.
echo $recipeArray['recipe_name']. "\n";
echo $recipeArray['recipe_url']. "\n";
echo $recipeArray['recipe_image']. "\n";
echo $recipeArray['recipe_dietLabels']. "\n";
echo $recipeArray['recipe_healthLabels']. "\n";
}
else
{
echo "no recipes";
}

?>