utils = {

	formatStr: function(str, data) {
		for (var paramName in data) {
			str = str.replace(new RegExp('{'+paramName+'}', 'g'), data[paramName]);
		}
		return str;
	},

	htmlen: function(str, keepQuotes) {
		var html = str.replace(/&[^#]/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
		return keepQuotes ? html : html.replace(/"/g, '&quot;');
	},

	htmlde: function(str) {
		return str.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&amp;/g, '&');
	},

	quote: function(str) {
		return str.replace(/"/g, '&quot;');
	},

	removeHtmlTags: function(str) {
		return str.replace(/<.*?>/g, '');
	},

	lineFeedsToBr: function(str) {
		return str.replace(/\n/g, '<br/>');
	},

	brToLineFeeds: function(str) {
		return str.replace(/<br\/?>/gi, '\n');
	},

	trim: function(str) {
		return str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
	},

	bind: function(func, context /*, arg1, arg2, ... */) {
		if (arguments.length < 1) return func;
		var args = Array.prototype.slice.call(arguments, 2);
		return function() {
			return func.apply(context, args.concat(Array.prototype.slice.call(arguments)));
		};
	},

	timeToString: function(time) {
		time = time.toString();
		return time.substr(0, time.length-2) + ':' + time.substr(-2);
	},

	minutesDurationToString: function(minutesCount) {
		if (minutesCount > 60) {
			var hours = Math.floor(minutesCount / 60);
			var mins = (minutesCount - hours * 60);
			return hours + '&nbsp;часа&nbsp;' + (mins ? mins + '&nbsp;мин' : ' ');
		}
		else {
			return minutesCount + '&nbsp;мин';
		}
	},

	getDateFromDateString: function(dateString) {
		var dateAndTime = dateString.split(' ');
		var dateParts = dateAndTime[0].split('-');
		var timeParts = dateAndTime[1].split(':');
		return new Date(dateParts[0], dateParts[1]-1, dateParts[2], timeParts[0], timeParts[1], timeParts[2]);
	}

};

/*
	strProto.validateEmail = function () {
		return /^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i.test(this);
	};

	arrayToHash = function(arr) {
		var obj = {};
		for (var i = 0, m; m = arr[i]; i++) {
			obj[m] = 1;
		}
		return obj;
	};

	hashToArray = function(hash) {
		var a = [];
		for (var i in hash) {
			if (hash.hasOwnProperty(i))
				a.push(hash[i])
		}
		return a;
	};

	getKeys = function(hash) {
		var a = [];
		for (var i in hash) {
			if (hash.hasOwnProperty(i))
				a.push(i);
		}
		return a;
	};
*/
