<?php
require_once(__DIR__."/model/FacebookWorker.php");

$fb_worker = new FacebookWorker();

$q_connection = array(
		"host" => "127.0.0.1",
		"port" => "5672",
		"user" => "postcenter",
		"pass" => "postcenter!",
		"q_name" => "Facebook_Comment_Only"
	);

$fb_worker->run('getPostAndComment', $q_connection);