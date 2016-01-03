define(function() {
    function MidiView($insertPoint, $barInsertPoint) {

        var selected = $([]);   // list of selected objects
        var lastselected = '';  // for the shift-click event

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
            $(".piano-keyboard-key-pressed").each(function() {
                var $this = $(this);
                var className = isBlackKey($this.data('note')) ? 'piano-keyboard-blackkey': 'piano-keyboard-whitekey';
                $this.attr('class', 'piano-keyboard-key ' + className);
            });
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

        var nowTick;

        function setNow(now_tick) {
            nowTick = now_tick;
        }

        var keyLeftArray = {};

        var calculateInfoFromPos = function(ele) {
            var $ele = $(ele);
            var pos = getPosition(ele);
            var left = pos.left;
            var top = pos.top;
            var height = $ele.height();

            var idx = binarySearch(keyLeftArray, left, 100000, 0, keyLeftArray.length-1);
            // idx >= 1

            var note;

            if(idx == 1) {
                note = key_dbound;
            } else {
                if(idx == keyLeftArray.length-1) {
                    note = key_ubound;
                } else {
                    if(Math.abs(keyLeftArray[idx] - left) < Math.abs(keyLeftArray[idx-1] - left)) {
                        note = key_dbound + idx - 1;
                    } else {
                        note = key_dbound + idx - 2;
                    }
                }
            }

            screen_path = getPosition(keyArray[note].get(0)).top;
            var velocity = screen_path / screen_time * tempo / standard_tempo;
            var ticksToLenCoeff = 60 * velocity / tempo / ticksPerBeat;

            var absoluteTicks = (screen_path - (top + height)) / ticksToLenCoeff + nowTick;
            var lastTime = height / ticksToLenCoeff;

            var old_absoluteTicks = $ele.data('absoluteTicks');
            var old_channel = $ele.data('channel');
            var old_note = $ele.data('note');
            var old_barId = $ele.data('barId');
            var old_lastTime = $ele.data('lastTime');
            var old_track = $ele.data('trackId');

            return {
                old_absoluteTicks: old_absoluteTicks,
                old_channel: old_channel,
                old_note: old_note,
                old_barId: old_barId,
                old_lastTime: old_lastTime,
                old_trackId: old_track,
                note: note,
                lastTime: Math.round(lastTime),
                absoluteTicks: Math.round(absoluteTicks)
            };
        };

        var generateBar = function(track, channelId, note, volume, absoluteTicks, lastTime, barId, isPause, isEditable) {
            if (nowTick >= absoluteTicks + lastTime) return;
            var index = barId;
            if (barArray[index]) return;

            var $ele = $("<div/>")
                .data('channel', channelId)
                .data('note', note)
                .data('absoluteTicks', absoluteTicks)
                .data('barId', barId)
                .data('selected', false)
                .data('trackId', track)
                .data('lastTime', lastTime);

            barArray[index] = $ele;

            if(lastTime <= 0) lastTime = 10;  // a "short open" bar

            if(isEditable) {
                var editCallback = function() {
                    var velocity = screen_path / screen_time * tempo / standard_tempo;
                    var ticksToLenCoeff = 60 * velocity / tempo / ticksPerBeat;

                    var info = calculateInfoFromPos(this);
                    info.volume = volume;
                    info.$ele = $(this)
                        .data('channel', channelId)
                        .data('note', info.note)
                        .data('absoluteTicks', info.absoluteTicks)
                        .data('barId', barId)
                        .data('lastTime', info.lastTime)
                        .css({
                            left: (getPosition(keyArray[info.note].get(0)).left * 100 / innerWidth) + '%',
                            width: (isBlackKey(info.note) ? blackkey_width : whitekey_width) + "%",
                            height: info.lastTime * ticksToLenCoeff + 'px'
                        });

                    $this.trigger('MidiView:dragged', info);
                };

                $ele.draggable({
                    start: function() {
                        $this.trigger('MidiView:drag-start');
                    },
                    stop: function() {
                        editCallback.apply($ele.get(0));
                        //$ele.get(0).click();
                    }
                }).on('click', function() {
                    console.log('clicked');
                    var flag = $ele.data('selected');
                    if(flag === false) {
                        $ele.addClass('piano-bar-selected');
                    } else {
                        $ele.removeClass('piano-bar-selected');
                    }
                    $ele.data('selected', !flag);
                });
            }

            var $key = keyArray[note];
            if(typeof $key === 'undefined') return;
            
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
                top: top + 'px',
                display: "inline-block"
            }).addClass("piano-bar").addClass(getChannelColorClassName(channelId));

            $ele.appendTo($barInsertPoint);

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

        var getChannelColorClassName = function(channel) {
            if(channel <= 2) return 'piano-keyboard-key-pressed-' + channel;
            return 'piano-keyboard-key-pressed-otherchannel';
        };

        var pressKey = function (channel, note, autoRelease) {
            var $ele = keyArray[note];
            if ($ele) $ele.addClass('piano-keyboard-key-pressed').addClass(getChannelColorClassName(channel));
            if (autoRelease) {
                window.setTimeout(function () {
                    releaseKey(note);
                }, 50);
            }
        };

        var releaseKey = function (channel, note) {
            var $ele = keyArray[note];
            if ($ele) $ele.removeClass(getChannelColorClassName(channel));
        };

        var isBlackKey = function (id) {
            return isBasicBlackKey[id % 12];
        };

        var resetKeyLeftArr = function() {
            keyLeftArray = [-99999];
            for(var keyId = key_dbound; keyId <= key_ubound; keyId++) {
                var res = getPosition(keyArray[keyId].get(0));
                keyLeftArray = keyLeftArray.concat(res.left);
            }
            keyLeftArray = keyLeftArray.concat(99999);
        };

        var onWindowResize = function() {
            refreshBarView();
            resetKeyLeftArr();
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
                pressKey(undefined, note);
                $this.trigger('MidiView:mousedown', note);
            }).mouseup(function() {
                var note = $(this).data('pressed', false).data('note');
                releaseKey(undefined, note);
                $this.trigger('MidiView:mouseup', note);
            }).mouseleave(function() {
                var $self = $(this);
                if($self.data('pressed')) {
                    var note = $self.data('pressed', false).data('note');
                    releaseKey(undefined, note);
                    $this.trigger('MidiView:mouseup', note);
                }
            });

            resetKeyLeftArr();
            window.addEventListener('resize', onWindowResize);
        };

        var scoring = function(note, score) {
            var rank = 'ok';
            if(score > 20) rank = 'perfect';
            if(15 < score && score <= 20) rank = 'good';
            if(score < 0) rank = 'wrong';

            var $key = keyArray[note];
            var pos = getPosition($key.get(0));
            var $ele = $("<div/>").text(rank).addClass('scoring').addClass('scoring-'+rank).css({
                top: pos.top + 'px',
                left: pos.left * 100 / innerWidth + '%'
            });
            $ele.animate({top: '-=10'}).fadeOut(function() { $(this).remove(); }).insertBefore($insertPoint);
        };

        var ret = {
            render: render,
            generateBar: generateBar,
            refreshBarView: refreshBarView,
            pressKey: pressKey,
            releaseKey: releaseKey,
            setTempo: setTempo,
            setTicksPerBeat: setTicksPerBeat,
            scoring: scoring,
            setNow: setNow
        };

        $this = $(ret);
        ret.$this = $this;

        return ret;
    }

    return MidiView;
});