@ECHO OFF
title [POSTCenter] facebook token
:loop

php facebook_publish.php
timeout /t 1800 /nobreak > NUL

goto loop

// time
// 1 = 1 seconde.
// 30 = 30 seconde.
// 60 = 1 minute.
// 600 = 10 minute.