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
?>