<?php 
/**
* Plugin name: LOOPIS enviroment reader
* Description: Reads enviroment varibles into constants.
* Author: Hubert Hilborn
* Since 2026-02-09
*
*/

// Enviroment loader
function load_environment(string $file_path){
	$lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	foreach($lines as $line){
		$line = trim($line);
		// Skip comments and artsy line jumps
		if (!$line||strpos($line,'#') === 0) continue;
		[$name, $value] = explode('=', $line, 2);
		$name = trim($name);
		$value = trim($value);
		putenv("$name=$value");
		$_ENV[$name]=$value;
	}
}

// Define path
$environment_filepath = __DIR__ . '/../../.env';

// Load enviroment file if it exists
if (file_exists($environment_filepath)){
	load_environment($environment_filepath);
}

// Admin password
define('ADMIN_PASS', getenv('ADMIN_PASS'));

// Stripe secret
define('LOOPIS_STRIPE_SECRET_KEY', getenv('LOOPIS_STRIPE_SECRET_KEY'));

