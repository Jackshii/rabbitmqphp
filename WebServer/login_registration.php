<?php
$user = $_POST['user_email'];
$pass = $_POST['pass'];
$login_registration = $_POST[$login_registration];
$creds = array();
$creds['login_registration']=$login_registration;
$creds['user']=$user;
$creds['password']=$pass;
?>