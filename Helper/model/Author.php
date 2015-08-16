<?php
require_once(__DIR__."/Database.php");
require_once(__DIR__."/FacebookPage.php");

/**
 * 
 */
class Author extends Database
{
	private $db;
	function __construct()
	{
		$this->db = parent::__construct();
	}
	
	public function insertAuthor($author, $channel)
	{
		if (!empty($author)) 
		{
			$value = array(
				"author_social_id" => $author['id'],
				"author_name" => $author['name'],
				"author_channel" => $channel,
				
			);
			
			$result = $this->db->insertInto('author')->values($value)->execute();
			return $result;
		}
	}
}

	