<?php

require_once(__DIR__.'../models/Queue.php');
require_once(__DIR__.'../models/Database.php');
$q = new Queue();
$db = new Database();
$config = parse_ini_file(__DIR__.'/config/config.ini',true);


while (true) 
{
	$account = $db->getAccount($config['get_account']['since'], $config['get_account']['limit']);
	$q_name = $config['queue']['q_name'];
	$q->sendToQueue($account, $q_name);
	usleep($config['queue']['wait']);
}



