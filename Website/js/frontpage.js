$(document).ready(function(){
    $(window).scroll(function(){
    	if ($(this).scrollTop() > 0) {
    		$("#navigation_bar").css({
    			"position": "fixed",
    			"top": "0px",
    			"background-color": "#1A384C"
    		});
    	} else {
    		$("#navigation_bar").css({
    			"position": "absolute",
    			"top": "0px",
    			"background-color": "transparent"
    		});
    	}
	});


	$(".menu").click(function(e){
		e.preventDefault();
        var position = $($(this).attr("href")).offset().top - 75;
        if ($(this).attr("href") == "#features") {
            position -= 60;
        }
        console.log($(this).attr("href"));
        $("html,body").animate({scrollTop: position}, 500);
	});
});


//POPUP WINDOW
function popup(type) {
    if (type == "login") {
        $(".form_login").css({"display": "inline"});
        $(".form_sign_up").css({"display": "none"});
    } else {
        $(".form_login").css({"display": "none"});
        $(".form_sign_up").css({"display": "inline"});
    }

    $("#popup").bPopup({
        fadeSpeed: "fast",
        opacity: 1,
        followSpeed: 0,
        modalColor: "#1E6798"
    });
}


//LOGIN OR SIGN UP
function ajax(type) {
    if (type == "login") {
        var object = {
            email: document.getElementById("lg_email").value,
            password: document.getElementById("lg_password").value 
        };

        $.post("https://cloudpack.ml/login.php", object, function(response){
            if (response == "succes") {
                window.location = "https://cloudpack.ml/myFiles.php";
            } else if (response == "error->user_not_found") {
                $("#form").effect("shake", {times:4}, 1000);
            }
        });
    } else {

        var name = document.getElementById("su_name").value;
        var email = document.getElementById("su_email").value;
        var password = document.getElementById("su_password").value;
        var password2 = document.getElementById("su_password2").value;

        var sendRequest = true;
        
        if (name.length == 0) {
            sendRequest = false;
            $("#su_name").css({"border": "solid 1px #dd4b39"});
        }
        if (email.indexOf("@") == -1) {
            sendRequest = false;
            $("#su_email").css({"border": "solid 1px #dd4b39"});
        }
        if (password.length == 0) {
            sendRequest = false;
            $("#su_password").css({"border": "solid 1px #dd4b39"});
        }
        if (password != password2) {
            sendRequest = false;
            $("#su_password2").css({"border": "solid 1px #dd4b39"});
        }

        if (sendRequest) {
            var object = {
                name: name,
                email: email,
                password: password 
            };

            $.post("createUser.php", object, function(response){
                if (response == "succes") {
                    window.location = "https://cloudpack.ml/myFiles.php";
                } else if (response == "error") {
                    $("#su_email").css({"border": "solid 1px #dd4b39"});
                    $("#form").effect("shake", {times:4}, 1000);
                }
            });
        }
    }
}

//Check if logged in
function getStarted() {
    $.get("https://cloudpack.ml/checkUserLoggedIn.php", function (response) {
        if (response == "yes") {
            window.location = "https://cloudpack.ml/myFiles.php?s=true";
        } else {
            popup("sign-up");
        }
    });
}

function toggle_form() {
    $('.form_content').animate({height: "toggle", opacity: "toggle"}, "slow");
    $('.form_helper').animate({height: "toggle", opacity: "toggle"}, "slow");
}

function enter(e, type) {
    if (e.keyCode == 13) {
        ajax(type);
    }
}
