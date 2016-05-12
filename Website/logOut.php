<?php
	//Remove session
	session_start();
	session_unset();
	session_destroy(); 
	header("Location: https://cloudpack.ml");
	die();
?>