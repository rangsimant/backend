<?php
ini_set('max_execution_time', 0); // limit seconds time for load
define('FACEBOOK', '..\libs\facebook_SDK\src\Facebook');
require(__DIR__.'\..\libs\facebook_SDK\autoload.php');

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
// use Facebook\FacebookRequestException;

class Facebook
{
	private $config;
	public function __construct()
	{
		date_default_timezone_set("Asia/Bangkok");
		$this->config = parse_ini_file('\config\config.ini',true);
	}

	private function setAppSession()
	{
		$app_id = $this->config['app']['app_id'];
		$app_secret = $this->config['app']['app_secret'];
		$session = null;

		if ($app_id != null && $app_secret != null) 
		{
			FacebookSession::setDefaultApplication($app_id, $app_secret);
			$session = FacebookSession::newAppSession();
		}
		
		return $session;
	}

	public function getFeedsFromPage($page_id)
	{
		if ($page_id != null)
		{
			$session = $this->setAppSession();
			if (!empty($session)) 
			{
				$query = "posts?fields=id,message,created_time,updated_time,attachments{media},link";
				$limit = "&limit=".$this->config['app']['limit'];
				$request = new FacebookRequest($session, 'GET', '/'.$page_id.'/'.$query.$limit);
				do 
				{
			        $response = $request->execute();
			        $object = $response->getGraphObject();
			        if ($object->getProperty('data') !== null) 
			        {
			        	$data = $object->getProperty('data')->asArray();
				       	foreach ($data as $key => $value) 
				       	{
							if (isset($value->message)) 
							{
								echo $key." ".$value->message."<br>";
								if (isset($value->attachments)) 
								{
									echo "<img src=".$value->attachments->data[0]->media->image->src."><br>";
								}
							}
						}
			        }
			       
		    	} 
		    	while ($request = $response->getRequestForNextPage());
		    }
	    }
	    else
	    {
	    	echo "\nPage ID is Null";
	    }

	}
}