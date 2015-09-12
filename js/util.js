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