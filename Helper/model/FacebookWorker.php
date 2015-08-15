<?php
require_once(__DIR__."/../helper/QueueHelper.php");
require_once(__DIR__."/../helper/FacebookHelper.php");

use PhpAmqpLib\Message\AMQPMessage;

/**
* 
*/
class FacebookWorker
{
	private $q;
	public function __construct()
	{
		$this->q = new QueueHelper();
		$this->config = parse_ini_file(__DIR__."/../config/social_conf.ini", true);
	}

	public function run($method)
	{
		$this->q->listen($this, $method);
	}

	public function getFacebookPost(AMQPMessage $msg)
	{
		$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
		$data = json_decode($msg->body, true);
		$field = $this->config['facebook']['post_field'];
		$limit = $this->config['facebook']['post_limit'];

		$access_token = $data['cp_access_token'];
		$fb_helper = new FacebookHelper($access_token);
		$posts = $fb_helper->getPosts($data['lasted_fetch']);
		print_r($posts);
			
	}
}