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
		$this->config = parse_ini_file(__DIR__.'/../config/config.ini',true);

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

			if ($account->account_specific_token == 'yes') 
			{
				$app_id = $this->config['app']['sub_app_id'];
				$app_secret = $this->config['app']['sub_app_secret'];
				FacebookSession::setDefaultApplication($app_id, $app_secret);
				$session = new FacebookSession($this->config['app']['sub_access_token']);
			}

			if (!empty($session)) 
			{
				$account_created_time = strtotime($account->account_last_datetime);
				$query = "posts?fields=id,message,from,created_time,updated_time,attachments{media},link";
				$limit = "&limit=".$this->config['app']['limit'];

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
			        	$obj_post = array();
				       	foreach ($data as $key => $post) 
				       	{
				       		$post_created_time = strtotime($post->created_time);

				       		if (strtotime($account->account_last_datetime) > $post_created_time) 
				       		{
				       			return;
				       		}
				       		if (isset($post->attachments)) 
				       		{
				       			$created_time = date('Y-m-d H:i:s',strtotime($post->created_time)+'7 hours');

				       			$obj_post[$key]['id'] =  $post->id;
								$obj_post[$key]['link'] =  "https://www.facebook.com/".$post->id;
								$obj_post[$key]['message'] =  isset($post->message)?$post->message:'';
								$obj_post[$key]['url_image'] =  $post->attachments->data[0]->media->image->src;
								$obj_post[$key]['created_time'] =  $created_time;
								$obj_post[$key]['from_id'] = $post->from->id;
								$obj_post[$key]['from_name'] =  $post->from->name;

				       			$this->db->insertAuthor($obj_post[$key], $type = 'facebook');
			       				$insert_post_result = $this->db->insertPost($obj_post[$key], $account);
			       				
				       			if ($account_created_time < $post_created_time) 
				       			{
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