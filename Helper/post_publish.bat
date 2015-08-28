@ECHO OFF
title [POSTCenter] Post Publisher
:loop

php post_publish.php
timeout /t 30 /nobreak

goto loop

// time
// 1 = 1 seconde.
// 30 = 30 seconde.
// 60 = 1 minute.
// 600 = 10 minute.