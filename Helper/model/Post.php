<?php 
require_once(__DIR__."/Database.php");
require_once(__DIR__."/FacebookPage.php");
require_once(__DIR__."/Author.php");

/**
* 
*/
class Post extends Database
{
	
	private $db;
	private $facebook_page;
	function __construct()
	{
		$this->db = parent::__construct();
		$this->facebook_page = new FacebookPage();
		$this->author = new Author();
	}

	public function insertNewPost($posts = array(),$page)
	{
		echo "\n[".$page['facebook_page_id']."] : ";
		$new_post = 0;
		$new_author = 0;
		if (!empty($posts)) 
		{
			foreach ($posts as $idx => $post) 
			{
				$created_time = $post['created_time'];
				$created_time->setTimezone(new DateTimeZone('Asia/Bangkok'));
				
				$value = array(
					"post_body" => isset($post['message'])?$post['message']:'',
					"Facebook_id" => $page['facebook_id'],
					"Author_social_id" => $post['from']['id'],
					"post_social_id" => $post['id'],
					"post_link" => isset($post['link'])?$post['link']:'',
					"post_created_at" => $created_time->format('Y-m-d H:i:s'),
					"post_type" => "post",
					"post_channel" => "facebook",
					"post_image_url" => isset($post['attachments'][0]['media'])?$post['attachments'][0]['media']['image']['src']:'',
					"post_likes_count" => count($post['likes']),
					"post_comment_count" => count($post['comments']),
					"created_at" => date('Y-m-d H:i:s'),
					"updated_at" => date('Y-m-d H:i:s'),
					);

				$author_result = $this->author->insertAuthor($post['from'], $channel = 'facebook');
				$post_result = $this->db->insertInto('post')->values($value)->execute();
				if ($author_result) 
				{
					$new_author++;
				}
				
				if ($post_result) 
				{
					$new_post++;
					if ($idx == 0) 
					{
						$lasted_fetch = $created_time->format('Y-m-d H:i:s');
					}

					$this->facebook_page->updateLastedFetch($page['facebook_id'], $lasted_fetch);
				}
			}
		}
		echo "\tNew ".$new_post." Post, ".$new_author." Author";
	}
}