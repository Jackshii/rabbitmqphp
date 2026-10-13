#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('connection.php');
function register($username, $password) {
        global $Host, $User, $Pass, $Name;
        $db = new mysqli($Host, $User, $Pass, $Name);

    $check="select * from logininfo where username = '$username'";
    $checkdup=mysqli_query($db, $check);
    if ($checkdup && $checkdup->num_rows > 0) {
        return ["status" => "duplicate"];
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "insert into logininfo (username, password) values ('$username', '$hash')" ;
    if ($db->query($sql)) {
        return ["status" => "success"];
    } else {
        return ["status" => "error"] ;
    }
}
function request($request) {
	echo "Received registration request:" . PHP_EOL;
	var_dump($request);
    $username = $request['user'] ;
    $password = $request['password'];
    echo "doing registration process for '$username'" . PHP_EOL;
    return register($username, $password);
}
$server = new rabbitMQServer("authBroker.ini", "registrationServer");
echo "Registration Listener start" . PHP_EOL;
$server->process_requests('request');
?> 
