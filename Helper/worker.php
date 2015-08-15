<?php
require_once(__DIR__."/model/Worker.php");

$worker = new Worker();

$worker->run('getFacebookPost');