@ECHO OFF
title [Social Image] sendJob
:loop

php sendJob.php
SLEEP 600

goto loop