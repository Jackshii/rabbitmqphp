#!/bin/bash
[ -f processesvernum.txt ] && v=$(cat processesvernum.txt) || v=1
name="processes_v${v}.tar.gz"

tar -czf "$name" dbprocesses.php
echo $((v + 1)) > processesvernum.txt

