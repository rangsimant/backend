<?php
require_once(__DIR__.'\Database.php');

use MetzWeb\Instagram\Instagram;

class Instagrams
{
	private $config;
	private $ig;
	private $account_last_datetime;
	private $account_created_time;
	private $db;
	private $account;
	function __construct()
	{
		date_default_timezone_set("Asia/Bangkok");
		$this->db = new Database();
		$this->config = parse_ini_file(__DIR__.'/../config/config.ini',true);

		$this->ig = new Instagram(array(
		    'apiKey'      => $this->config['instagram']['apiKey'],
		    'apiSecret'   => $this->config['instagram']['apiSecret'],
		    'apiCallback' => $this->config['instagram']['apiCallback']
		));

	}

	public function getIDFromName($name)
	{
		$limit = 1;
		$user = $this->ig->searchUser($name, $limit);
		return $user;
	}

	public function getUserMedia($id)
	{
		$media = $this->ig->getUserMedia($id);
		return $media;
	}

	public function pagination($obj_media)
	{
		$media = $this->ig->pagination($obj_media);
		return $media;
	}

	public function getMedia($account)
	{
		$this->account = $account;
		if ($account != null && isset($account->account_id_user))
		{
			$this->account_last_datetime = strtotime($account->account_last_datetime);
			$this->account_created_time = strtotime($account->account_last_datetime);

			$account_created_time = strtotime($account->account_last_datetime);
			echo "\n[".$account->account_id_user."] : ";
			$medias = $this->loopAllMedia($account->account_id_user);
		}
		
		return $medias;
	}

	protected function loopAllMedia($id_user)
	{
		$obj_media = array();
		$key = 1;
		$medias = $this->getUserMedia($id_user);
		if (!empty($medias->data)) 
		{
			do 
			{
				echo ">";
				foreach ($medias->data as $media) 
				{
					$post_created_time = $media->created_time;
					if ($this->account_last_datetime > $post_created_time) 
		       		{
		       			return;
		       		}
		       		if (isset($media->caption->id)) 
		       		{
						if (isset($media->images)) 
						{
							$obj_media[$key]['id'] =  $media->id;
							$obj_media[$key]['link'] =  $media->link;
							$obj_media[$key]['message'] =  isset($media->caption->text)?$media->caption->text:'';
							$obj_media[$key]['url_image'] =  $media->images->standard_resolution->url;
							$obj_media[$key]['created_time'] =  date('Y-m-d H:i:s', $media->created_time);
							$obj_media[$key]['from_id'] =  $media->caption->from->id;
							$obj_media[$key]['from_name'] =  isset($media->caption->from->full_name)?$media->caption->from->full_name:$media->caption->from->username;

							$this->db->insertAuthor($obj_media[$key], $type = 'instagram');
		       				$insert_post_result = $this->db->insertPost($obj_media[$key], $this->account);

		       				if ($this->account_created_time < $post_created_time) 
			       			{
			       				if ($insert_post_result == TRUE) 
				       			{
				       				$this->account_created_time = $post_created_time;
				       				$date = date('Y-m-d H:i:s', $this->account_created_time);
				       				$this->db->updateAccountDateTimeLastPost($this->account->account_id_user, $date);
				       			}
			       			}

							$key++;
						}
					}
				}
			} while ($medias = $this->ig->pagination($medias));
		}
		return $obj_media;
	}
}