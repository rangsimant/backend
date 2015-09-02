<?php

require_once(__DIR__.'/../vendor/autoload.php');
/**
* 
*/
class FacebookHelper
{
	private $fb;
	private $session;
	private $app;
	private $fb_config;
	public function __construct($access_token = '')
	{
		$this->fb_config = parse_ini_file(__DIR__."/../config/social_conf.ini", true);

		$this->fb = new Facebook\Facebook([
				  'app_id' => $this->fb_config['facebook']['app_id'],
				  'app_secret' => $this->fb_config['facebook']['app_secret'],
				  'default_graph_version' => $this->fb_config['facebook']['api_version'],
				  ]);

		$this->fb->setDefaultAccessToken($access_token);
	}

	public function getPosts($lasted_fetch)
	{

		try {
		  // Requires the "read_stream" permission
		  $response = $this->fb->get($this->fb_config['facebook']['post_field']);
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
		  // When Graph returns an error
		  echo 'Graph returned an error: ' . $e->getMessage();
		  exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		  // When validation fails or other local issues
		  echo 'Facebook SDK returned an error: ' . $e->getMessage();
		  exit;
		}

		// Page 1
		$feedEdge = $response->getGraphEdge();

		$posts = $this->getPostsAndNextPage($feedEdge, $lasted_fetch);
		return $posts;
	}
	
	public function getCommentOrReply($data)
	{
		$post_id = $data['post_social_id'];
		try {
		  // Requires the "read_stream" permission
		  $response = $this->fb->get("/".$post_id.$this->fb_config['facebook']['comment_field']);
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
		  // When Graph returns an error
			if ($e->getCode() == 100) 
			{
				return "\tThis post Deleted.\n";
			}
		  echo 'Graph returned an error: ' . $e->getMessage();
		  exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		  // When validation fails or other local issues
		  echo 'Facebook SDK returned an error: ' . $e->getMessage();
		  exit;
		}

		// Page 1
		$feedEdge = $response->getGraphEdge();
		$posts = $this->getPostsAndNextPage($feedEdge, $lasted_fetch = "0000-00-00 00:00:00");
		return $posts;
	}


	public function request($method = "GET", $field = '/me', $limit = 25)
	{
		$request = $this->fb->request($method, $field."&limit=".$limit);

		$batch = [
	    'request-me' => $request,
	    ];

		try {
		  $responses = $this->fb->sendBatchRequest($batch);
		  return $responses;
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
		  // When Graph returns an error
		  echo 'Graph returned an error: ' . $e->getMessage();
		  exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		  // When validation fails or other local issues
		  echo 'Facebook SDK returned an error: ' . $e->getMessage();
		  exit;
		}
	}

	public function processResponseBatchRequest($responses)
	{
		foreach ($responses as $key => $response) 
		{
		  if ($response->isError()) 
		  {
		    $e = $response->getThrownException();
		    echo '<p>Error! Facebook SDK Said: ' . $e->getMessage() . "\n\n";
		    echo '<p>Graph Said: ' . "\n\n";
		    var_dump($e->getResponse());
		    exit();
		  } 
		  else 
		  {
		  	$me = $this->responseAsArray($response);
		    return $me;
		  }
		}
	}

	private function responseAsArray($response)
	{
		return json_decode($response->getBody(), $isArray = true);
	}

	private function getPostsAndNextPage($object, $lasted_fetch)
	{
		$posts = array();
		$lasted_fetch = strtotime($lasted_fetch);
		do 
		{
			foreach ($object as $status) 
			{
				$post = $status->asArray();
				$created_time = $post['created_time'];
				$created_time->setTimezone(new DateTimeZone('Asia/Bangkok'));
				$created_time = $created_time->format('Y-m-d H:i:s');
				$created_time = strtotime($created_time);
				if ($created_time > $lasted_fetch) 
				{
					$posts[] = $post;
				}
				else
				{
					break;
				}
			}
		} while ($object = $this->fb->next($object));
		
		return $posts;
	}
}