<?php

class Database 
{
	private $config;
	private $db;
	function __construct()
	{
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

	private function updateTimeStampGetAccount($account_id_user)
	{
		$now = date("Y-m-d H:i:s");
		$sql = "UPDATE account SET account_timestamp = '".$now."' WHERE account_id_user IN (".$account_id_user.")";
		$result = $this->db->exec($sql);
	}
}