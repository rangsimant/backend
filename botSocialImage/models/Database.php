<?php

class Database 
{
	private $config;
	private $db;
	function __construct()
	{
		date_default_timezone_set("Asia/Bangkok");
		$this->config = parse_ini_file('\config\config.ini',true);

		$this->connection($this->config['db_test']['host'], 
						$this->config['db_test']['user'], 
						$this->config['db_test']['pass'], 
						$this->config['db_test']['db_name']
					);
	}

	public function connection($host, $user, $pass, $db_name)
	{
		$this->db = new PDO('mysql:host='.$host.';dbname='.$db_name, $user, $pass);
	}

	public function getAccount($last_datetime = null, $limit = 10)
	{
		(empty($last_datetime))? $last_datetime = date("Y-m-d H:i:s"):$last_datetime;

		$sql = "SELECT account_id_user, account_channel, account_last_datetime, account_subject
		FROM account 
		WHERE account_available = 'open' AND account_last_datetime < '".$last_datetime."'
		ORDER BY account_timestamp ASC
		LIMIT ".$limit;

		$data = array();
		$account_id_user = array();
	    foreach ($this->db->query($sql) as $key => $row) {
	        $data[$key]['account_id_user'] = $row['account_id_user'];
	        $data[$key]['account_channel'] = $row['account_channel'];
	        $data[$key]['account_last_datetime'] = $row['account_last_datetime'];
	        $data[$key]['account_subject'] = $row['account_subject'];
	        $account_id_user[] = $row['account_id_user'];
	    }

	    $account_id_user = implode(',', $account_id_user);

	    $this->updateTimeStampGetAccount($account_id_user);

		return $data;
	}



	public function insertPost($post, $account)
	{
		$sql = "INSERT IGNORE INTO post(author_id, post_social_id, post_text, post_created_time, post_channel, post_link, post_subject, post_url_image)
		 		VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(1,$post->from->id);
		$stmt->bindParam(2,$post->id);
		$stmt->bindParam(3,isset($post->message)?$post->message:'');
		$stmt->bindParam(4,$post->created_time);
		$stmt->bindParam(5,$account->account_channel);
		$stmt->bindParam(6,$post->link);
		$stmt->bindParam(7,$account->account_subject);
		$stmt->bindParam(8,$post->attachments->data[0]->media->image->src);

		$result = $stmt->execute();
		if ($result)
		{
			echo ".";
		}
		else
		{
			echo "F";
		}
	}

	public function insertAuthor($post)
	{
		$sql = "INSERT IGNORE INTO author(author_id,author_displayname,author_type)
				VALUES(?, ?, ?)";

		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(1,$post->from->id);
		$stmt->bindParam(2,$post->id);
		$stmt->bindParam(3,$post->from->name);
	}

	public function updateAccountDateTimeLastPost($account_id_user, $created_time)
	{
		$sql = "UPDATE account SET account_last_datetime = '".$created_time."' WHERE account_id_user = '".$account_id_user."'";
		$result = $this->db->exec($sql);
		if ($result)
			echo "+";
		else
			echo "-";
	}	

	private function updateTimeStampGetAccount($account_id_user)
	{
		$now = date("Y-m-d H:i:s");
		$sql = "UPDATE account SET account_timestamp = '".$now."' WHERE account_id_user IN (".$account_id_user.")";
		$result = $this->db->exec($sql);
	}
}