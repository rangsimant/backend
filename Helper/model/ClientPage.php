<?php

require_once(__DIR__."/Database.php");
/**
* 
*/
class ClientPage extends Database
{
	private $db;
	function __construct()
	{
		$this->db = parent::__construct();
	}

	public function getFacebookPageIDAndToken()
	{
		$query = $this->db->from('client_page')
						->leftJoin('facebook_page ON facebook_page.facebook_id = client_page.facebook_id')
						->orderBy('facebook_page.lasted_fetch ASC')
						->select([ 
								'client_page.cp_access_token',
								'facebook_page.facebook_id',
								'facebook_page.facebook_page_id',
								'facebook_page.lasted_fetch'
								]);
		$data = array();
		foreach ($query as $row) 
		{
		    $temp['facebook_id'] = $row['facebook_id'];
		    $temp['cp_access_token'] = $row['cp_access_token'];
		    $temp['facebook_page_id'] = $row['facebook_page_id'];
		    $temp['lasted_fetch'] = $row['lasted_fetch'];
		    $temp['channel'] = 'facebook';
		    $data[] = $temp;
		}

		return $data;
	}
}