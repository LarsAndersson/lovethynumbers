<?php 
	//destroy all session data
	session_start();
	session_destroy();
	header('Location: login');
?>