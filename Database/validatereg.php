#!/usr/bin/php
<?php
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');
require_once('../connection.php');
function register($user, $pass) {
        global $sqlconnection; ;

    $check="select * from logininfo where username = '$user'";
    $checkdup=mysqli_query($sqlconnection, $check);
    if ($checkdup->num_rows > 0) {
        return ["status" => "duplicate"];
    }
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $sql = "insert into logininfo (username, password) values ('$user', '$hash')" ;
    if ($sqlconnection->query($sql)) {
        return ["status" => "success"];
    } else {
        return ["status" => "error"] ;
    }
}
function request($req) {
	echo "Received registration request:" . PHP_EOL;
	var_dump($req);
    $user = $req['user'] ;
    $pass = $req['password'];
    echo "doing registration process for '$user'" . PHP_EOL;
    return register($user, $pass);
}
$server = new rabbitMQServer("../authBroker.ini", "registrationServer");
echo "Registration Listener start" . PHP_EOL;
$server->process_requests('request');
?> 

