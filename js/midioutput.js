function MidiOutput() {
    // singleton
    if(MidiOutput._instance !== null) return MidiOutput._instance;

    var AudioContext = AudioContext || webkitAudioContext; // for ios/safari
    this.context = new AudioContext();

    if (navigator.requestMIDIAccess) {
        navigator.requestMIDIAccess({
            sysex: false
        }).then(onMIDISuccess, function() {
            alert("Load MIDI failed. Please refresh this page.");
        });
    } else {
        alert("No MIDI support in your browser.");
    }

    function onMIDISuccess(midiAccess) {
        /*
        midi = midiAccess;
        var inputs = midi.inputs.values();
        // loop through all inputs
        for (var input = inputs.next(); input && !input.done; input = inputs.next()) {
            // listen for midi messages
            input.value.onmidimessage = onMIDIMessage;
            // this just lists our inputs in the console
            listInputs(input);
        }
        // listen for connect/disconnect message
        midi.onstatechange = onStateChange;*/
    }

    this._objs = {};

    MidiOutput._instance = this;
}

MidiOutput._instance = null;

MidiOutput.instance = function() {
    if(MidiOutput._instance !== null) return MidiOutput._instance;
    return new MidiOutput();
};

MidiOutput.noteOn = function(channelId, note, velocity, deltatime) {
    MidiOutput.instance().noteOn(channelId, note, velocity, deltatime);
};

MidiOutput.noteOff = function(channelId, note) {
    MidiOutput.instance().noteOff(channelId, note);
};

MidiOutput.prototype._indexNote = function(channelId, note) {
    return channelId + 0x100 * note;
};

MidiOutput.prototype.noteOn = function(channelId, note, velocity, deltatime) {
    if(deltatime == 0) deltatime = -1;

    if(this._objs[this._indexNote(channelId, note)]) this.noteOff(channelId, note);

    var oscillator = this.context.createOscillator();
    var envelope = this.context.createGain();
    oscillator.frequency.value = frequencyFromNoteNumber(note);
    envelope.gain.value = velocity / 256.0;
    oscillator.connect(envelope);
    envelope.connect(this.context.destination);
    oscillator.start();

    this._objs[this._indexNote(channelId, note)] = [oscillator, envelope];

    if(deltatime == -1) {
        var self = this;
        window.setTimeout(function() { self.noteOff(channelId, note); }, 1000);
    }
};

MidiOutput.prototype.noteOff = function(channelId, note) {
    if(this._objs[this._indexNote(channelId, note)]) {
        this._objs[this._indexNote(channelId, note)][0].stop();
        this._objs[this._indexNote(channelId, note)][0].disconnect();
        this._objs[this._indexNote(channelId, note)][1].disconnect();
        this._objs[this._indexNote(channelId, note)] = undefined;
    }
};
