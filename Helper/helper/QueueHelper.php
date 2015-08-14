
<?php

require_once(__DIR__.'/../vendor/autoload.php');
require_once(__DIR__.'/LogHelper.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
/**
* 
*/

class QueueHelper
{
	private $config;
	public function __construct($connection = array())
	{
		date_default_timezone_set("Asia/Bangkok");
		$this->config = parse_ini_file(__DIR__.'\..\config\queue_conf.ini',true);

 		$host = $this->config['publish']['host'];
		$port = $this->config['publish']['port'];
		$user = $this->config['publish']['user'];
		$pass = $this->config['publish']['pass'];
		$this->q_name = $this->config['publish']['q_name'];

		if (!empty($connection) && is_array($connection)) 
		{
			$host = $connection['host'];
			$port = $connection['port'];
			$user = $connection['user'];
			$pass = $connection['pass'];
			$this->q_name = $connection['q_name'];
		}
		
		$this->connect = self::connection($host, $port, $user, $pass);
	}

	private static function connection($host, $port, $user, $pass)
	{
		try 
		{
			$connection = new AMQPStreamConnection($host, $port, $user, $pass);
			LogHelper::printScreen("[QUEUE SERVER] : ".$host.":".$port."\n");
			return $connection;
		} catch (Exception $e) 
		{
			LogHelper::printScreen($e->getMessage());
		}
	}

	public function publish($data = null, $print_msg = null)
	{
		$channel = $this->connect->channel();

		$channel->queue_declare($this->q_name, false, true, false, false);

		$msg = new AMQPMessage($data,
		                        array('delivery_mode' => 2) # make message persistent
		                      );

		if (is_null($data)) 
		{
			LogHelper::printScreen("Not Send!");		
		}
		else
		{
			try 
			{
				$result = $channel->basic_publish($msg, '', $this->q_name);
				LogHelper::printScreen("Send : ".$print_msg);
			} catch (Exception $e) 
			{
				LogHelper::printScreen($e->getMessage());
			}
		}


		$channel->close();
	}

	public function listen($class = null, $method = '', $prefetch_count = 1)
	{
		$channel = $this->connect->channel();
		$channel->queue_declare($this->q_name, false, true, false, false);

		echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

		$channel->basic_qos(null, $prefetch_count, null);
		$channel->basic_consume(
			$this->q_name, #queue
			'', 		#consumer tag - Identifier for the consumer, valid within the current channel. just string
  			false,		#no local - TRUE: the server will not send messages to the connection that published them
            false,		#no ack, false - acks turned on, true - off.  send a proper acknowledgment from the worker, once we're done with a task
            false,		#exclusive - queues may only be accessed by the current connection
            false,		#no wait - TRUE: the server will not respond to the method. The client should not wait for a reply method
			array($class, $method));       #callback

		while(count($channel->callbacks)) {
		    $channel->wait();
		}

		$channel->close();
	}

	public function closeConnection()
	{
		$this->connect->close();
	}

	public function processMessage(AMQPMessage $msg)
	{
		  echo " [x] Received ", $msg->body, "\n";
		  sleep(substr_count($msg->body, '.'));
		  echo " [x] Done", "\n";
		  $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
	}
}