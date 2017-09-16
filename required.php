<?php
//required core php files. Binds all aspects of the system

//set error reporting for good structure
session_start();

error_reporting(E_ALL);
ini_set('display_errors', '1');

//load languages
require_once(__DIR__.'/config.php');

//set this to read config eventually
//require_once(__DIR__.'/languages/swedish.php');
require_once(__DIR__.'/languages/english.php');

//auto loads all classes from /classes folder
foreach (glob(__DIR__."/classes/*.php") as $filename) {
	require_once($filename);
}

$global_helper = new Helper;
$application = new Application;

//load the logged in user and all details (if there is one)
if(isset($_SESSION['user_email']) && $_SESSION['user_email'] != '') {
	$global_user = new User($_SESSION['user_email']);
}
?>