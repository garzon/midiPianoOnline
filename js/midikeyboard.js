function MidiKeyBoard($insertPoint) {
    var isBasicBlackKey = {
        0: false, 1: true, 2: false, 3: true, 4: false,
        5: false, 6: true, 7: false, 8: true, 9: false, 10: true, 11:false
    };
    var keyArray = {};

    var key_dbound = 21, key_ubound = 108;
    var whitekey_width, blackkey_width;

    var pressKey = function(id) {

    };

    var releaseKey = function(id) {

    };

    var isBlackKey = function(id) {
        return isBasicBlackKey[id%12];
    };

    var render = function() {
        var whiteKeyNum = 0;
        for(var keyId = key_dbound; keyId <= key_ubound; keyId++)
            if (!isBlackKey(keyId)) whiteKeyNum++;
        whitekey_width = 98.0 / whiteKeyNum;
        blackkey_width = whitekey_width*0.8;
        for(var keyId = key_dbound; keyId <= key_ubound; keyId++) {
            var $keyDiv = $("<div/>").addClass('piano-keyboard-key');
            var basicCss = {};
            if (isBlackKey(keyId)) {
                basicCss['width'] = blackkey_width + "%";
                $keyDiv.addClass('piano-keyboard-blackkey');
                if (keyId != key_dbound)
                    basicCss['marginLeft'] = -blackkey_width/2 + '%';
            } else {
                basicCss['width'] = whitekey_width + "%";
                $keyDiv.addClass('piano-keyboard-whitekey');
                if (keyId != key_dbound) {
                    if (isBlackKey(keyId-1))
                        basicCss['marginLeft'] = -blackkey_width/2 + '%';
                    else
                        basicCss['marginLeft'] = 0;
                }
            }
            $keyDiv.css(basicCss).appendTo($insertPoint);
            keyArray[keyId] = $keyDiv;
        }
    };

    return {
        render: render,
        getWhiteKeyWidth: function() { return whitekey_width; },
        getBlackKeyWidth: function() { return blackkey_width; },
        getKeyElement:    function(keyId) { return keyArray[keyId]; }
    };
}