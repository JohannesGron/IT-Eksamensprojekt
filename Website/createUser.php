<?php
$name = $_POST["name"];
$email = $_POST["email"];
$password = $_POST["password"];

$hash = crypt($password, '$6$rounds=5000$' . bin2hex(mcrypt_create_iv(50, MCRYPT_DEV_URANDOM)));

include_once("database.php");

$result = mysqli_query($con, "INSERT INTO `users` (`name`, `email`, `password`) VALUES ('" . mysqli_real_escape_string($con, $name) . "', '" . mysqli_real_escape_string($con, $email) . "', '" . mysqli_real_escape_string($con, $hash) . "');");

if ($result) {
	$result2 = mysqli_query($con, "SELECT * FROM `users` WHERE `email`= '" . mysqli_real_escape_string($con, $email) ."'");
	$check = mysqli_fetch_array($result2);

	session_start();
	$_SESSION["user_id"] = $check["user_id"];
	$_SESSION["name"] = $check["name"];
	echo "succes";
} else {
	echo "error";
}

mysqli_close($con);
?>