var continue_;
var progress = 0;
var tickForEachCoursus;
var tickForEachFile;
var tickForEach5Sec;
var action = false;

//Event fired when we recieve a message:
chrome.runtime.onMessageExternal.addListener( function(request, sender, sendResponse) {
	
	//Open itslearning
	if (request.data == "open_popup") {
		chrome.tabs.create({url: "https://itslearning.com"});
		sendResponse({data: true});
	}

	//Installed?
	if (request.data == "is_installed") {
		sendResponse({data: true});
	}

	//Logged in?
	if (request.data == "is_logged_in") {
		getSubdomain( function(success) {
			if (success) {
				sendResponse({data: true});
			} else {
				sendResponse({data: false});
			}
		});
		return true;
	}

	//Download?
	if (request.data == "download") {
		continue_ = true;
		progress = 0;
		$.post("https://cloudpack.ml/removeUserFiles.php", {"dontbrowsetothisurl": true}, function () {
			getSubdomain( function(success, subdomain) {
				if (success && continue_) {
					getCourses(subdomain, function (courses) {
						if ((courses.length != 0) && continue_) {
							getCoursesItemID(courses, subdomain, function (fileStructure, foldersToResearch) {
								if (continue_) {
									getFoldersAndFiles(fileStructure, foldersToResearch, subdomain, function (fileStructure) {
										$.post("https://cloudpack.ml/done.php", {"fileStructure": btoa(JSON.stringify(fileStructure))}, function () {
											chrome.browserAction.setBadgeText({"text": ""});
										});
										chrome.browserAction.setBadgeBackgroundColor({ "color": "#00CC00"});
										chrome.browserAction.setBadgeText({"text": "Done"});
										chrome.tabs.executeScript(null, {file: "content_script.js"});
										var timer = setInterval( function () {  
											if (action) {
												clearInterval(timer);
												var opt = {
												  type: "basic",
												  title: "CloudPack",
												  message: "Your files were successfully synchronizing!",
												  iconUrl: "icon/icon128.png"
												}
												chrome.notifications.create(opt);
											}
										}, 500);
									})
								}
							});
						} else {
							console.log("You don't have any courses");
						}
					});
				}
			});
		});
	}

	//Cancel?
	if (request.data == "cancel") {
		continue_ = false;
		chrome.browserAction.setBadgeText({"text": ""});
	}
});



//Get subdomain/school
function getSubdomain(callback) {
	var httpRequests = 0;
	var httpResponses = 0;
	chrome.cookies.getAll({name: "ASP.NET_SessionId", session: true}, function(cookies) {
		if (cookies.length != 0) {
			for (i = 0; i < cookies.length; i++) {
				var domain = cookies[i].domain;
				if (domain.substring(domain.length - 16, domain.length) == ".itslearning.com") {
					(function (subdomain) {
						httpRequests++;
						$.get("https://" + subdomain + ".itslearning.com/XmlHttp/Api.aspx?Function=NetworkLatencyPing", function(response, xhr) {
							httpResponses++;
							if ((xhr.status = 200) && (response == "ok")) {
								callback(true, subdomain);
							}
							if (httpRequests == httpResponses) {
								callback(false);
							}
						});
					}(domain.replace(".itslearning.com", "")));
				}
			}
		} else {
			callback(false);
		}
	});
}



