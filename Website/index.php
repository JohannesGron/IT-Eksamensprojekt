<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8"/>

		<title>CloudPack - We protect your digital schoolbag forever and free!</title>
		<link rel="shortcut icon" type="image/png" href="images/icon16.png"/>
		
		<link rel="stylesheet" href="css/frontpage.css"/>
		<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
		<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.2/css/font-awesome.min.css" rel="stylesheet">

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
		<script src="js/frontpage.js"></script>
		<script src="js/jquery.bpopup.min.js"></script>

	</head>
		
	<body>
		<div id="content">
			<div id="navigation_bar">
				<img id="icon" src="images/icon.png" onClick="window.location.href='https://cloudpack.ml'">
				<a href="https://cloudpack.ml" id="name">CloudPack</a>
				
				<ul id="nav_right">
					<li><a href="#home" class="menu">HOME</a></li>
				    <li><a href="#features" class="menu">FEATURES</a></li>
				    <li><a href="#howitworks" class="menu">HOW IT WORKS</a></li>
					<?php
						session_start();
						if (isset($_SESSION["user_id"]) && isset($_SESSION["name"])) {
echo <<<TEXT
	<li><a href="https://cloudpack.ml/myFiles.php">MY FILES</a></li>
	<li><i style="cursor: pointer;" onClick="location.replace('https://cloudpack.ml/logOut.php');" class="fa fa-sign-out" aria-hidden="true"></i></li>
TEXT;
						} else {
echo <<<TEXT
	<li><i style="cursor: pointer;" onClick="popup('login');" class="fa fa-sign-in" aria-hidden="true"></i></li>
TEXT;
						}
					?>
		     	</ul>
			</div>

			<div id="home">
				<div id="wow_box">
					<a id="wow_text">Backup your digital school files</a>
					<a id="wow_des">Before you lose them!</a>
					<div style="text-align: center; width: 100%;">
						<a class="get_started" onClick="getStarted();">Get Started</a>
					</div>
				</div>
			</div>					

			<div id="features">				
				<a id="f_headline" class="page_headline">Features</a>
				<div id="feature_list">
					<div class="feature">
						<img class="fea_img" src="images/fast.png">
						<a class="mini_headline">Fast & Easy</a>
						<a class="description">CloudPack is built the be fast while still being easy to use.</a>
					</div>
					<div class="feature">
						<img class="fea_img" src="images/secure.png">
						<a class="mini_headline">Secure</a>
						<a class="description">No usernames/passwords/cookies is used during the backup process on our servers because everything is done in your browser and transfered securely(SSL/TLS) to our servers.</a>
					</div>
					<div class="feature">
						<img class="fea_img" src="images/free.png">
						<a class="mini_headline">Free</a>
						<a class="description">Our service is free and will forever be free.</a>
					</div>
				</div>
			</div>

			<div id="howitworks">
				<a id="hiw_headline" class="page_headline">How it works</a>
				<div id="hiw_content">
					<div class="step">
						<img class="step_image" src="images/signup.png">
						<a class="mini_headline">Create Account</a>
					</div>
					<div class="separator"></div>
					<div class="step">
						<img class="step_image" src="images/chrome.png">
						<a class="mini_headline">Install Chrome Extension</a>
					</div>
					<div class="separator"></div>
					<div class="step">
						<img class="step_image" src="images/instructions.png">
						<a class="mini_headline">Follow backup instructions</a>
					</div>
					<div class="separator"></div>
					<div class="step">
						<img class="step_image" src="images/succes.png">
						<a class="mini_headline">Enjoy!</a>
					</div>					
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
		</div>

		<div id="popup">
			<div id="popup_content">
				<div id="icon_and_logo">
					<img id="huge_icon" src="images/icon.png">
					<a id="form_name">CloudPack</a>
				</div>
				<div id="form">
					<div class="form_login form_content">
						<a class="form_content_name">Log In</a>
						<input type="email" class="input" id="lg_email" placeholder="Email Address" onkeypress="enter(event, 'login');"> 
						<input type="password" class="input" id="lg_password" placeholder="Password" onkeypress="enter(event, 'login');">
						<p class="form_button" onClick="ajax('login');">LOGIN</p>
					</div>

					<div class="form_sign_up form_content">
						<a class="form_content_name">Sign Up</a>
						<input type="text" class="input" id="su_name" placeholder="Full Name" onkeypress="enter(event, 'sign-up');">
						<input type="email" class="input" id="su_email" placeholder="Email Address" onkeypress="enter(event, 'sign-up');">
						<input type="password" class="input" id="su_password" placeholder="Password" onkeypress="enter(event, 'sign-up');">
						<input type="password" class="input" id="su_password2" placeholder="Retype Password" onkeypress="enter(event, 'sign-up');">
						<p class="form_button" onClick="ajax('sign-up');">SIGN UP</p>
					</div>
				</div>
				<div class="form_helper form_login"><a class="form_mini">Not registered? </a><a class="form_mini" onClick="toggle_form();"style="text-decoration: underline; cursor: pointer;"> Create an account</a></div>
				<div class="form_helper form_sign_up"><a class="form_mini">Already have an account? </a><a class="form_mini" onClick="toggle_form();"style="text-decoration: underline; cursor: pointer;"> Log In</a></div>
			</div>
		</div>
	</body>
</html>	