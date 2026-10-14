#!/usr/bin/php
<?php
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');
require_once('../connection.php');
function login($username, $password) {
	global $Host, $User, $Pass, $Name;
	$db = new mysqli($Host, $User, $Pass, $Name);
    $sql = "select id, password from logininfo where username = '$username'";
    $result = $db->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        $hash = $row['password'];
        if (password_verify($password, $hash)) {
            $sessionkey = bin2hex(random_bytes(32));
            $update = "update logininfo set session_key = '$sessionkey' where id = " . $row['id'];
            $db->query($update);
            return ["status" => "success" , "session_key" => $sessionkey , "username" => $username];
        }
    }
    return ["status"  => "error"] ;
}
function request($request) {
    echo "login request:" . PHP_EOL;
    $username = $request['user'] ;
    $password = $request['password'];
    echo "doing login process for '$username'" . PHP_EOL;
    return login($username, $password);
}
$server = new rabbitMQServer("authBroker.ini", "loginServer");
echo "Login Listener Start" . PHP_EOL;
$server->process_requests('request');
?> 