//Get all courses for subdomain/school
var getCourses = function(subdomain, callback) {
	var httpGet = function() {
		$.get("https://" + subdomain + ".itslearning.com/Course/AllCourses.aspx", function(response) {
			updateProgres(1);
			var regex = /id="__VIEWSTATE" value="(.*)" \/>/;
			var viewstate = regex.exec(response)[1];
			var regex2 = /id="__EVENTVALIDATION" value="(.*)" \/>/;
			var eventvalidation = regex2.exec(response)[1];

			var httpPost = function() {
				$.post("https://" + subdomain + ".itslearning.com/Course/AllCourses.aspx", {"__VIEWSTATE": viewstate, "__EVENTVALIDATION": eventvalidation, "__EVENTTARGET": "ctl26:7:Pagesize:1000000"}, function(response2) {
					var regex3 = /<a class="ccl-iconlink" href="(.*)" target="_top"><span>(.*)<\/span><\/a><\/td>/g;

					var courses = [];
					while (match1 = regex3.exec(response2)) {
						var id = match1[1].replace("/main.aspx?CourseID=", "");
						courses.push({courseID: id, name: match1[2]});
					}
					updateProgres(2); 
					tickForEachCoursus = (10 / courses.length);
					tickForEachFile = (88 / (courses.length * 15));  
					callback(courses);
				}).fail(function() {
				    httpPost();
				    console.log("itslearning error (#3)");
				});
			};
			httpPost();
		}).fail(function() {
		    httpGet();
		    console.log("itslearning error (#2)");
		});
	};
	httpGet();
};



//Get itemID for each courses
var getCoursesItemID = function(courses, subdomain, callback) {
  
	var fileStructure = {};
	var foldersToResearch = [];

	var coursesIndex = 0;
	var coursesDone = 0;

	var loop = function () {
		if (coursesIndex != courses.length) {
			(function (i) {
				coursesIndex++;
				updateProgres(tickForEachCoursus);
				fileStructure[courses[i].courseID] = {"name": courses[i].name, "courseID": courses[i].courseID, "fileStructure": {}};

				var httpGet = function() {
					$.get("https://" + subdomain + ".itslearning.com/ContentArea/ContentArea.aspx?LocationID=" + courses[i].courseID + "&LocationType=1", function (response) {
						coursesDone++;
						var regex = /alt=''>Link<\/a><\/li><li id='(.*)' class=' folder/;
						var courseItemID = regex.exec(response)[1];

						foldersToResearch.push({"courseID": courses[i].courseID, "itemID": courseItemID, "path": courses[i].courseID + ".fileStructure"});
						loop();
					}).fail(function() {
					    httpGet();
					    console.log("itslearning error (#4)");
					});
				}
				httpGet();
			})(coursesIndex);
		} else {
			if (coursesDone == courses.length) {
				callback(fileStructure, foldersToResearch);
			}
		}
	};

	for (i = 0; i < 10; i++) {
		loop();
	}
};








