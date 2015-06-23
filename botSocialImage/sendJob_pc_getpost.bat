@ECHO OFF
title [POSTcenter] sendJob
:loop

php sendJob_pc_getpost.php
SLEEP 1

goto loop

// time
// 1 = 1 seconde.
// 30 = 30 seconde.
// 60 = 1 minute.
// 600 = 10 minute.