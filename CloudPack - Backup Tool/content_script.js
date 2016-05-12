document.addEventListener("keypress", function() {
	chrome.runtime.sendMessage({action: true});
});

document.addEventListener("mousemove", function() {
  chrome.runtime.sendMessage({action: true});
});