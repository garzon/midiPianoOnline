MidiNoteBarController = function(Channel) {
    // singleton
    if(MidiNoteBarController._instance !== null) return MidiNoteBarController._instance;

    if(typeof Channel == 'undefined') Channel = MidiChannel;

    this.midiFileObj = null;

    this.channels = [];
    for(var i = 0; i < 16; i++) {
        this.channels[i] = new Channel(i);
    }

    MidiNoteBarController._instance = this;
};

MidiNoteBarController._instance = null;

MidiNoteBarController.instance = function() {
    if(MidiNoteBarController._instance !== null) return MidiNoteBarController._instance;
    return new MidiNoteBarController(MidiChannel);
}

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
    this._setPlayLoop(this._findNextDeltatime());
};

MidiNoteBarController.prototype._setPlayLoop = function(deltatime) {
    if(this.pause) return;
    var msPerTick = 60000 / (this.ticksPerBeat * this.beatsPerMinute);

    // use string type to gc
    window.setTimeout('MidiNoteBarController.instance()._playLoop(' + deltatime + ');', deltatime * msPerTick);
};

MidiNoteBarController.prototype.open = function(midiFileObj) {
    this.midiFileObj = midiFileObj;
    this.beatsPerMinute = 120;
    this.ticksPerBeat = midiFileObj.header.ticksPerBeat;
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
                    break;
                case 'noteOff':
                    this.channels[event.channel].noteOff(event.noteNumber, event.velocity);
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