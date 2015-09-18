define(function() {
    function MidiView($insertPoint) {

        var $this;

        var isBasicBlackKey = {
            0: false, 1: true, 2: false, 3: true, 4: false,
            5: false, 6: true, 7: false, 8: true, 9: false, 10: true, 11: false
        };
        var keyArray = {};
        var barArray = {};

        var key_dbound = 21, key_ubound = 108, screen_path, screen_time = 3;
        var whitekey_width, blackkey_width;

        var refreshBarView = function () {
            $(".piano-bar").remove();
            $(".piano-keyboard-key-pressed").removeClass("piano-keyboard-key-pressed");
            barArray = [];
        };

        var standard_tempo = tempo = 120;
        var ticksPerBeat = 480;
        function setTempo(newTempo) {
            tempo = newTempo;
            refreshBarView();
        }

        function setTicksPerBeat(newTicksPerBeat) {
            ticksPerBeat = newTicksPerBeat;
        }

        var generateBar = function(channelId, note, absoluteTicks, lastTime, nowTick, barId, isPause) {
            if (nowTick >= absoluteTicks + lastTime) return;
            var index = barId;
            if (barArray[index]) return;

            var $ele = $("<div/>").data('channel', channelId).data('note', note).data('absoluteTicks', absoluteTicks).data('barId', barId);
            barArray[index] = $ele;

            if (lastTime <= 0) lastTime = 10;  // a "short open" bar

            var $key = keyArray[note];

            screen_path = getPosition($key.get(0)).top;
            var velocity = screen_path / screen_time * tempo / standard_tempo;
            var ticksToLenCoeff = 60 * velocity / tempo / ticksPerBeat;
            var height = ticksToLenCoeff * lastTime;

            var toTopTick = absoluteTicks + lastTime - nowTick - screen_path / ticksToLenCoeff;
            var top = - toTopTick * ticksToLenCoeff;
            var deleteTime = (toTopTick * ticksToLenCoeff + screen_path) / velocity;

            deleteTime = deleteTime * 1000;

            $ele.css({
                width: (isBlackKey(note) ? blackkey_width : whitekey_width) + "%",
                left: (getPosition($key.get(0)).left * 100 / innerWidth) + '%',
                height: height + 'px',
                top: top + 'px'
            }).addClass("piano-bar");

            $ele.insertBefore($insertPoint);

            if(!isPause) {
                $ele.animate({
                    top: screen_path + 'px'
                }, deleteTime, 'linear');
                window.mySetTimeout(function () {
                    if (barArray[index] && barArray[index].get(0) == $ele.get(0))
                        barArray[index] = undefined;
                    $ele.remove();
                }, deleteTime);
            }
        };

        var pressKey = function (note, autoRelease) {
            var $ele = keyArray[note];
            if ($ele) $ele.addClass("piano-keyboard-key-pressed");
            if (autoRelease) {
                window.setTimeout(function () {
                    releaseKey(note);
                }, 50);
            }
        };

        var releaseKey = function (note) {
            var $ele = keyArray[note];
            if ($ele) $ele.removeClass("piano-keyboard-key-pressed");
        };

        var isBlackKey = function (id) {
            return isBasicBlackKey[id % 12];
        };

        var render = function () {
            var whiteKeyNum = 0;
            for(var keyId = key_dbound; keyId <= key_ubound; keyId++)
                if(!isBlackKey(keyId)) whiteKeyNum++;
            whitekey_width = 98.0 / whiteKeyNum;
            blackkey_width = whitekey_width * 0.6;
            for(var keyId = key_dbound; keyId <= key_ubound; keyId++) {
                var $keyDiv = $("<div/>").addClass('piano-keyboard-key').data('note', keyId).data('pressed', false);
                var basicCss = {};
                if(isBlackKey(keyId)) {
                    basicCss['width'] = blackkey_width + "%";
                    $keyDiv.addClass('piano-keyboard-blackkey');
                    if (keyId != key_dbound)
                        basicCss['marginLeft'] = -blackkey_width / 2 + '%';
                } else {
                    basicCss['width'] = whitekey_width + "%";
                    $keyDiv.addClass('piano-keyboard-whitekey');
                    if (keyId != key_dbound) {
                        if (isBlackKey(keyId - 1))
                            basicCss['marginLeft'] = -blackkey_width / 2 + '%';
                        else
                            basicCss['marginLeft'] = 0;
                    }
                }
                $keyDiv.css(basicCss).appendTo($insertPoint);
                keyArray[keyId] = $keyDiv;
            }
            $(".piano-keyboard-key").mousedown(function() {
                var note = $(this).data('pressed', true).data('note');
                pressKey(note);
                $this.trigger('MidiView:mousedown', note);
            }).mouseup(function() {
                var note = $(this).data('pressed', false).data('note');
                releaseKey(note);
                $this.trigger('MidiView:mouseup', note);
            }).mouseleave(function() {
                var $self = $(this);
                if($self.data('pressed')) {
                    var note = $self.data('pressed', false).data('note');
                    releaseKey(note);
                    $this.trigger('MidiView:mouseup', note);
                }
            });
        };

        window.addEventListener('resize', refreshBarView);

        var ret = {
            render: render,
            generateBar: generateBar,
            refreshBarView: refreshBarView,
            screen_time: screen_time,
            pressKey: pressKey,
            releaseKey: releaseKey,
            getKeyElement: function (keyId) {
                return keyArray[keyId];
            },
            setTempo: setTempo,
            setTicksPerBeat: setTicksPerBeat
        };

        $this = $(ret);
        ret.$this = $this;

        return ret;
    }

    return MidiView;
});