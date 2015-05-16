<?php

require_once(__DIR__.'../models/Queue.php');
require_once(__DIR__.'../models/Worker.php');

$q = new Queue();
$config = parse_ini_file('\config\config.ini',true);

$q_name = $config['queue']['q_name'];
$connection = $q->connection();
$channel = $connection->channel();

$channel->queue_declare($q_name, false, true, false, false);

$callback = function($msg) {

	$worker = new Worker();
	$worker->processMessages($msg->body);
	$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume($q_name, '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();