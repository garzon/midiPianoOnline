$document = $(document);

function getPosition(element) {
    var left = 0, top = 0;
    while(element !== null) {
        left += element.offsetLeft;
        top += element.offsetTop;
        element = element.offsetParent;
    }
    return {
        left: left,
        top:  top
    };
}

function loadRemoteBinary(path, callback) {
    var fetch = new XMLHttpRequest();
    fetch.open('GET', path);
    fetch.overrideMimeType("text/plain; charset=x-user-defined");
    fetch.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200) {
            var data = this.responseText || "" ;
            callback(string2binary(data));
        }
    };
    fetch.send();
}

function string2binary(str) {
    var ret = [];
    for(var i=0; i<str.length; i++) {
        ret = ret.concat(String.fromCharCode(str.charCodeAt(i) & 0xff));
    }
    return ret.join('');
}

function frequencyFromNoteNumber(note) {
    return 440 * Math.pow(2,(note-69)/12);
}

function mySetTimeout(callback, countdown) {
    if(countdown < 30) {
        window.setTimeout(callback, countdown);
        return;
    }
    countdown = countdown/2;
    var originalTime = new Date().getTime() + countdown;
    var realCallback = function() {
        var msDelay = new Date().getTime() - originalTime;
        window.setTimeout(callback, countdown-msDelay);
    };
    window.setTimeout(realCallback, countdown);
}

function New(f, callback) {
    return function () {
        var n = { '__proto__': f.prototype };
        if(typeof callback == 'function') callback.apply(n);
        f.apply(n, arguments);
        return n;
    };
}

function debouncer(func, timeout) {
    var timeoutID;
    timeout = timeout || 200;
    return function () {
        var scope = this , args = arguments;
        clearTimeout(timeoutID);
        timeoutID = setTimeout(function() {
            func.apply(scope, Array.prototype.slice.call(args));
        }, timeout);
    };
}

function inRange(item, dbound, ubound) {
    return dbound <= item && item <= ubound;
}

function binarySearch(arr, dbound, ubound, rangeDBound, rangeUBound) {
    if (rangeUBound - rangeDBound === 1) {
        if (inRange(arr[rangeDBound], dbound, ubound)) return rangeDBound;
        if (inRange(arr[rangeUBound], dbound, ubound)) return rangeUBound;
        return -1;
    }
    if (rangeDBound === rangeUBound) {
        if (inRange(arr[rangeDBound], dbound, ubound)) return rangeDBound;
        return -1;
    }
    var m = Math.floor((rangeUBound + rangeDBound) / 2);
    if (inRange(arr[m], dbound, ubound)) {
        var idx = binarySearch(arr, dbound, ubound, rangeDBound, m-1);
        if (idx === -1) return m; else return idx;
    }
    if (arr[m] < dbound) return binarySearch(arr, dbound, ubound, m+1, rangeUBound);
    return binarySearch(arr, dbound, ubound, rangeDBound, m-1);
}

function asciiArray2Binary(arr) {
    var ret = '';
    for(var i in arr) ret += String.fromCharCode(arr[i]);
    return string2binary(ret);
}