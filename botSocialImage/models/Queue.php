<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Queue
{
	private $config;

	public function __construct()
	{
		date_default_timezone_set("Asia/Bangkok");
		$this->config = parse_ini_file('\config\config.ini',true);
	}

	public function connectQueue()
	{
		$host = $this->config['queue']['host'];
		$user = $this->config['queue']['user'];
		$pass = $this->config['queue']['pass'];

		return $connection = new AMQPConnection($host, 5672, $user, $pass);
	}

	public function sendToQueue($data=null)
	{
		$count = count($data);
		if (is_array($data) && !empty($data)) 
		{
			$data = json_encode($data);
		}

		$q_name = $this->config['queue']['q_name'];
		$connection = $this->connectQueue();
		$channel = $connection->channel();
		$channel->queue_declare($q_name, false, true, false, false);

		if(!empty($data))
		{
			$msg = new AMQPMessage($data, array('delivery_mode' => 2)); # make message persistent 

			$channel->basic_publish($msg, '', $q_name);
			echo "[".date('Y-m-d H:i:s')."] [x] Sent Total = ".$count." Account. \n";
		}
		else
		{
			echo "[".date('Y-m-d H:i:s')."] [!] No Sent Total = ".$count." Account. \n";
		}

		$channel->close();
		$connection->close();
	}

	public function receiveFromQueue()
	{
		$q_name = $this->config['queue']['q_name'];
		$connection = $this->connectQueue();
		$channel = $connection->channel();

		$channel->queue_declare($q_name, false, true, false, false);

		$callback = function($msg) {
			# Write to Database
		 	echo " [x] Received ", $msg->body, "\n";
		};

		$channel->basic_consume($q_name, '', false, true, false, false, $callback);

		while(count($channel->callbacks)) {
		    $channel->wait();
		}
	}
}