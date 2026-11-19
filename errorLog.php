<?php
    function frontendLogError($level, $page, $error) {
        date_default_timezone_set('America/New_York');
        $user = $_SESSION['username'] ?? 'user-N/A';
        $log = date('Y-m-d H:i:s') . " " . gethostname() . " " . $level . " " . $page . " (" . $user . "): " . $error;

        $client = new rabbitMQClient("logBroker.ini", "Web Log Listener");
        $client->publish(array('log' => $log));
    }

    function dbLogError($level, $page, $error) {
        date_default_timezone_set('America/New_York');
        $user = $_SESSION['username'] ?? 'user-N/A';
        $log = date('Y-m-d H:i:s') . " " . gethostname() . " " . $level . " " . $page . " (" . $user . "): " . $error;

        $client = new rabbitMQClient("logBroker.ini", "Backend Listener");
        $client->publish(array('log' => $log));
    }

    function dmzLogError($level, $page, $error) {
        date_default_timezone_set('America/New_York');
        $user = $_SESSION['username'] ?? 'user-N/A';
        $log = date('Y-m-d H:i:s') . " " . gethostname() . " " . $level . " " . $page . " (" . $user . "): " . $error;

        $client = new rabbitMQClient("logBroker.ini", "DMZ Listener");
        $client->publish(array('log' => $log));
    }
?>