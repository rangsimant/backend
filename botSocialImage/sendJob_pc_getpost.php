<?php

require_once(__DIR__.'../models/Queue.php');
require_once(__DIR__.'../models/PostCenter.php');
$q = new Queue();
$PostCenter = new PostCenter();
$config = parse_ini_file('\config\postcenter.ini',true);


$data = $PostCenter->getSocialID($config['get_account']['since'], $config['get_account']['limit']);

$q_name = $config['queue']['q_name'];
$q->sendToQueue($data, $q_name);