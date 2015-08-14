<?php
require_once(__DIR__."/helper/QueueHelper.php");
require_once(__DIR__."/model/ClientPage.php");

$q = new QueueHelper();
$db = new ClientPage();


$fb_id_token = $db->getFacebookPageIDAndToken();

foreach ($fb_id_token as $val) 
{
	$q->publish(json_encode($val), $val['facebook_page_id']);
}
$q->closeConnection();


