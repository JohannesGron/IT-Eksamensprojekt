<?php

//Session
session_start();
session_write_close();

//60 min.
set_time_limit(7200);

if (isset($_SESSION["user_id"]) && isset($_SESSION["name"]) && file_exists("/home/admin/zipFiles/" . $_SESSION["user_id"] . ".zip")) {
	header("Content-Length: " . filesize("/home/admin/zipFiles/" . $_SESSION["user_id"] . ".zip"));
	header("Content-type: application/zip");
	header("Content-Disposition: attachment; filename='MyFiles.zip'");
	readfile("/home/admin/zipFiles/" . $_SESSION["user_id"] . ".zip");
} else {
	http_response_code(500);
}
?>