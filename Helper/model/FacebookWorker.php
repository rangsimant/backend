<?php
require_once(__DIR__."/../helper/QueueHelper.php");
require_once(__DIR__."/../helper/FacebookHelper.php");
require_once(__DIR__."/Post.php");

use PhpAmqpLib\Message\AMQPMessage;

/**
* 
*/
class FacebookWorker
{
	private $q;
	private $post;
	public function __construct()
	{
		$this->q = new QueueHelper();
		$this->post = new Post();
	}

	public function run($method)
	{
		$this->q->listen($this, $method);
	}

	public function getPost(AMQPMessage $msg)
	{
		$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
		$data = json_decode($msg->body, true);
		switch ($data['channel']) 
		{
			case 'facebook':
				$this->fromFacebook($data);
				break;
			case 'instagram':
				# code...
				break;
			case 'twitter':
				# code...
				break;
			
		}
	}
	public function fromFacebook($data)
	{
		$access_token = $data['cp_access_token'];
		$fb_helper = new FacebookHelper($access_token);
		$posts = $fb_helper->getPosts($data['lasted_fetch']);
		$this->post->insertNewPost($posts, $data);
			
	}
}