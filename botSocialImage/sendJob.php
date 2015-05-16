<?php

require_once(__DIR__.'../models/Queue.php');
require_once(__DIR__.'../models/Database.php');
$q = new Queue();
$db = new Database();

$account = $db->getAccount();
$q->sendToQueue($account);


