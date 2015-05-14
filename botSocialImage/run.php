<?php

require_once(__DIR__.'../models/facebook.php');
$fb = new Facebook();
$fb->getFeeds();