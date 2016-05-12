<?php

// Vi starter session funktionen for at vi kan tilgå værdierne for sessionen
session_start();

// Vi lukker for skrive mekanismen for session cookien da dette kan få andre scripts til at vente hvis 
// Dette php-script tager for langt tid om at køre
session_write_close();

// Vi tjekker om der overhovedet er nogen session værdi for henholdsvis "user_id" og "name". Hvis ikke så er de ikke logget ind
// Den tredje parameter får fat i HTTP POST værdien "dontbrowsetothisurl" og ser om den er sand. Dette gør jeg for at man ikke
// Uheldigvis kommer til at browse ind på denne url. Da HTTP POST REQUEST ikke kan sendes via et standard link er vi på den sikre side
if (isset($_SESSION["user_id"]) && isset($_SESSION["name"]) && ($_POST["dontbrowsetothisurl"] == true)) {
	
	// Vi inkluderer database filen hvor der indgår server, brugernavn, password, database-name + den starter en connection
	include_once("database.php");

	// Vi forespørger alle brugere i tabellen "users"
	$result = mysqli_query($con, "SELECT * FROM `users` WHERE 1");

	// Sætter to tomme liste
	$allSHA = [];
	$userFiles = [];

	// Vi kører igennem hver bruger i tabellen
	while ($row = mysqli_fetch_array($result)){

		// Hvis vi tjekker om dette ikke er den bruger der er logget ind
		if ($row["user_id"] != $_SESSION["user_id"]) {

			// Vi tjekker om brugeren "ejer" noget
			if ($row["files_owned"] != null) {

				// Vi foretager en regular expression på indholdet for at finde de sha256 værdier som brugeren ejer
				preg_match_all("/sha256\": \"[a-zA-Z0-9]{64}/", $row["files_owned"], $tempArray);

				// Vi samler listen $allSHA med listen som regular expression smed ud
				$allSHA = array_merge($allSHA, $tempArray[0]);
			}			
		} else {

			//Vi foretager en regular expression på indholdet for at finde de sha256 værdier som brugeren ejer
			preg_match_all("/sha256\": \"[a-zA-Z0-9]{64}/", $row["files_owned"], $tempArray);

			//Vi lægger de matchende værdier op i listen $userFiles
			$userFiles = $tempArray[0];
		}
	}

	// Vi kører nu igennem hver sha256 element i $userFiles
	foreach ($userFiles as $key) {

		// Vi tjekker om sha256 værdien er i den fælles liste
		if (!in_array($key, $allSHA)) {

			// Filen findes ikke i den fælles liste og vi kan hermed slette filen
			unlink("/home/admin/userFiles/" . str_replace('sha256": "', "", $key));
		}
	}
	
	// Vi opdaterer brugeren kollonerne - "files_owned" og "file_structure" og sætter dem null
	mysqli_query($con, "UPDATE `users` SET `file_structure`=NULL,`files_owned`=NULL WHERE `user_id`='" . $_SESSION["user_id"] . "'");
	
	// Hvis zipfilen for brugeren existerer så skal den gå ind i forgreningen
	if (file_exists("/home/admin/zipFiles/" . $_SESSION["user_id"] . ".zip")) {

		// Vi sletter zip filen
		unlink("/home/admin/zipFiles/" . $_SESSION["user_id"] . ".zip");
	}
} else {

	//Vi sender fejlen - Intern Serverfejl ud ved HTTP ERROR 500
	http_response_code(500);
}
?>