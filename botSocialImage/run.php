<?php

require_once(__DIR__.'../models/Facebook.php');
require_once(__DIR__.'../models/Queue.php');
require_once(__DIR__.'../models/Database.php');
$fb = new Facebook();
$q = new Queue();
$db = new Database();

$page_id = new stdClass();
$page_id->account_id_user = "334239899993973";
$page_id->account_last_datetime = "2012-01-01 00:00:00";
$page_id->account_channel = "facebook";
$page_id->account_subject = "nissan";

$fb->getFeedsFromPage($page_id);