@ECHO OFF
title [POSTCenter] Comment Publisher
:loop

php comment_publish.php
timeout /t 180 /nobreak

goto loop

// time
// 1 = 1 seconde.
// 30 = 30 seconde.
// 60 = 1 minute.
// 600 = 10 minute.