var getFoldersAndFiles = function(fileStructure, foldersToResearch, subdomain, callback) {
	
	var folderIndex = 0;
	var runningInstances = 15;
	var searchForEmpty = [];

	var loop = function() {
		if (continue_) {
			(function (i) {
				folderIndex++;
				var getFilesInFolder = function () {
					$.get("https://" + subdomain + ".itslearning.com/ContentArea/ContentArea.aspx?LocationID=" + foldersToResearch[i].courseID + "&LocationType=1&ElementId=0&id=item" + foldersToResearch[i].itemID, function (response) {

						//Add folders to "foldersToResearch"
						var containFolders = false;
						var regex = /<a href='https:\/\/[a-zA-Z0-9]+.itslearning.com\/Folder\/processfolder.aspx\?FolderID=([0-9]+)' target='mainmenu'><img.*?alt=''>(.*?)<\/a><\/li>/g;
						while (match = regex.exec(response)) {
							containFolders = true;
							updateObject(fileStructure, foldersToResearch[i].path + "." + match[1], {"name": match[2], "type": "folder", "elements": {}});
							foldersToResearch.push({"courseID": foldersToResearch[i].courseID, "itemID": match[1], "path": foldersToResearch[i].path + "." + match[1] + ".elements"});
						}


						//Handle files
						var filesRequested = 0;
						var filesDone = 0;

						var regex2 = /<a href='https:\/\/[a-zA-Z0-9]+.itslearning.com\/File\/fs_folderfile.aspx\?FolderFileID=([0-9]+)'/g;
						while (match = regex2.exec(response)) {
							filesRequested++;
							var httpGet = function() {
								$.get("https://" + subdomain + ".itslearning.com/File/fs_folderfile.aspx?FolderFileID=" + match[1], function (response2) {
									var regex2 = /\/file\/download.aspx\?FileID=([0-9]+)/;
									var fileID = regex2.exec(response2)[1];
									var url = "https://" + subdomain + ".itslearning.com/file/download.aspx?FileID=" + fileID;
									
									var sendDownloadUrl = function() {
										getFileUrl(url, "HEAD", function (name, mime, downloadUrl) {
											$.post("https://cloudpack.ml/createDownloadScript.php", {url: btoa(downloadUrl), fileID: fileID}, function (response3) {
												if (response3 == "ok") {
													updateProgres(tickForEachFile);
													updateObject(fileStructure, foldersToResearch[i].path + "." + fileID, {"name": name, "type": "file", "fileID": fileID, "mime": mime});
													filesDone++;
													if ((filesRequested == filesDone) && (assignmentsRequested == assignmentsDone)) {
														startOver();
													}
												}
											}).fail(function() {
											    sendDownloadUrl();
												console.log("Url timeout (#1)");
											});
										});
									};
									sendDownloadUrl();
								}).fail(function() {
								    httpGet();
								    console.log("itslearning error (#6)");
								});
							}
							httpGet();
						}


						//Handle assignments
						var assignmentsRequested = 0;
					  	var assignmentsDone = 0;
					  	var added = false;

					  	var regex3 = /<a href='https:\/\/[a-zA-Z0-9]+.itslearning.com\/essay\/read_essay.aspx\?EssayID=([0-9]+)' target='mainmenu'><img.*?alt=''>(.*?)<\/a><\/li>/g;

					  	while (match = regex3.exec(response)) {
					  		(function (fileID, name) {
					  			assignmentsRequested++;
					  			updateObject(fileStructure, foldersToResearch[i].path + "." + fileID, {"name": name, "type": "assignment", "elements": {}});
					  			var httpGet = function() {
						  			$.get("https://" + subdomain + ".itslearning.com/essay/read_essay.aspx?EssayID=" + fileID, function (response2) {
						  				var filesRequested2 = 0;
										var filesDone2 = 0;
										var fileNumber = 0;

						  				var regex4 = /fileRepoId=(.*?)" target="_blank">.*?<span>(.*?)<\/span>/g;

						  				while (match2 = regex4.exec(response2)) {
						  					(function (fileNumber2, match3) {
						  						filesRequested2++;
						  						var sendDownloadUrl = function() {
						  							getFileUrl("https://" + subdomain + ".itslearning.com/essay/Proxy/DownloadRedirect.ashx?fileRepoId=" + match3[1].replace(/amp;/g,''), "GET", function(name, mime, downloadUrl) {
								  						$.post("https://cloudpack.ml/createDownloadScript.php", {url: btoa(downloadUrl), fileID: (fileID + "_" + fileNumber2)}, function (response3) {
															if (response3 == "ok") {
																updateProgres(tickForEachFile);
																filesDone2++;
																updateObject(fileStructure, foldersToResearch[i].path + "." + fileID + ".elements." + fileID + "_" + fileNumber2, {"name": name, "type": "file", "fileID": fileID + "_" + fileNumber2, "mime": mime});
										  						
										  						if (filesRequested2 == filesDone2) {
											  						assignmentsDone++;
											  					}
											  					if ((filesRequested == filesDone) && (assignmentsRequested == assignmentsDone)) {
																	startOver();
																}
															}
														}).fail(function() {
														    sendDownloadUrl();
															console.log("Url timeout (#2)");
														});
								  					});
						  						};
							  					sendDownloadUrl();
						  					})(fileNumber, match2);
						  					
						  					fileNumber++;
						  				}
						  				if (filesRequested2 == 0) {
						  					assignmentsDone++;

						  					deleteObject(fileStructure, foldersToResearch[i].path + "." + fileID);
						  					var path = foldersToResearch[i].path + "." + fileID;
											var split = path.split(".");
											if (split > 2) {
												searchForEmpty.push({"path": foldersToResearch[i].path});
											}
											
						  					if ((filesRequested == filesDone) && (assignmentsRequested == assignmentsDone)) {
												startOver();
											}
						  				}
						  			}).fail(function() {
									    httpGet();
									    console.log("itslearning error (#7)");
									});
						  		}
						  		httpGet();
					  		})(match[1], match[2]);
					  	}

						var startOver = function () {
							if (foldersToResearch.length != folderIndex) {
								loop();
							} else {
								runningInstances--;
								console.log("One man down!");
							}

							if (runningInstances == 0) {
								for (var i = 0; i < searchForEmpty.length; i++) {
									if (isEmpty(fileStructure, searchForEmpty[i].path)) {
										var path = searchForEmpty[i].path;
										var split = path.split(".");
										var deleteName = path.substring(0, path.length - split[split.length - 1].length - 1);
										deleteObject(fileStructure, deleteName);
										if (split > 2) {
											var shoterPath = path.substring(0, path.length - split[split.length - 1].length - split[split.length - 2].length - 2);
											searchForEmpty.push({"path": shoterPath});
										}
									}
								}	

								callback(fileStructure);
							}
						}

						if ((filesRequested == 0) && (assignmentsRequested == 0)) {
							if (!containFolders) {
								var path = foldersToResearch[i].path;
								var split = path.split(".");
								var deleteName = path.substring(0, path.length - split[split.length - 1].length - 1);
								deleteObject(fileStructure, deleteName);
								if (split.length > 2) {
									var shortPath = path.substring(0, path.length - split[split.length - 1].length - split[split.length - 2].length - 2);
									searchForEmpty.push({"path": shortPath});
								}
							}
							startOver();
						}
					}).fail(function() {
					    getFilesInFolder();
					    console.log("itslearning error (#5)");
					});
				}
				getFilesInFolder();
			})(folderIndex);
		}
	};

	for (i = 0; i < runningInstances; i++) {
	  loop();
	}
};




