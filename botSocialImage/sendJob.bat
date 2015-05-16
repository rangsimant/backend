@ECHO OFF
title [Social Image] sendJob
:loop

php sendJob.php
SLEEP 30

goto loop

// time
// 1 = 1 seconde.
// 30 = 30 seconde.
// 60 = 1 minute.
// 600 = 10 minute.