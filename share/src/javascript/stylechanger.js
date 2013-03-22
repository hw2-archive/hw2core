/*global window, localStorage, fontSizeTitle, bigger, reset, smaller, biggerTitle, resetTitle, smallerTitle, Cookie */
var prefsLoaded = false;
var defaultFontSize = 100;
var currentFontSize = defaultFontSize;
var fontSizeTitle;
var bigger;
var smaller;
var reset;
var biggerTitle;
var smallerTitle;
var resetTitle;
var bgEnabled = true;
/*
Object.append(Browser.Features, {
	localstorage: (function() {
		return ('localStorage' in window) && window.localStorage !== null;
	})()
});
*/
function switchBgStretcher(enabled,basePath) {
	if (enabled==true) {
        //  Initialize Backgound Stretcher	   
		jQuery('#hw2-bg').bgStretcher({
			images: [basePath+'/images/Bottom_texture.jpg'],
			imageWidth: 1352, 
			imageHeight: 830, 
			slideDirection: 'N',
			nextSlideDelay: 30000,
            resizeProportionally: true,
            slideShowSpeed: "slow",
			transitionEffect: 'fade',
			sequenceMode: 'normal',
			buttonPrev: '#prev',
			buttonNext: '#next',
			pagination: '#nav',
			anchoring: 'left center',
			anchoringImg: 'left center'
		});
	} else {
		jQuery('#hw2-main').bgStretcher.sliderDestroy();
	}
}

function writeBgSwitch(value) {
	if (Browser.Features.localstorage) {
		localStorage.bgEnabled = value;
	} else {
		Cookie.write("bgEnabled", value, {duration: 180});
	}
}

function readBgSwitch() {
	if (Browser.Features.localstorage) {
		return localStorage.bgEnabled;
	} else {
		return Cookie.read("bgEnabled");
	}
}

function hw2SwitchBg(tmplurl)
{
	if (bgEnabled) {
		document.body.style.backgroundImage='none';
		switchBgStretcher(false,tmplurl);
		bgEnabled=false;
	} else {
		document.body.style.backgroundImage=tmplurl+'/images/Bottom_texture.jpg';
		switchBgStretcher(true,tmplurl);	
		bgEnabled=true;
	}

	hw2BtnState();
}

function hw2BtnState() {
	var element=document.getElementById("bg_btn");
	var spans=document.getElementById("bg_switch").getElementsByTagName("span");
	if (bgEnabled) {
		element.setAttribute("value", "bg on");
	} else {
		element.setAttribute("value", "bg off");
	}
}

/*

function setFontSize(fontSize) {
	document.body.style.fontSize = fontSize + '%';
}

function changeFontSize(sizeDifference) {
	currentFontSize = parseInt(currentFontSize, 10) + parseInt(sizeDifference * 5, 10);
	if (currentFontSize > 180) {
		currentFontSize = 180;
	} else if (currentFontSize < 60) {
		currentFontSize = 60;
	}
	setFontSize(currentFontSize);
}

function revertStyles() {
	currentFontSize = defaultFontSize;
	changeFontSize(0);
}

function writeFontSize(value) {
	if (Browser.Features.localstorage) {
		localStorage.fontSize = value;
	} else {
		Cookie.write("fontSize", value, {duration: 180});
	}
}

function readFontSize() {
	if (Browser.Features.localstorage) {
		return localStorage.fontSize;
	} else {
		return Cookie.read("fontSize");
	}
}

function setUserOptions() {
	if (!prefsLoaded) {
		var size = readFontSize();
		currentFontSize = size ? size : defaultFontSize;
		setFontSize(currentFontSize);
		prefsLoaded = true;
	}
}

function addControls() {
	var container = document.id('fontsize');
	var content = '<h3>'+ fontSizeTitle +'</h3><p><a title="'+ biggerTitle +'"  href="#" onclick="changeFontSize(2); return false">'+ bigger +'</a><span class="unseen">.</span><a href="#" title="'+resetTitle+'" onclick="revertStyles(); return false">'+ reset +'</a><span class="unseen">.</span><a href="#"  title="'+ smallerTitle +'" onclick="changeFontSize(-2); return false">'+ smaller +'</a></p>';
	container.set('html', content);
}

function saveSettings() {
	writeFontSize(currentFontSize);
}

window.addEvent('domready', setUserOptions);
window.addEvent('domready', addControls);
window.addEvent('unload', saveSettings);

*/