function getFileUrl(url, method, callback) {
	var httpRequest = function () {
		var xhr = new XMLHttpRequest();
		xhr.open(method, url);
		xhr.onreadystatechange = function() {
			if (xhr.readyState == 4) {
				if (xhr.status == 200) {
					var regex = /filename=\"(.*)\"/;
					var name = regex.exec(xhr.getResponseHeader("Content-disposition"))[1];
					var split = name.split(".");
					var mime = "." + split[split.length - 1];
					name = name.replace(mime, "");
					callback(name, mime, xhr.responseURL);
				} else {
					httpRequest();
					console.log("itslearning error (#10)");
				}
			}
		}
		xhr.send();
	}
	httpRequest();
}


function updateObject(object, path, value){
	var stack = path.split('.');
	while(stack.length>1){
		object = object[stack.shift()];
	}
	object[stack.shift()] = value;
}


function deleteObject(object, path){
	var stack = path.split('.');
	while(stack.length>1){
		object = object[stack.shift()];
	}
	delete object[stack.shift()];
}


function isEmpty(object, path){
	var stack = path.split('.');
	while(stack.length>1){
		object = object[stack.shift()];
	}
	if (Object.keys(object[stack.shift()]).length == 0) {
		return true;
	} else {
		return false;
	}
}


function updateProgres(increasement) {
	progress += increasement;
	if ((progress < 100) && continue_) {	
		var text = Math.floor(progress) + " %";
		chrome.browserAction.setBadgeBackgroundColor({ "color": "#00CC00"});
		chrome.browserAction.setBadgeText({"text": text});
	}
}



//Event which fires when popup button is fired
chrome.browserAction.onClicked.addListener(function() {
	$.get("https://cloudpack.ml/checkUserLoggedIn.php", function (response) {
		var url;
		if (response == "yes") {
			url = "https://cloudpack.ml/myFiles.php";
		} else {
			url = "https://cloudpack.ml";
		}
		chrome.tabs.create({url: url});
	});
});


chrome.runtime.onMessage.addListener( function(message) {
	if (message.action == true) {
		action = true;
	}
});


chrome.runtime.onStartup.addListener(function () {
	chrome.browserAction.setBadgeText({"text": ""});
});