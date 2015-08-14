<?php

/**
* 
*/
class LogHelper
{
	public static function debug_log($msg)
	{
		print_r($msg);
	}

	public static function printScreen($msg)
	{
		echo "\n".$msg;
	}
}