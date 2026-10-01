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
if ($login_registration ==='registration')
{
    $login_registration_server="registrationServer";
}
else
{
    $login_registration_server="loginServer";
}
$phpClient = new rabbitMQClient("../authBroker.ini",$login_registration_server);
$response = $phpClient -> send_request ($creds);
if($response==true)
{
echo "success";
print($response);
}
else
{
echo "fail";
print($response);
}
?>