$(document).ready(function(){
	var name = document.getElementById("name").innerHTML;

	$("#name").hover(function(){
        document.getElementById("name").innerHTML = "Log Out";
        $("#cloudpack").css({"padding-right": 800 - document.getElementById('name').offsetWidth});
        }, function(){
        document.getElementById("name").innerHTML = name;
        $("#cloudpack").css({"padding-right": 800 - document.getElementById('name').offsetWidth});
    });



    var url = window.location.href;
    if (url.indexOf("s=true") > -1) {
        syncPopup();
    }


    $.get("https://cloudpack.ml/getFileStructure.php", function(response) {
        if (response != "empty") {
            $("#icon_tool_delete").css({"display": "inline-block"});
            $("#icon_tool_download").css({"display": "inline-block"});

            var data = response;

            //File View
            data = data.replace(/&#198;/g, "Æ");
            data = data.replace(/&#230;/g, "æ");
            data = data.replace(/&#216;/g, "Ø");
            data = data.replace(/&#248;/g, "ø");
            data = data.replace(/&#197;/g, "Å");
            data = data.replace(/&#229;/g, "å");

            data = JSON.parse(data);

            var list = [];

            for (var i in data) {
                list.push(data[i].name + "_" + data[i].courseID);
            }
            list.sort();


            var DOM = document.createElement("ul");
            for (var i in list) {
                var course = list[i].toString().split("_");
                var courseID = course[course.length - 1];

                //Create html elements that we need
                var li = document.createElement("li");
                var div = document.createElement("div");
                var img = document.createElement("img");
                var a = document.createElement("a");
                var ul = document.createElement("ul");

                //Add stuff to elements
                var randomNumber = random();
                div.className = "course fv_name";
                div.id = randomNumber;
                img.className = "fv_icon";
                img.src = "images/folder.png";
                a.className = "fv_name_a";
                a.innerText = data[courseID].name;
                ul.className = "fv_items";
                ul.id = randomNumber + "_items";

                //Append
                div.appendChild(img);
                div.appendChild(a);
                li.appendChild(div);
                li.appendChild(ul); 


                function search(obj, html) {
                    Object.keys(obj).forEach(function(b){
                        if (obj[b].type == "folder" || obj[b].type == "assignment") {

                            //Create html elements that we need
                            var li = document.createElement("li");
                            var div = document.createElement("div");
                            var img = document.createElement("img");
                            var a = document.createElement("a");
                            var ul = document.createElement("ul");

                            //Add stuff to elements
                            var randomNumber = random();
                            div.className = "fv_name";
                            div.id = randomNumber;
                            img.className = "fv_icon";
                            if (obj[b].type == "folder") {
                                img.src = "images/folder.png";
                            } else {
                                img.src = "images/assignment.png";
                            }
                            a.className = "fv_name_a";
                            a.innerText = obj[b].name;
                            ul.className = "fv_items";
                            ul.id = randomNumber + "_items";

                            //Append
                            div.appendChild(img);
                            div.appendChild(a);
                            li.appendChild(div);
                            li.appendChild(ul);
                            html.appendChild(li);
                            
                            search(obj[b].elements, ul);
                        } else {

                            //Create html elements that we need
                            var li = document.createElement("li");
                            var div = document.createElement("div");
                            var img = document.createElement("img");
                            var a = document.createElement("a");

                            //Add stuff to elements
                            div.className = "fv_name download_file";
                            div.id = obj[b].fileID;
                            img.className = "fv_icon";
                            img.src = "images/file.png";
                            a.className = "fv_name_a";
                            a.innerText = obj[b].name + obj[b].mime;

                            //Append
                            div.appendChild(img);
                            div.appendChild(a);
                            li.appendChild(div);
                            html.appendChild(li);
                        }
                    });
                }
                search(data[courseID].fileStructure, ul);
                DOM.appendChild(li);                
            }
            document.getElementById("file_view").appendChild(DOM);

            $(".fv_name").click(function(){
                $("#" + this.id + "_items").animate({height: "toggle", opacity: "toggle"}, "fast");
            });

            $(".download_file").click(function(){
                window.location = "https://cloudpack.ml/userDownload.php?fileID=" + this.id;        
            });
        } else {
            $("#file_view_div").css({"height": "700px"});
        }
    });
});


$(window).load(function () {
	$("#navigation_bar").css({"display": "flex"});
    $("#cloudpack").css({"padding-right": 800 - document.getElementById('name').offsetWidth});
});


function deleteFiles() {
    if (confirm("Are you sure that you want to delete this backup from itslearning?")) {
        $.post("https://cloudpack.ml/removeUserFiles.php", {"dontbrowsetothisurl": true}, function () {
            window.location = "https://cloudpack.ml/myFiles.php";
        });
    }
}


function syncPopup() {
    $("#step1").css({"display": "block"});
    $("#step2").css({"display": "none"});
    $("#step3").css({"display": "none"});
    $("#step4").css({"display": "none"});

    $("#popup").bPopup({
        fadeSpeed: "fast",
        opacity: 0.9,
        followSpeed: 0,
        modalColor: "black"
    });
}


var chromeExtensionId = "oghfigglcjjieaafpiholdhmhokgibmh";

function syncGetStarted() {
    chrome.runtime.sendMessage(chromeExtensionId, {data: "is_installed"}, function(response) {
        if ((response) && (response.data)) {
            chrome.runtime.sendMessage(chromeExtensionId, {data: "is_logged_in"}, function(response2) {
                if (response2.data) {
                    $("#step1").hide("slow");
                    $("#step4").css({"display": "block"});
                } else {
                    $("#step1").hide("slow");
                    $("#step3").css({"display": "block"});
                    startNewWindowTimer();
                }
            });
        } else {
            $("#step1").hide("slow");
            $("#step2").css({"display": "block"});
        }
    });
}


function startNewWindowTimer() {
    myVar = setTimeout(function(){
        if (document.hasFocus()) {
            chrome.runtime.sendMessage(chromeExtensionId, {data: "open_popup"});
        }
    }, 2000);
}


function checkItslearning() {
    chrome.runtime.sendMessage(chromeExtensionId, {data: "is_logged_in"}, function(response) {
        if (response.data) {
            $("#step3").hide("slow");
            $("#step4").css({"display": "block"});
        } else {
            alert("Please login on Itslearning")
        }
    });
}


function backupNow() {
    chrome.runtime.sendMessage(chromeExtensionId, {data: "download"});
    window.location = "https://cloudpack.ml/myFiles.php";
}


function random() {
    var output = "";
    var values = "abcdefghijklmnopqrstuvwxyz0123456789";

    for (var i = 0; i < 15; i++) {
        output = output + values.charAt(Math.floor(Math.random() * values.length));
    }
    return output;
}
