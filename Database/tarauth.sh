#!/bin/bash
[ -f authvernum.txt ] && v=$(cat authvernum.txt) || v=1
name="auth_v${v}.tar.gz"

tar -czf "$name" validatelogin.php validatereg.php
echo $((v + 1)) > authvernum.txt

