function MidiKeyBoard($insertPoint) {
    var isBasicBlackKey = {
        0: false, 1: true, 2: false, 3: true, 4: false,
        5: false, 6: true, 7: false, 8: true, 9: false, 10: true, 11:false
    };
    var keyArray = {};
    var barArray = {};

    var indexBar = function(channelId, note, absoluteTime) {
        return channelId + '-' + note + '-' + absoluteTime;
    };

    var key_dbound = 21, key_ubound = 108;
    var whitekey_width, blackkey_width;

    var screen_time = 3;

    var refreshBarView = function() {
        $(".piano-bar").remove();
        barArray = [];
    };

    var generateBar = function(channelId, note, realAbsoluteTime, realDuringTime, realNowTime) {
        var index = indexBar(channelId, note, realAbsoluteTime);
        if(barArray[index]) return;

        if(realDuringTime <= 0) realDuringTime = 0.01;  // a short bar

        var $ele = $("<div/>");
        barArray[index] = $ele;
        var $key = keyArray[note];

        var screen_path = getPosition($insertPoint.get(0)).top;
        var velocity = screen_path / screen_time;
        var height = Math.round(realDuringTime * velocity);

        var toTopTime = realAbsoluteTime-screen_time-realNowTime+realDuringTime;
        var deleteTime = (realAbsoluteTime+realDuringTime-realNowTime)*1000;

        $ele.css({
            width: (isBlackKey(note) ? blackkey_width : whitekey_width) + "%",
            left: (getPosition($key.get(0)).left * 100 / innerWidth) + '%',
            height: height + 'px',
            top: Math.round(-toTopTime*velocity) + 'px'
        }).addClass("piano-bar").animate({
            top: screen_path + 'px'
        }, deleteTime, 'linear');

        $ele.insertBefore($insertPoint);

        window.setTimeout(function() {
            if(barArray[index] && barArray[index].get(0) == $ele.get(0))
                barArray[index] = undefined;
            $ele.remove();
        }, deleteTime);
    };

    var pressKey = function(note, autoRelease) {
        var $ele = keyArray[note];
        if($ele) $ele.addClass("piano-keyboard-key-pressed");
        if(autoRelease) {
            window.setTimeout(function() {
                releaseKey(note);
            }, 50);
        }
    };

    var releaseKey = function(note) {
        var $ele = keyArray[note];
        if($ele) $ele.removeClass("piano-keyboard-key-pressed");
    };

    var isBlackKey = function(id) {
        return isBasicBlackKey[id%12];
    };

    var render = function() {
        var whiteKeyNum = 0;
        for(var keyId = key_dbound; keyId <= key_ubound; keyId++)
            if (!isBlackKey(keyId)) whiteKeyNum++;
        whitekey_width = 98.0 / whiteKeyNum;
        blackkey_width = whitekey_width*0.6;
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

    window.addEventListener('resize', refreshBarView);

    return {
        render: render,
        generateBar: generateBar,
        refreshBarView: refreshBarView,
        screen_time: screen_time,
        pressKey: pressKey,
        releaseKey: releaseKey,
        getKeyElement: function(keyId) { return keyArray[keyId]; }
    };
}