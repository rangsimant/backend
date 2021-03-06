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
			$picture = isset($author['picture']['url'])?$author['picture']['url']:'';
			$value = array(
				"author_social_id" => $author['id'],
				"author_name" => $author['name'],
				"author_channel" => $channel,
				"author_image_url" => $picture,
				
			);
			
			$result = $this->db->insertInto('author')->values($value)->execute();
			return $result;
		}
	}
}

	