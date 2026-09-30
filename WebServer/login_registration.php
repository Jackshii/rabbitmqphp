<?php
$user = $_POST['user_email'];
$pass = $_POST['pass'];
$login_registration = $_POST['login_registration'];
$creds = array();
$creds['login_registration']=$login_registration;
$creds['user']=$user;
$creds['password']=$pass;
require ('../path.inc');
require ('../get_host_info.inc');
require ('../rabbitMQLib.inc');
$phpClient = new rabbitMQClient("../testRabbitMQ.ini","testServer");
$response = $phpClient -> send_request ($creds);
if($response==true)
{
echo "success";
}
else
{
echo "fail";
}
?>