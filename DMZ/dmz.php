#!/usr/bin/php
<?php
$api_info = __DIR__ . '/api.ini';
$api_creds= parse_ini_file($api_info);
$api_id=$api_creds['api_ID'];
$api_key=$api_creds['api_KEY'];
?>