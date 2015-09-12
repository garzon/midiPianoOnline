MidiNoteBarController = function(midiKeyboardObj, Channel) {
    this.midiKeyboardObj = midiKeyboardObj;
    if(typeof Channel == 'undefined') Channel = WebAudioChannel;

    this.midiFileObj = null;
    this.tick = 0;
    this.totalTicks = 1;

    this.channels = [];
    for(var i = 0; i < 16; i++) {
        this.channels[i] = new Channel(i);
    }
};

MidiNoteBarController.prototype._findNextDeltatime = function() {
    var nextDeltatime = 20;
    for(var i=0; i<this.midiFileObj.tracks.length; i++) {
        if(this.tracksCurrentEvent[i]+1 == this.midiFileObj.tracks[i].length) continue;
        nextDeltatime = Math.min(nextDeltatime, this.midiFileObj.tracks[i][this.tracksCurrentEvent[i]+1].deltaTime);
    }
    return nextDeltatime;
};

MidiNoteBarController.prototype._playLoop = function(deltatime) {
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

    // show the bars
    var findEventToShowInTicks = this.msToTicks((this.midiKeyboardObj.screen_time + 1)*1000) + this.tick;
    var realNowTime = this.ticksToMs(this.tick)/1000;
    for(var i=0; i<this.midiFileObj.tracks.length; i++) {
        var evtPointer = this.tracksCurrentEvent[i]+1;
        if(evtPointer == this.midiFileObj.tracks[i].length) continue;
        while(evtPointer < this.midiFileObj.tracks[i].length &&
              this.midiFileObj.tracks[i][evtPointer].absoluteTicks <= findEventToShowInTicks) {
            var event = this.midiFileObj.tracks[i][evtPointer];
            if(event.subtype == 'noteOn') {
                this.midiKeyboardObj.generateBar(event.channel, event.noteNumber,
                    this.ticksToMs(event.absoluteTicks)/1000, this.ticksToMs(event.lastTime)/1000, realNowTime);
            }
            evtPointer++;
        }
    }

    this._setPlayLoop(this._findNextDeltatime());
};

MidiNoteBarController.prototype._setPlayLoop = function(deltatime) {
    if(this.pause) return;

    // write in this form in order to activate gc
    var playLoopCallback = (function(self, dtime) {
        return function() {
            self._playLoop(dtime);
        };
    })(this, deltatime);

    window.setTimeout(playLoopCallback, this.ticksToMs(deltatime));
};

MidiNoteBarController.prototype.ticksToMs = function(ticks) {
    var msPerTick = 60000 / (this.ticksPerBeat * this.beatsPerMinute);
    return msPerTick * ticks;
};

MidiNoteBarController.prototype.msToTicks = function(ms) {
    var msPerTick = 60000 / (this.ticksPerBeat * this.beatsPerMinute);
    return ms / msPerTick;
};

MidiNoteBarController.prototype.open = function(midiFileObj) {
    this.midiFileObj = midiFileObj;
    this.beatsPerMinute = 120;
    this.ticksPerBeat = midiFileObj.header.ticksPerBeat;
    this.totalTicks = midiFileObj.totalTicks;
    this.resetCursor();
};

MidiNoteBarController.prototype.resetCursor = function() {
    if (!this.midiFileObj) return;
    this.pause = true;
    this.tick = 0;
    this.recordMode = false;
    this.tracksCurrentEvent = [];
    for(var i=0; i<this.midiFileObj.tracks.length; i++) {
        this.tracksCurrentEvent = this.tracksCurrentEvent.concat(-1);
    }
};

MidiNoteBarController.prototype.handleEvent = function(event, isFromView) {
    if(isFromView && this.recordMode) {

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
};

MidiNoteBarController.prototype.play = function() {
    if (!this.midiFileObj) return;
    this.pause = false;
    this._playLoop(this._findNextDeltatime());
};