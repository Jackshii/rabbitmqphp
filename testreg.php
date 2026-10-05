#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
function register($username, $password) {
    $db = new mysqli("localhost", "dbuser", "dbpass", "users");
    $check="SELECT * FROM logininfo WHERE username = '$username'";
    $checkdup=mysqli_query($db, $check);
    if ($checkdup && $checkdup->num_rows > 0) {
        return ["status" => "error", "message" => "Username already exists"];
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO logininfo (username, password) VALUES ('$username', '$hash')" ;
    if ($db->query($sql)) {
        return ["status" => "it works", "message" => "User registered"];
    } else {
        return ["status" => "error", "message" => "Registration failed"] ;
    }
}
function request($request) {
    echo "Received registration request:" . PHP_EOL;
    $username = $request['user'] ;
    $password = $request['password'];
    echo "doing registration process for '$username'" . PHP_EOL;
    return register($username, $password);
}
$server = new rabbitMQServer("authBroker.ini", "registrationServer");
echo "Registration Listener start" . PHP_EOL;
$server->process_requests('request');
?> 
