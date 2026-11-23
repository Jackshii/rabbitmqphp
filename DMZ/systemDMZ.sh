#!/bin/bash
cd /home/vboxuser/git/rabbitmqphp/DMZ
php dmz.php &
cd /home/vboxuser/git/rabbitmqphp && php dmzErrorLogListener.php
