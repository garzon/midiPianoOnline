define(function(require) {
    var WebAudioChannel = require('WebAudioChannel');
    var MidiEvent = require('MidiEvent');

    var playMode = 'playing', recordMode = 'editing';

    MidiController = function(midiKeyboardObj) {
        this.midiKeyboardObj = midiKeyboardObj;

        this.midiFileObj = null;
        this.tick = 0;
        this.time = 0;
        this.score = 0;
        this.totalTicks = 1;
        this.totalTime = 0;
        this.currentTrack = 0;
        this._playLoopLock = false;
        this._jumpFlag = false;
        this._playLoopTimerId = undefined;

        this.currentChannel = 0;

        this._pause = true;
        this.mode = playMode;
        this.recording = false;

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

    MidiController.prototype._resetTempo = function() {
        var haystack = [];
        for(var i in this.midiFileObj.setTempoEvent) {
            haystack = haystack.concat(this.midiFileObj.setTempoEvent[i].absoluteTicks);
        }
        var idx = binarySearch(haystack, this.tick, this.totalTicks, 0, haystack.length-1);
        if(idx === -1) idx = haystack.length;
        idx -= 1;
        if(idx !== -1) this.setMicrosecondsPerBeat(this.midiFileObj.setTempoEvent[idx].microsecondsPerBeat);
        else this.setMicrosecondsPerBeat(120);
    };

    MidiController.prototype._resetTracksCurrentEvent = function() {
        this.tracksCurrentEvent = [];
        for(var i=0; i<this.midiFileObj.tracks.length; i++) {
            this.tracksCurrentEvent =
                this.tracksCurrentEvent.concat(this._findTrackCurrentEventIdAtTick(i));
        }
        this._resetTempo();
        this._needToResetTime = true;
    };

    MidiController.prototype._findNextDeltatime = function() {
        var nextDeltatime = 10;
        for(var i=0; i<this.midiFileObj.tracks.length; i++) {
            if(this.tracksCurrentEvent[i]+1 == this.midiFileObj.tracks[i].length) continue;
            nextDeltatime = Math.min(nextDeltatime, this.midiFileObj.tracks[i][this.tracksCurrentEvent[i]+1].absoluteTicks - this.tick);
        }
        return nextDeltatime;
    };

    MidiController.prototype._createBarInView = function() {
        var findEventToShowInTicks = this.msToTicks(5000) + this.tick;
        for(var i=0; i<this.midiFileObj.tracks.length; i++) {
            var evtPointer = this.tracksCurrentEvent[i];
            while (evtPointer < this.midiFileObj.tracks[i].length &&
                   this.midiFileObj.tracks[i][evtPointer].absoluteTicks <= findEventToShowInTicks) {
                var event = this.midiFileObj.tracks[i][evtPointer];
                if (event.subtype == 'noteOn') {
                    var barId = event.channel + '-' + event.noteNumber + '-' + event.absoluteTicks;
                    this.midiKeyboardObj.generateBar(event.channel, event.noteNumber,
                        event.absoluteTicks, event.lastTime, this.tick, barId, this._pause);
                }
                evtPointer++;
            }
        }
    };

    MidiController.prototype._playLoop = function(deltatime, msDelay) {
        var startTime = new Date().getTime();

        if(!this._playLoopLock) this._playLoopLock = true;
        else return;

        if(this._jumpFlag) { this._jumpFlag = false; this._playLoopLock = false; return; }

        if(msDelay > 300) {
            this.pause();
            this.$this.trigger('evt_autopause');
            this.midiKeyboardObj.refreshBarView();
        }
        this.$this.trigger('evt_play:before');

        if(typeof msDelay == 'undefined') msDelay = 0;
        this.tick += deltatime;
        this.time += this.ticksToMs(deltatime);
        var finishFlag = true;
        for(var i=0; i<this.midiFileObj.tracks.length; i++) {
            if(this.tracksCurrentEvent[i]+1 == this.midiFileObj.tracks[i].length) continue;
            finishFlag = false;
            while(this.tracksCurrentEvent[i]+1 < this.midiFileObj.tracks[i].length &&
                  this.midiFileObj.tracks[i][this.tracksCurrentEvent[i]+1].absoluteTicks <= this.tick) {
                if(this._needToResetTime && this.midiFileObj.tracks[i][this.tracksCurrentEvent[i]+1].absoluteTicks == this.tick) {
                    this.time = this.midiFileObj.tracks[i][this.tracksCurrentEvent[i]+1].absoluteTime;
                    this._needToResetTime = false;
                }
                this.tracksCurrentEvent[i] += 1;
                this.handleEvent(this.midiFileObj.tracks[i][this.tracksCurrentEvent[i]]);
            }
        }
        if(finishFlag && !this.recording) {
            this._playLoopLock = false;
            this.pause();
            this.$this.trigger('evt_autopause');
            this.$this.trigger('evt_finish');
            return;
        }

        this._createBarInView();
        this.$this.trigger('evt_play:after');

        this._setPlayLoop(this._findNextDeltatime(), msDelay + new Date().getTime() - startTime);
    };

    MidiController.prototype._setPlayLoop = function(deltatime, msDelay) {
        if(this._pause) { this._playLoopLock = false; return; }

        var lastTime = new Date().getTime();
        var countdown = this.ticksToMs(deltatime)-msDelay; // fix the error of js timer

        // write in this form in order to activate gc
        var playLoopCallback = (function(self, dtime, lastTime) {
            return function() {
                var msDelay = new Date().getTime() - lastTime;
                self._playLoop(dtime, msDelay);
            };
        })(this, deltatime, lastTime+countdown);

        this._playLoopLock = false;
        if(countdown > 0)
            this._playLoopTimerId = window.setTimeout(playLoopCallback, countdown);
        else {
            console.log(countdown);
            playLoopCallback();
        }
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
        this.score = 0;
        this.ticksPerBeat = midiFileObj.header.ticksPerBeat;
        this.currentTrack = midiFileObj.header.trackCount-1;
        this.midiFileObj.reload();
        this.midiKeyboardObj.setTicksPerBeat(this.ticksPerBeat);
        this.totalTicks = this.midiFileObj.totalTicks;
        this.totalTime = this.midiFileObj.totalTime;
        this.resetCursor();
        this.$this.trigger('evt_load');
    };

    MidiController.prototype.resetCursor = function() {
        if (!this.midiFileObj) return;
        var tmp_stat = this._pause;
        this._killRunningLoop();
        this.tick = 0;
        this.time = 0;
        this._resetTracksCurrentEvent();
        this.midiKeyboardObj.refreshBarView();
        if(!tmp_stat) this.play();
        this.$this.trigger('evt_reset');
    };

    MidiController.prototype.setCursor = function(tick) {
        this._killRunningLoop();
        var tmp_stat = this._pause;
        this.tick = tick;
        this.time = 0;
        this._resetTracksCurrentEvent();
        this.midiKeyboardObj.refreshBarView();
        if(!tmp_stat) this.play();
    };

    MidiController.prototype.sliding = function(tick) {
        this._killRunningLoop();
        this.tick = tick;
        this.time = 0;
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

    MidiController.sameEvent = function(ev1, ev2) {
        if(ev1.subtype !== ev2.subtype) return false;
        if(ev1.subtype !== 'noteOn' || ev1.subtype !== 'noteOff') return false;
        return ev1.noteNumber === ev2.noteNumber;
    };

    MidiController.prototype.setTempo = function(tempo) {
        this.beatsPerMinute = tempo;
        this.midiKeyboardObj.setTempo(tempo);
    };

    MidiController.prototype.setMicrosecondsPerBeat = function(microsecondsPerBeat) {
        this.setTempo(60000000 / microsecondsPerBeat);
    };

    MidiController.prototype.handleEvent = function(event, isFromView) {
        if(isFromView && this.recording && !this._pause) {
            this.midiFileObj.insertEvent(event, this.currentTrack, this.tick, this.time);
            this.tracksCurrentEvent[this.currentTrack] += 1;
            if(this.tick > this.totalTicks) {
                this.totalTicks = this.tick;
                this.totalTime = this.time;
            }
        }
        if(isFromView && this.mode === playMode && !this._pause) {
            var correctFlag = false;
            for(var i in this.midiFileObj.tracks) {
                var lastEvent = undefined, nextEvent = undefined;
                if(this.tracksCurrentEvent[i] < this.midiFileObj.tracks[i].length)
                    lastEvent = this.midiFileObj.tracks[i][this.tracksCurrentEvent[i]];
                if(this.tracksCurrentEvent[i]+1 < this.midiFileObj.tracks[i].length)
                    nextEvent = this.midiFileObj.tracks[i][this.tracksCurrentEvent[i]+1];
                if(lastEvent && Math.abs(lastEvent.absoluteTicks - this.tick) > 300) lastEvent = undefined;
                if(nextEvent && Math.abs(nextEvent.absoluteTicks - this.tick) > 300) nextEvent = undefined;
                if(lastEvent && MidiController.sameEvent(lastEvent, event)) correctFlag = true;
                if(nextEvent && MidiController.sameEvent(nextEvent, event)) correctFlag = true;
                if(correctFlag) break;
            }
            if(correctFlag) this.score += 10;
        }
        switch (event.type) {
            case 'meta':
                switch (event.subtype) {
                    case 'setTempo':
                        this.setMicrosecondsPerBeat(event.microsecondsPerBeat);
                }
                break;
            case 'channel':
                switch (event.subtype) {
                    case 'noteOn':
                        this.channels[event.channel].noteOn(event.noteNumber, event.velocity, event.lastTime);
                        this.midiKeyboardObj.pressKey(event.channel, event.noteNumber, event.lastTime <= 0);
                        break;
                    case 'noteOff':
                        this.channels[event.channel].noteOff(event.noteNumber, event.velocity);
                        this.midiKeyboardObj.releaseKey(event.channel, event.noteNumber);
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
        this.totalTime = this.midiFileObj.totalTime;
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
        this.setEditingMode();
        this.recording = true;
        this.$this.trigger('evt_record');
    };

    MidiController.prototype.stopRecord = function() {
        this.recording = false;
        this.midiFileObj.reload();
        this.$this.trigger('evt_load');
        this.$this.trigger('evt_stopRecord');
    };

    MidiController.prototype.getRaw = function() {
        return this.midiFileObj.save();
    };

    MidiController.prototype.setPlayMode = function() {
        this.stopRecord();
        this.mode = playMode;
    };

    MidiController.prototype.setEditingMode = function() {
        this.mode = recordMode;
    };

    MidiController.prototype.refreshBarView = function() {
        this.midiKeyboardObj.refreshBarView();
    };

    return MidiController;
});