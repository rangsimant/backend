<?php

require_once(__DIR__.'../models/Facebook.php');
require_once(__DIR__.'../models/Queue.php');
require_once(__DIR__.'../models/Database.php');
require_once(__DIR__.'../models/Instagram.php');
$fb = new Facebook();
$q = new Queue();
$db = new Database();
$ig = new Instagrams();

$db->testInsertLangTH();
