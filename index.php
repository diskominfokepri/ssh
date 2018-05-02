<?php

$checkPathsRW = array(
	'assets',
	'protected/runtime',
);

foreach($checkPathsRW as $p)
{
	$path = __DIR__.'/'.$p;
	if(!is_writable($path))
		die("Please make sure that the $path directory is writable by the web server user.");
}

/*
 * PHP >= 5.3 doesn't rely anymore on the system environment to decide default timezone
 * If no value has been set in php.ini for "date.timezone" php spits a runtime notice
 * Enforce a proper default value here in order to avoid the runtime notices
 */
//date_default_timezone_set('UTC');

require 'protected/vendor/autoload.php';
$application = new \Prado\TApplication;
$application->run();