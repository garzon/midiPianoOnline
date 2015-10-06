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

(function() {
    var list = {
        0: 0,
        1: 1,
        2: 2,
        3: 3,
        4: 4,
        5: 5,
        6: 6,
        7: 7,
        8: 8,
        9: 9,
        10: 'a',
        11: 'b',
        12: 'c',
        13: 'd',
        14: 'e',
        15: 'f'
    };
    window.hexEncode = function(str) {
        var ret = '';
        for (var i in str) {
            var code = str.charCodeAt(i);
            ret += list[(code & 0xF0) >> 4];
            ret += list[code & 0xF];
        }
        return ret;
    };
})();

// set up jquery ui components
$document.ready(function() {
    $(".dialog").each(function() {
        var $this = $(this);
        $this.dialog({
            title: $this.data('title')
        });
    });
});

(function() {
    var list = {0: 'C', 1:'Db', 2:'D', 3: 'Eb', 4: 'E', 5: 'F', 6: 'Gb', 7: 'G', 8: 'Ab', 9: 'A', 10: 'Bb', 11: 'B'};
    window.noteToName = function(note) {
        var octave = Math.floor(note / 12) - 1;
        var name = list[note % 12];
        return name + octave;
    };
})();


// utils
$document = $(document);

$document.ready(function() {
    buildNewQuery = function(baseUrl, propName, propVal) {
        var sym = baseUrl.indexOf('?') == -1 ? '?' : '&';
        var url = baseUrl.replace(new RegExp(propName + '=[^&]*'), '').replace(/#.*/, '');
        return (url + sym + encodeURI(propName) + '=' + encodeURI(propVal)).replace('&&', '&').replace('?&', '?');
    };

    buildNewQueryArray = function(baseUrl, props) {
        for (var i in props) {
            baseUrl = buildNewQuery(baseUrl, i, props[i]);
        }
        return baseUrl;
    };

    buildNewUrlQuery = function(propName, propVal) {
        var baseUrl = window.location.href;
        return buildNewQuery(baseUrl, propName, propVal);
    };

    buildNewUrlQueryArray = function(props) {
        var baseUrl = window.location.href;
        return buildNewQueryArray(baseUrl, props);
    };
});

// upload component
$document.on('change', '.btn-file :file', function() {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
});
$document.ready(function() {
    $('.btn-file :file').on('fileselect', function(event, numFiles, label) {
        $(this).siblings("span").get(0).innerText = label;
    });
});

// category component
$document.ready(function() {
    var $position = $(".form-category-selector");
    $(".fabu-form-category").on('click', function(e) {
        e.preventDefault();
        var $this = $(this);
        var text = $this.text();
        $position.val(text);
        $this.parent().parent().siblings('button').html(text + ' <span class="caret"></span>');
    });
});

// add red stars on required fields
$document.ready(function() {
    $(".form-group").each(function() {
        var $this = $(this);
        var $children_req = $this.find("[required]");
        if ($children_req.length != 0) {
            var arr = $this.find("label").get();
            for(var i in arr) {
                arr[i].innerHTML = '<span class="red">*</span>' + arr[i].innerHTML;
            }
        }
    });
});

// pager
$document.ready(function() {
    $(".btn-pager").on('click', function(e) {
        e.preventDefault();
        window.location = buildNewUrlQuery('page', $(this).data('page'));
    });
});

$document.ready(function() {
    var nav_height = $(".navbar-bottom").height();
    $($(".main-block")[0]).css({minHeight: innerHeight - nav_height - 102 + "px"});
});
