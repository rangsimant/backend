<?php
ini_set('max_execution_time', 0); // limit seconds time for load
define('FACEBOOK', '..\libs\facebook_SDK\src\Facebook');
require(__DIR__.'\..\libs\facebook_SDK\autoload.php');
require_once(__DIR__.'\Database.php');

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
// use Facebook\FacebookRequestException;

class Facebook
{
	private $config;
	private $db;
	public function __construct()
	{
		date_default_timezone_set("Asia/Bangkok");
		$this->db = new Database();
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

	public function getFeedsFromPage($account)
	{
		if ($account != null && isset($account->account_id_user))
		{
			$session = $this->setAppSession();
			if (!empty($session)) 
			{
				$account_created_time = strtotime($account->account_last_datetime);
				$query = "posts?fields=id,message,from,created_time,updated_time,attachments{media},link";
				$limit = "&limit=".$this->config['app']['limit'];

				// $post_since = strtotime($account->account_last_datetime);
				// $post_until = strtotime('+2 month', $post_since);
				// echo $account->account_id_user.'/'.$query.$limit.'&until='.$post_until.'&since='.$post_since;die();
				$request = new FacebookRequest($session, 'GET', '/'.$account->account_id_user.'/'.$query.$limit);
				echo "\n[".$account->account_id_user."] : ";
				do 
				{
			        $response = $request->execute();
			        $object = $response->getGraphObject();
			        $data = $object->getProperty('data');
			        if (!empty($data)) 
			        {
			        	echo ">";
			        	$data = $data->asArray();
			        	$post = array();
				       	foreach ($data as $key => $post) 
				       	{
				       		$post_created_time = strtotime($post->created_time);

				       		if (strtotime($account->account_last_datetime) > $post_created_time) 
				       		{
				       			return;
				       		}
				       		if (isset($post->attachments)) 
				       		{
				       			$this->db->insertAuthor($post);
				       			
				       			
				       			if ($account_created_time < $post_created_time) 
				       			{
				       				$insert_post_result = $this->db->insertPost($post, $account);
				       				if ($insert_post_result == TRUE) 
					       			{
					       				$account_created_time = $post_created_time;
					       				$date = date('Y-m-d H:i:s', $account_created_time);
					       				$this->db->updateAccountDateTimeLastPost($account->account_id_user, $date);
					       			}
				       			}
				       			
				       		}
				       		
						}
			    	
			        }
			        else
			        {
			        	return;
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