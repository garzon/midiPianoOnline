define(function(require) {
    var WebAudioChannel = require('WebAudioChannel');

    MidiController = function(midiKeyboardObj, Channel) {
        this.midiKeyboardObj = midiKeyboardObj;
        if(typeof Channel == 'undefined') Channel = WebAudioChannel;

        this.midiFileObj = null;
        this.tick = 0;
        this.totalTicks = 1;

        this.channels = [];
        for(var i = 0; i < 16; i++) {
            this.channels[i] = new Channel(i);
        }

        this.$this = $(this);
    };

    MidiController.prototype._findNextDeltatime = function() {
        var nextDeltatime = 20;
        for(var i=0; i<this.midiFileObj.tracks.length; i++) {
            if(this.tracksCurrentEvent[i]+1 == this.midiFileObj.tracks[i].length) continue;
            nextDeltatime = Math.min(nextDeltatime, this.midiFileObj.tracks[i][this.tracksCurrentEvent[i]+1].deltaTime);
        }
        return nextDeltatime;
    };

    MidiController.prototype._createBarInView = function() {
        var findEventToShowInTicks = this.msToTicks((this.midiKeyboardObj.screen_time + 0.5)*1000) + this.tick;
        var realNowTime = this.ticksToMs(this.tick)/1000;
        for(var i=0; i<this.midiFileObj.tracks.length; i++) {
            var evtPointer = this.tracksCurrentEvent[i]+1;
            if(evtPointer == this.midiFileObj.tracks[i].length) continue;
            while(evtPointer < this.midiFileObj.tracks[i].length &&
            this.midiFileObj.tracks[i][evtPointer].absoluteTicks <= findEventToShowInTicks) {
                var event = this.midiFileObj.tracks[i][evtPointer];
                if(event.subtype == 'noteOn') {
                    var barId = event.channel + '-' + event.noteNumber + '-' + event.absoluteTicks;
                    this.midiKeyboardObj.generateBar(event.channel, event.noteNumber,
                        this.ticksToMs(event.absoluteTicks)/1000, this.ticksToMs(event.lastTime)/1000, realNowTime, barId);
                }
                evtPointer++;
            }
        }
    };

    MidiController.prototype._playLoop = function(deltatime, msDelay) {
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
        if(finishFlag) {
            this.resetCursor();
            console.log('finish');
            return;
        }

        this._createBarInView();
        this._setPlayLoop(this._findNextDeltatime(), msDelay);

        this.$this.trigger('evt_play:after');
    };

    MidiController.prototype._setPlayLoop = function(deltatime, msDelay) {
        if(this._pause) return;

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

        window.setTimeout(playLoopCallback, countdown);
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
        this.totalTicks = midiFileObj.totalTicks;
        this.resetCursor();
        this.$this.trigger('evt_load');
    };

    MidiController.prototype.resetCursor = function() {
        if (!this.midiFileObj) return;
        this._pause = true;
        this.tick = 0;
        this.recordMode = false;
        this.tracksCurrentEvent = [];
        for(var i=0; i<this.midiFileObj.tracks.length; i++) {
            this.tracksCurrentEvent = this.tracksCurrentEvent.concat(-1);
        }
        this.$this.trigger('evt_reset');
    };

    MidiController.prototype.handleEvent = function(event, isFromView) {
        if(isFromView && this.recordMode) {
            // TODO
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

    MidiController.prototype.play = function() {
        if (!this.midiFileObj) return;
        this._pause = false;
        this._playLoop(this._findNextDeltatime());
        this.$this.trigger('evt_play');
    };

    MidiController.prototype.pause = function() {
        this._pause = true;
        this.$this.trigger('evt_pause');
    };

    return MidiController;
});