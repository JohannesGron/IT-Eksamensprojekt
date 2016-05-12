<?php

//Session
session_start();

$email = $_POST["email"];
$password = $_POST["password"];

include_once("database.php");
$clean_email = mysqli_real_escape_string($con, $email);

$result = mysqli_query($con, "SELECT * FROM `users` WHERE `email`= '" . $clean_email ."'");
$check = mysqli_fetch_array($result);
if (crypt($password, $check["password"]) == $check["password"]) {
	$_SESSION["user_id"] = $check["user_id"];
	$_SESSION["name"] = $check["name"];
	echo "succes";
} else {
	echo "error->user_not_found";
}

mysqli_close($con);
?>