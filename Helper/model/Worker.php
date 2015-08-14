<?php
require_once(__DIR__."/../helper/QueueHelper.php");
require_once(__DIR__."/../helper/FacebookHelper.php");
require_once(__DIR__."/../helper/LogHelper.php");

use PhpAmqpLib\Message\AMQPMessage;

/**
* 
*/
class Worker
{
	private $q;
	private $log;
	public function __construct()
	{
		$this->q = new QueueHelper();
		$this->log = new LogHelper();
	}

	public function run()
	{
		$this->q->listen($this, 'processMessage');
	}

	public function processMessage(AMQPMessage $msg)
	{
		  echo " [x] Received ", $msg->body, "\n";
		  sleep(substr_count($msg->body, '.'));
		  echo " [x] Done", "\n";
		  $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
	}
}