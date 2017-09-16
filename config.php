<?php
	//database settings
	$database_location = 'localhost';
	$database_name = 'arcent_lovethynumbers';
	$database_user = 'arcent_screen';
	$database_password = 'screenviewer231';
	
	$db = new PDO('mysql:host='.$database_location.';dbname='.$database_name.';charset=utf8mb4', $database_user, $database_password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	
	$global_user_files = 'user_files/';
	$global_site_path = '/dev/lovethynumbers/';
	
?>