<?php
require_once(__DIR__."/../helper/QueueHelper.php");
require_once(__DIR__."/../helper/FacebookHelper.php");

use PhpAmqpLib\Message\AMQPMessage;

/**
* 
*/
class Worker
{
	private $q;
	public function __construct()
	{
		$this->q = new QueueHelper();
	}

	public function run($method)
	{
		$this->q->listen($this, $method);
	}

	public function getFacebookPost(AMQPMessage $msg)
	{
		$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
		$data = json_decode($msg->body, true);

		$access_token = $data['cp_access_token'];
		$fb_helper = new FacebookHelper($access_token);
		$responses = $fb_helper->request('get', '/me/posts');
		$posts = $fb_helper->processResponseBatchRequest($responses);
		print_r($posts);
			
	}
}