<?php

require_once(__DIR__.'/../vendor/autoload.php');
/**
* 
*/
class FacebookHelper
{
	private $fb;
	public function __construct($access_token = '')
	{
		$fb_config = parse_ini_file(__DIR__."/../config/social_conf.ini", true);

		$this->fb = new Facebook\Facebook([
				  'app_id' => $fb_config['facebook']['app_id'],
				  'app_secret' => $fb_config['facebook']['app_secret'],
				  'default_graph_version' => $fb_config['facebook']['api_version'],
				  ]);
		$this->fb->setDefaultAccessToken($access_token);
	}

	public function request($method = "GET", $field = '/me')
	{
		$request = $this->fb->request($method, $field);

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
}