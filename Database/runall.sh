#!/bin/bash
cd /home/eric/git/rabbitmqphp/Database
php dbprocesses.php & php validatelogin.php & php validatereg.php & /git/rabbitmqphp/Database wait
