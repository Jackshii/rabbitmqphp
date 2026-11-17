<?php
//it gets run by the cronjob every hour 
require_once('/home/eric/git/rabbitmqphp/connection.php');
$sqlconnection->query("TRUNCATE TABLE temp");
?>
