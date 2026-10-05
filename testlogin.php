#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
function login($username, $password) {
    $db = new mysqli("localhost", "dbuser", "dbpass", "users");
    $sql = "SELECT id, password FROM logininfo WHERE username = '$username'";
    $result = $db->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        $hash = $row['password'];
        if (password_verify($password, $hash)) {
            $sessionkey = bin2hex(random_bytes(32));
            $update = "UPDATE logininfo SET session_key = '$sessionkey' WHERE id = " . $row['id'];
            $db->query($update);
            return ["status" => "it works " , "session_key" => $sessionkey];
        }
    }
    return ["status"  => "error " , "message"  => "Wrong login info"] ;
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

