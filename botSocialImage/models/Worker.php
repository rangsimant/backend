<?php
require_once(__DIR__.'\Facebook.php');
require_once(__DIR__.'\Queue.php');
require_once(__DIR__.'\Database.php');


class Worker
{
	private $fb;
	private $q;
	private $db;
	private $config;

	public function __construct()
	{
		$this->fb = new Facebook();
		$this->q = new Queue();
		$this->db = new Database();
		$this->config = parse_ini_file('\config\config.ini',true);
	}

	public function processMessages($msg)
	{
		$datas = json_decode($msg);

		foreach ($datas as $key => $account) {
			if ($account->account_channel == 'facebook') 
			{
				$this->fb->getFeedsFromPage($account);
			}
			
		}
	}
}