#!/usr/bin/php
<?php
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');
require_once('../connection.php');

function login($user, $pass) {
	global $Host, $User, $Pass, $Name;
	$db = new mysqli($Host, $User, $Pass, $Name);
    $sql = "select id, password from logininfo where username = '$user'";
    $checkuser = $db->query($sql);
    if ($row = $checkuser->fetch_assoc()) {
        $hash = $row['password'];
        if (password_verify($pass, $hash)) {
            $sessionkey = bin2hex(random_bytes(32));
            $update = "update logininfo set sessionkey = '$sessionkey' where id = " . $row['id'];
            $db->query($update);
            return ["status" => "success" , "session_key" => $sessionkey , "username" => $user];
        }
    }
    return ["status"  => "error"] ;
}
function request($req) {
	echo "login request:" . PHP_EOL;
	var_dump($req);
    $user = $req['user'] ;
    $pass = $req['password'];
    echo "doing login process for '$user'" . PHP_EOL;
    return login($user, $pass);
}
$server = new rabbitMQServer("../authBroker.ini", "loginServer");
echo "Login Listener Start" . PHP_EOL;
$server->process_requests('request');
?> 
