
function logTimeSince(date) {

	var currentDate = new Date();
	var tsDiff = currentDate - date;
	var tsSec = tsDiff / 1000;

	if(tsSec > 60) {
		var min = Math.floor(tsSec/60);
		var sec = Math.floor(tsSec%60);
		console.log(min + ' min ' + sec + ' s');
	}
	else {
		var diff = Math.round(tsSec * 100)/100;
		console.log(diff + ' s');
	}
}

function timeSince(date) {

	var currentDate = new Date();
	var tsDiff = currentDate - date;
	var tsSec = tsDiff / 1000;

	if(tsSec > 60) {
		var min = Math.floor(tsSec/60);
		var sec = Math.floor(tsSec%60);
		return min + ' min ' + sec + ' s';
	}
	else {
		var diff = Math.round(tsSec);
		return diff + ' s';
	}
}

function timeRemaining(date, percent) {

	if(percent == 0) { return ''; }

	var currentDate = new Date();
	var tsDiff = currentDate - date;
	var ratio = (100 - percent) / (percent);
	var tsRemaining = tsDiff*ratio;
	var tsSec = tsRemaining / 1000;

	if(tsSec > 60) {
		var min = Math.floor(tsSec/60);
		var sec = Math.floor(tsSec%60);
		return min + ' min ' + sec + ' s';
	}
	else if(tsSec > 10) {
		var diff = Math.floor(tsSec);
		return diff + ' s';
	}
	else {
		var diff = Math.round(tsSec*100)/100;
		return diff + ' s';
	}

}