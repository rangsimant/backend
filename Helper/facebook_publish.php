<?php
require_once(__DIR__."/helper/QueueHelper.php");
require_once(__DIR__."/model/ClientPage.php");
require_once(__DIR__."/model/FacebookPage.php");

$q = new QueueHelper();
$CLP = new ClientPage();
$FBP = new FacebookPage();


$fb_id_token = $CLP->getFacebookPageIDAndToken();

foreach ($fb_id_token as $val) 
{
	$q->publish(json_encode($val), $val['facebook_page_id']);
	$FBP->updateTimeStamp($val['facebook_page_id']);
}
$q->closeConnection();


