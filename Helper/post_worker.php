<?php
require_once(__DIR__."/model/FacebookWorker.php");

$fb_worker = new FacebookWorker();

$fb_worker->run('getPost');