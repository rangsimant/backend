<?php

require_once(__DIR__."/Database.php");
/**
* 
*/
class FacebookPage extends Database
{
	private $db;
	function __construct()
	{
		$this->db = parent::__construct();
	}

	public function updateTimeStamp($facebook_page_id)
	{
		$set = array('timestamp' => date('Y-m-d H:i:s'));

		$query = $this->db->update('facebook_page')
						->set($set)
						->where('facebook_page_id', $facebook_page_id);
		return $query->execute();
	}

	public function updateLastedFetch($facebook_page_id, $lasted_fetch)
	{
		$set = array('lasted_fetch' => $lasted_fetch);

		$query = $this->db->update('facebook_page')
						->set($set)
						->where('facebook_id', $facebook_page_id);
		return $query->execute();
	}
}