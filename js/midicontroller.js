define(function(require) {
    var WebAudioChannel = require('WebAudioChannel');
    var MidiEvent = require('MidiEvent');

    var playMode = 0, recordMode = 1;

    MidiController = function(midiKeyboardObj) {
        this.midiKeyboardObj = midiKeyboardObj;

        this.midiFileObj = null;
        this.tick = 0;
        this.totalTicks = 1;
        this.currentTrack = 0;
        this._playLoopLock = false;
        this._jumpFlag = false;
        this._playLoopTimerId = undefined;

        this.currentChannel = 0;

        this._pause = true;
        this.mode = playMode;

        this.channels = [];
        this.channelInstructmentId = [];
        for(var i = 0; i < 16; i++) {
            this.channels[i] = new WebAudioChannel(i);
            this.channelInstructmentId[i] = 0;
        }

        this.$this = $(this);
    };

    MidiController.prototype._findTrackCurrentEventIdAtTick = function(trackId) {
        var track = this.midiFileObj.tracks[trackId];
        var haystack = [];
        for(var i in track) {
            haystack = haystack.concat(track[i].absoluteTicks);
        }
        var idx = binarySearch(haystack, this.tick, this.totalTicks, 0, haystack.length-1);
        return idx === -1 ? haystack.length-1 : idx;
    };

    MidiController.prototype._resetTracksCurrentEvent = function() {
        this.tracksCurrentEvent = [];
        for(var i=0; i<this.midiFileObj.tracks.length; i++) {
            this.tracksCurrentEvent =
                this.tracksCurrentEvent.concat(this._findTrackCurrentEventIdAtTick(i));
        }
    };

    MidiController.prototype._findNextDeltatime = function() {
        var nextDeltatime = 30;
        for(var i=0; i<this.midiFileObj.tracks.length; i++) {
            if(this.tracksCurrentEvent[i]+1 == this.midiFileObj.tracks[i].length) continue;
            nextDeltatime = Math.min(nextDeltatime, this.midiFileObj.tracks[i][this.tracksCurrentEvent[i]+1].absoluteTicks - this.tick);
        }
        return nextDeltatime;
    };

    MidiController.prototype._createBarInView = function() {
        var findEventToShowInTicks = this.msToTicks((this.midiKeyboardObj.screen_time + 0.5)*1000) + this.tick;
        var realNowTime = this.ticksToMs(this.tick)/1000;
        for(var i=0; i<this.midiFileObj.tracks.length; i++) {
            var evtPointer = this.tracksCurrentEvent[i];
            while (evtPointer < this.midiFileObj.tracks[i].length &&
            this.midiFileObj.tracks[i][evtPointer].absoluteTicks <= findEventToShowInTicks) {
                var event = this.midiFileObj.tracks[i][evtPointer];
                if (event.subtype == 'noteOn') {
                    var barId = event.channel + '-' + event.noteNumber + '-' + event.absoluteTicks;
                    this.midiKeyboardObj.generateBar(event.channel, event.noteNumber,
                        this.ticksToMs(event.absoluteTicks) / 1000, this.ticksToMs(event.lastTime) / 1000, realNowTime, barId, this._pause);
                }
                evtPointer++;
            }
        }
    };

    MidiController.prototype._playLoop = function(deltatime, msDelay) {
        if(!this._playLoopLock) this._playLoopLock = true;
        else return;

        if(this._jumpFlag) { this._jumpFlag = false; this._playLoopLock = false; return; }

        if(msDelay > 300) {
            this.pause();
            this.midiKeyboardObj.refreshBarView();
        }
        this.$this.trigger('evt_play:before');

        if(typeof msDelay == 'undefined') msDelay = 0;
        this.tick += deltatime;
        var finishFlag = true;
        for(var i=0; i<this.midiFileObj.tracks.length; i++) {
            if(this.tracksCurrentEvent[i]+1 == this.midiFileObj.tracks[i].length) continue;
            finishFlag = false;
            while(this.tracksCurrentEvent[i]+1 < this.midiFileObj.tracks[i].length &&
            this.midiFileObj.tracks[i][this.tracksCurrentEvent[i]+1].absoluteTicks <= this.tick) {
                this.tracksCurrentEvent[i] += 1;
                this.handleEvent(this.midiFileObj.tracks[i][this.tracksCurrentEvent[i]]);
            }
        }
        if(finishFlag && this.mode != recordMode) {
            console.log('finish');
            this._playLoopLock = false;
            this.pause();
            return;
        }

        this._createBarInView();
        this.$this.trigger('evt_play:after');

        this._setPlayLoop(this._findNextDeltatime(), msDelay);
    };

    MidiController.prototype._setPlayLoop = function(deltatime, msDelay) {
        if(this._pause) { this._playLoopLock = false; return; }

        var date = new Date();
        var lastTime = date.getTime();
        var countdown = this.ticksToMs(deltatime)-msDelay; // fix the error of js timer

        // write in this form in order to activate gc
        var playLoopCallback = (function(self, dtime, lastTime) {
            return function() {
                var date = new Date();
                var msDelay = date.getTime() - lastTime;
                self._playLoop(dtime, msDelay);
            };
        })(this, deltatime, lastTime+countdown);

        this._playLoopLock = false;
        this._playLoopTimerId = window.setTimeout(playLoopCallback, countdown);
    };

    MidiController.prototype._killRunningLoop = function() {
        if(this._playLoopLock) {
            this._jumpFlag = true;
            while(this._jumpFlag);
        }
        if(this._playLoopTimerId) {
            window.clearTimeout(this._playLoopTimerId);
        }
        this.mute();
    };

    MidiController.prototype.ticksToMs = function(ticks) {
        var msPerTick = 60000 / (this.ticksPerBeat * this.beatsPerMinute);
        return msPerTick * ticks;
    };

    MidiController.prototype.msToTicks = function(ms) {
        var msPerTick = 60000 / (this.ticksPerBeat * this.beatsPerMinute);
        return ms / msPerTick;
    };

    MidiController.prototype.load = function(midiFileObj) {
        this.midiFileObj = midiFileObj;
        this.beatsPerMinute = 120;
        this.ticksPerBeat = midiFileObj.header.ticksPerBeat;
        this.currentTrack = midiFileObj.header.trackCount-1;
        this.midiFileObj.reload();
        this.totalTicks = this.midiFileObj.totalTicks;
        this.resetCursor();
        this.$this.trigger('evt_load');
    };

    MidiController.prototype.resetCursor = function() {
        if (!this.midiFileObj) return;
        var tmp_stat = this._pause;
        this._killRunningLoop();
        this.tick = 0;
        this._resetTracksCurrentEvent();
        this.midiKeyboardObj.refreshBarView();
        if(!tmp_stat) this.play();
        this.$this.trigger('evt_reset');
    };

    MidiController.prototype.setCursor = function(tick) {
        this._killRunningLoop();
        var tmp_stat = this._pause;
        this.tick = tick;
        this._resetTracksCurrentEvent();
        this.midiKeyboardObj.refreshBarView();
        if(!tmp_stat) this.play();
    };

    MidiController.prototype.sliding = function(tick) {
        this._killRunningLoop();
        this.tick = tick;
        this._resetTracksCurrentEvent();
        this.midiKeyboardObj.refreshBarView();

        for(var i=0; i<this.midiFileObj.tracks.length; i++) {
            if(this.tracksCurrentEvent[i]+1 == this.midiFileObj.tracks[i].length) continue;
            while(this.tracksCurrentEvent[i]+1 < this.midiFileObj.tracks[i].length &&
                  Math.abs(this.midiFileObj.tracks[i][this.tracksCurrentEvent[i]+1].absoluteTicks - this.tick) < 500) {
                this.tracksCurrentEvent[i] += 1;
                this.handleEvent(this.midiFileObj.tracks[i][this.tracksCurrentEvent[i]]);
            }
        }

        this._createBarInView();
    };

    MidiController.prototype.handleEvent = function(event, isFromView) {
        if(isFromView && this.mode === recordMode && !this._pause) {
            event.absoluteTicks = this.tick;
            event.lastTime = 1;
            this.midiFileObj.insertEvent(event, this.currentTrack, this.tick);
            this.tracksCurrentEvent[this.currentTrack] += 1;
            if(this.tick > this.totalTicks) {
                this.totalTicks = this.tick;
            }
        }
        switch (event.type) {
            case 'meta':
                switch (event.subtype) {
                    case 'setTempo':
                        this.beatsPerMinute = 60000000 / event.microsecondsPerBeat;
                }
                break;
            case 'channel':
                switch (event.subtype) {
                    case 'noteOn':
                        this.channels[event.channel].noteOn(event.noteNumber, event.velocity, event.lastTime);
                        this.midiKeyboardObj.pressKey(event.noteNumber, event.lastTime <= 0);
                        break;
                    case 'noteOff':
                        this.channels[event.channel].noteOff(event.noteNumber, event.velocity);
                        this.midiKeyboardObj.releaseKey(event.noteNumber);
                        break;
                    case 'programChange':
                        //console.log('program change to ' + event.programNumber);
                        this.channels[event.channel].setProgram(event.programNumber);
                        break;
                }
                break;
        }
        this.$this.trigger('evt_event', [event, isFromView]);
    };

    MidiController.prototype.setInstructment = function(channelId, instructmentId) {
        this.channelInstructmentId[channelId] = instructmentId;
        this.channels[channelId].setProgram(instructmentId);
    };

    MidiController.prototype.setAllInstructment = function(instructmentId) {
        for(var i in this.channels) this.setInstructment(i, instructmentId);
    };

    MidiController.prototype.refreshInstructment = function() {
        for(var i in this.channels) this.setInstructment(i, this.channelInstructmentId[i]);
    };

    MidiController.prototype.setInstructmentSet = function(instructmentSet) {
        WebAudioChannel.setInstructmentSet(instructmentSet);
        this.refreshInstructment();
    };

    MidiController.prototype.play = function() {
        if (!this.midiFileObj) return;
        this.midiFileObj.reload();
        this.totalTicks = this.midiFileObj.totalTicks;
        console.log(this.midiFileObj);
        this._pause = false;
        this._playLoop(this._findNextDeltatime());
        this.midiKeyboardObj.refreshBarView();
        this.$this.trigger('evt_load');
        this.$this.trigger('evt_play');
    };

    MidiController.prototype.pause = function() {
        this._pause = true;
        this.mute();
        this.midiKeyboardObj.refreshBarView();
        this._createBarInView();
        this.$this.trigger('evt_pause');
    };

    MidiController.prototype.mute = function() {
        for(var i in this.channels) {
            this.channels[i].mute();
        }
    };

    MidiController.prototype.record = function() {
        this.mode = recordMode;
        console.log('recording');
    };

    MidiController.prototype.stopRecord = function() {
        this.mode = playMode;
        this.midiFileObj.reload();
        this.$this.trigger('evt_load');
        console.log('stop recording');
    };

    MidiController.prototype.getRaw = function() {
        return this.midiFileObj.save();
    };

    return MidiController;
});