<?php

require_once(__DIR__.'../models/Facebook.php');
require_once(__DIR__.'../models/Queue.php');
require_once(__DIR__.'../models/Database.php');
require_once(__DIR__.'../models/Instagram.php');
$fb = new Facebook();
$q = new Queue();
$db = new Database();
$ig = new Instagrams();

// $page_id = new stdClass();
// $page_id->account_id_user = "334239899993973";
// $page_id->account_last_datetime = "2012-01-01 00:00:00";
// $page_id->account_channel = "facebook";
// $page_id->account_subject = "nissan";
echo "<pre>";
$id_user = array('178668260');
$i=1;
foreach ($id_user as $key => $id) 
{
	$media = $ig->getUserMedia($id);
	do {
		foreach ($media->data as $key => $value) {
			echo $i." ".$value->id." ".$value->images->standard_resolution->url." ".$value->link."\n";
			// print_r($value);
			$i++;
		}
	} while ($media = $ig->pagination($media));
	
}