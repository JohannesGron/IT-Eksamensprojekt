<?php

ignore_user_abort(true);

//60 min.
set_time_limit(7000);

//Session
session_start();
session_write_close();

//Get fileStructure from POST request
$fileStructure = base64_decode($_POST["fileStructure"]);

if (isset($_SESSION["user_id"]) && isset($_SESSION["name"]) && isset($fileStructure) && json_decode($fileStructure)) {
	
	include_once("database.php");

	//Update database collum - "fileStructure"
	$clean_fileStructure = mysqli_real_escape_string($con, $fileStructure);
	mysqli_query($con, "UPDATE `users` SET `file_structure`='" . $clean_fileStructure ."' WHERE `user_id`='" . $_SESSION["user_id"] . "'");

	mysqli_query($con, "UPDATE `users` SET `files_owned`= CONCAT(SUBSTRING(`files_owned`, '1', CHAR_LENGTH(`files_owned`) - '1'), '}') WHERE `user_id` ='" . $_SESSION["user_id"] . "'");
	$result = mysqli_query($con, "SELECT * FROM `users` WHERE `user_id`= '" . $_SESSION["user_id"] ."'");
	$row = mysqli_fetch_array($result);

	mysqli_close($con);
	
	$files_owned = $row["files_owned"];
	$files_owned = json_decode($files_owned);
	
	$zip = new ZipArchive();
	$zip->open("/home/admin/zipFiles/" . $_SESSION["user_id"] . ".zip", ZipArchive::CREATE);

	foreach ($files_owned as $obj) {
	   $zip->addFile("/home/admin/userFiles/" . $obj->sha256, $obj->name . $obj->mime);
	}
	
	$zip->close();

	echo "ok";
} else {
	http_response_code(500);
}
?>