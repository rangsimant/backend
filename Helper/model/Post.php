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
	
	public function getPostIDAndToken()
	{
		$query = $this->db->from('post')
						->where('post.post_type','post')		
						->leftJoin('client_page ON client_page.facebook_id = post.Facebook_id')
						->select('client_page.cp_access_token');
						
		$data = array();
		// print_r($query);die();
		foreach ($query as $row) 
		{
			$temp['facebook_id'] = $row['Facebook_id'];
			$temp['post_social_id'] = $row['post_social_id'];
			$temp['cp_access_token'] = $row['cp_access_token'];
			$temp['channel'] = 'facebook';
			$temp['type'] = 'comment';
			$data[] = $temp;
		}
		
		return $data;
	}
	
	public function getCommentIDAndToken()
	{
		$query = $this->db->from('post')
						->where('post.post_type','comment')		
						->leftJoin('client_page ON client_page.facebook_id = post.Facebook_id')
						->select('client_page.cp_access_token');
						
		$data = array();
		// print_r($query);die();
		foreach ($query as $row) 
		{
			$temp['facebook_id'] = $row['Facebook_id'];
			$temp['post_social_id'] = $row['post_social_id'];
			$temp['cp_access_token'] = $row['cp_access_token'];
			$temp['channel'] = 'facebook';
			$temp['type'] = 'reply';
			$data[] = $temp;
		}
		
		return $data;
	}

	public function insertNewPostCommentReply($posts = array(),$data)
	{
		$id = ($data['type'] == 'post')?$data['facebook_page_id']:$data['post_social_id'];
		echo "\n[".$id."] : ";
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
					"Facebook_id" => $data['facebook_id'],
					"Author_social_id" => $post['from']['id'],
					"post_social_id" => $post['id'],
					"post_parent_id" => ($data['type']=='comment' || $data['type']=='reply')?$data['post_social_id']:null,
					"post_link" => isset($post['link'])?$post['link']:'',
					"post_created_at" => $created_time->format('Y-m-d H:i:s'),
					"post_type" => $data['type'],
					"post_channel" => "facebook",
					"post_image_url" => isset($post['attachments'][0]['media'])?$post['attachments'][0]['media']['image']['src']:'',
					"post_likes_count" => isset($post['like_count'])?$post['like_count']:count($post['likes']),
					"post_comment_count" => isset($post['comments'])?count($post['comments']):0,
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
					if ($idx == 0 && $data['type'] == 'post') 
					{
						$lasted_fetch = $created_time->format('Y-m-d H:i:s');
						$this->facebook_page->updateLastedFetch($data['facebook_id'], $lasted_fetch);
					}
				}
			}
		}
		echo "\tNew ".$new_post." ".$data['type'].", ".$new_author." Author";
	}
	
	
}