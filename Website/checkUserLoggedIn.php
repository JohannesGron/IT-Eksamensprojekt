<?php

//Session
session_start();

if (isset($_SESSION["user_id"])) {
	echo "yes";
} else {
	echo "no";
}
?>