<?php
require_once(__DIR__."/helper/QueueHelper.php");
require_once(__DIR__."/model/ClientPage.php");
require_once(__DIR__."/model/FacebookPage.php");
require_once(__DIR__."/model/Post.php");

$q = new QueueHelper();
$CLP = new ClientPage();
$FBP = new FacebookPage();
$post = new Post();


$fb_id_token = $CLP->getFacebookPageIDAndToken();
$post_id_token = $post->getPostIDAndToken();
$comment_id_token = $post->getCommentIDAndToken();
$merge_post_comment = array_merge($fb_id_token, $post_id_token);
$merge_post_comment_reply = array_merge($merge_post_comment, $comment_id_token);

foreach ($merge_post_comment_reply as $val) 
{
	$facebook_id = (isset($val['facebook_page_id'])) ? $val['facebook_page_id'] : $val['post_social_id'] ;
	$q->publish(json_encode($val), $facebook_id);
	if (isset($val['facebook_page_id'])) 
	{
		$FBP->updateTimeStamp($val['facebook_page_id']);
	}
}
$q->closeConnection();


