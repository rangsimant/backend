<?php

require_once(__DIR__.'../models/Queue.php');
require_once(__DIR__.'../models/Database.php');
$q = new Queue();
$db = new Database();
$config = parse_ini_file('\config\config.ini',true);


$account = $db->getAccount($config['get_account']['since'], $config['get_account']['limit']);
$q_name = $config['queue']['q_name'];
$q->sendToQueue($account, $q_name);


