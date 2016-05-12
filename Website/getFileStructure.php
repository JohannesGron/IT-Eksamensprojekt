<?php

//Session
session_start();

if (isset($_SESSION["user_id"]) && isset($_SESSION["name"])) {
	include_once("database.php");
	$result = mysqli_query($con, "SELECT * FROM `users` WHERE `user_id`= '" . $_SESSION["user_id"] ."'");
	$row = mysqli_fetch_array($result);
	mysqli_close($con);

	if ($row["file_structure"] == null) {
		echo "empty";
	} else {
		echo $row["file_structure"];
	}
}

?>