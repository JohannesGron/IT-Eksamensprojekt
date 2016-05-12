<?php

//Session
session_start();
session_write_close();

//16 min.
set_time_limit(1000);

$fileID = $_GET["fileID"];

if (isset($_SESSION["user_id"]) && isset($_SESSION["name"])) {
	include_once("database.php");
	$result = mysqli_query($con, "SELECT * FROM `users` WHERE `user_id`= '" . $_SESSION["user_id"] ."'");
	$row = mysqli_fetch_array($result);
	mysqli_close($con);

	$files_owned = $row["files_owned"];
	$files_owned = json_decode($files_owned);

	if (isset($files_owned->$fileID)) {
		header("Content-Length: " . filesize("/home/admin/userFiles/" . $files_owned->$fileID->sha256));
		header("Content-type: " . $files_owned->$fileID->mime_type);
		header("Content-Disposition: attachment; filename='" . $files_owned->$fileID->name . $files_owned->$fileID->mime . "'");
		readfile("/home/admin/userFiles/" . $files_owned->$fileID->sha256);
	} else {
		http_response_code(500);
	}
} else {
	http_response_code(500);
}
?>