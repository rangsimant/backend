<?php

require_once(__DIR__.'../models/Queue.php');
require_once(__DIR__.'../models/Database.php');
$q = new Queue();
$db = new Database();

$account = $db->getAccount(null,10);
$q->sendToQueue($account);


