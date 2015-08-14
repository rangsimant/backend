<?php

/**
* 
*/
class Database
{
	private $fpdo;
	public function __construct()
	{
		date_default_timezone_set("Asia/Bangkok");
		$db_config = parse_ini_file(__DIR__."/../config/database_conf.ini", true);
		
		$pdo = new PDO('mysql:host='.$db_config['host'].';
						dbname='.$db_config['db_name'].';
						charset=UTF8', 
						$db_config['user'], 
						$db_config['pass']);

		$this->fpdo = new FluentPDO($pdo);
		return $this->fpdo;
	}
}