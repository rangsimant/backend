@ECHO OFF
title [POSTCenter] facebook token
:loop

php facebook_publish.php
SLEEP 10

goto loop

// time
// 1 = 1 seconde.
// 30 = 30 seconde.
// 60 = 1 minute.
// 600 = 10 minute.