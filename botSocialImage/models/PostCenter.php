<?php
class PostCenter 
{
	private $config;
	private $db;
	function __construct()
	{
		date_default_timezone_set("Asia/Bangkok");
		$this->config = parse_ini_file(__DIR__.'/../config/postcenter.ini',true);
		$this->connection($this->config['db']['host'], 
						$this->config['db']['user'], 
						$this->config['db']['pass'], 
						$this->config['db']['db_name']
					);
	}

	public function connection($host, $user, $pass, $db_name)
	{
		try
		{
			$this->db = new PDO('mysql:host='.$host.';dbname='.$db_name.';charset=UTF8', $user, $pass);
		}
		catch (Exception $e)
		{
			echo "Connection Fail.\n";
			exit();
		}
	}

	public function getSocialID($last_datetime, $limit = 10)
	{
		(empty($last_datetime))? $last_datetime = date("Y-m-d H:i:s"):$last_datetime;

		$sql = "SELECT facebook_page_id
		FROM facebook_page 
		WHERE timestamp < '".$last_datetime."'
		ORDER BY timestamp ASC
		LIMIT ".$limit;

		$data = array();
		$facebook_page_id = array();
	    foreach ($this->db->query($sql) as $key => $row) {
	        $data[$key]['id'] = $row['facebook_page_id'];
	        $facebook_page_id[] = $row['facebook_page_id'];
	    }

	    $facebook_page_id = implode(',', $facebook_page_id);

	    $this->updateTimeStampGetPage($facebook_page_id);

		return $data;
	}

	private function updateTimeStampGetPage($id)
	{
		$now = date("Y-m-d H:i:s");
		$sql = "UPDATE facebook_page SET timestamp = '".$now."' WHERE facebook_page_id IN (".$id.")";
		$result = $this->db->exec($sql);
	}

	
}