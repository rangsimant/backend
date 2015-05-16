<?php

require_once(__DIR__.'../models/Facebook.php');
require_once(__DIR__.'../models/Queue.php');
require_once(__DIR__.'../models/Database.php');
$fb = new Facebook();
$q = new Queue();
$db = new Database();

$page_id = "ceclip";
$data = new stdClass();
$data->id = "1530700275";
$data->name = "Rangsimant Hanwongsa";
$data->created_at = "2015-05-15 00:00:00";
$result = $db->getAccount();
echo "<pre>";
print_r($result);
die();
// $q->sendToQueue($data);die();
$q->receiveFromQueue();die();
$fb->getFeedsFromPage($page_id);