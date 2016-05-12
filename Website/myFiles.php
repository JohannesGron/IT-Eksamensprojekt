<?php
session_start();
if (!isset($_SESSION["user_id"]) && !isset($_SESSION["name"])) {
	echo "Please Log In!";
	die();
}
?>

<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8"/>

		<title>CloudPack - We protect your digital schoolbag forever and free!</title>
		<link rel="shortcut icon" type="image/png" href="images/icon16.png"/>
		
		<link rel="stylesheet" href="css/myFiles.css"/>
		<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
		<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.2/css/font-awesome.min.css" rel="stylesheet">

		<link rel="chrome-webstore-item" href="https://chrome.google.com/webstore/detail/oghfigglcjjieaafpiholdhmhokgibmh">
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
		<script src="js/myFiles.js"></script>
		<script src="js/jquery.bpopup.min.js"></script>
	</head>
		
	<body>
		<div id="content">
			<div id="navigation_bar">
				<img id="icon" src="images/icon.png" onClick="window.location.href='https://cloudpack.ml'">
				<a href="https://cloudpack.ml" id="cloudpack">CloudPack</a>
				
				<img id="person" src="images/person_placeholder.png">
				<?php
					include_once("database.php");
					$result = mysqli_query($con, "SELECT * FROM `users` WHERE `user_id`= '" . mysqli_real_escape_string($con, $_SESSION["user_id"]) . "'");
					$check = mysqli_fetch_array($result);
					$firstName = explode(" ", $check["name"]);
					echo '<a href="https://cloudpack.ml/logOut.php" id="name">' . $firstName[0] . '</a>';
					mysqli_close($con);
				?>
			</div>

			<div id="tools">
				<a>Backup from Itslearning</a>
				<i id="icon_tool_sync" class="fa fa-refresh" aria-hidden="true" onClick="syncPopup();"></i>
				<i id="icon_tool_delete" class="fa fa-trash" aria-hidden="true" onClick="deleteFiles();"></i>
				<i id="icon_tool_download" class="fa fa-cloud-download" aria-hidden="true" onClick="window.location = 'userDownloadZip.php';"></i>
			</div>

			<div id="file_view_div">
				<div id="file_view">
					
				</div>
			</div>

			<div id="footer">
				<div id="about_box">
					<a class="footer_headline">About CloudPack</a>
					<div id="footer_div_text"><a class="footer_text">CloudPack is non profit organization that strikes to make a secure file platform for every student. The internet has grown since the beginning in 1962 and now the internet has become the new platform for education by replacing physical elements like papir and notebooks. But since learning platforms providers will only keep your files stored if you are subscribed to their service a potentiale disaster could occuor if you for instance graduated. That's the reason why we built CloudPack - to protect your digital schoolbag forever and free!</a></div>
				</div>
				<div class="footer_box" id="special">
					<a class="footer_headline">Keep Connected</a>
					<div class="social_box">
						<img class="social_icons" src="images/github.png"><a class="social" href="https://github.com/JohannesGron">Follow us on Github</a>
					</div>
					<div class="social_box">
						<img class="social_icons" src="images/facebook.png"><a class="social" href="https://www.facebook.com">Like us on Facebook</a>
					</div>
					<div class="social_box">
						<img class="social_icons"src="images/twitter.png"><a class="social" href="https://www.twitter.com">Follow us on Twitter</a>
					</div>
				</div>
				<div class="footer_box">
					<a class="footer_headline">Contact Information</a>
					<i class="fa fa-home item" aria-hidden="true"><a class="inf" style="padding-left:20px;">CloudPack</a></i>
					<i class="fa fa-map-marker item" aria-hidden="true"><a class="inf" style="padding-left:25px";>Mozillavej 21,</a><a class="inf" style="padding-left:34px"> Herning - Danmark</a></i>
					<i class="fa fa-envelope-o item" aria-hidden="true"><a class="inf" style="padding-left:19px";>contact@cloudpack.ml</a></i>
					<i class="fa fa-mobile item" aria-hidden="true"><a class="inf" style="padding-left:28px">+45 90099009</a></i>
				</div>
			</div>

			<div id="popup">
				<div id="popup_content">
					<div id="icon_and_logo">
						<img id="huge_icon" src="images/icon.png">
						<a id="popup_name">CloudPack</a>
					</div>
					<div id="message">
						<div id="step1" class="step">
							<a class="step_message">Welcome!</a>
							<div class="step_message_div"><a class="step_text">This guide will help you through the process of synchronizing your Itslearning files with CloudPack. This will approximately take 15 min. depending on the amount of files you own.</a></div>
							<p class="step_button" onClick="syncGetStarted();">GET ME STARTED</p>						
						</div>
						<div id="step2" class="step">
							<a class="step_message">Install our Chrome Extension</a>
							<div class="step_message_div"><a class="step_text">This is needed in order for us to be able to reach your files.</a></div>

							<img id="chrome_icon" src="images/huge_chrome.png">
							<p class="step_button" onClick='chrome.webstore.install(undefined, function() {$("#step2").hide("slow");$("#step3").css({"display": "block"});startNewWindowTimer();}, undefined);'>INSTALL</p>
						</div>
						<div id="step3" class="step">
							<a class="step_message">Login on Itslearning</a>
							<img id="itslearning_icon" src="images/itslearning.png">
							<p class="step_button" onClick="checkItslearning();">NEXT STEP</p>
						</div>
						<div id="step4" class="step">
							<a class="step_message">We're are ready!</a>
							<div class="step_message_div"><a class="step_text">We are in position and ready to backup your files. You can follow the process at the icon of the CloudPack Extension but you will be notified when we're done.</a></div>
							<p class="step_button" onClick="backupNow();">BACKUP NOW</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>	