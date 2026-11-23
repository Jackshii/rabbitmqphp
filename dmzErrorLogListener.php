<?php
    require_once('rabbitMQLib.inc');

    function appendLog($message) {
        file_put_contents('/var/log/IT490/sharedLogs.log', $message['log'] . "\n", FILE_APPEND | LOCK_EX);
    }

    $server = new rabbitMQServer("logBroker.ini", "DMZ Listener");
    $server->process_requests('appendLog');
?>