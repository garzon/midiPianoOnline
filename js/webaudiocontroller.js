function WebAudioController() {
    // singleton
    if(WebAudioController._instance !== null) return WebAudioController._instance;

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

    WebAudioController._instance = this;
}

WebAudioController._instance = null;

WebAudioController.instance = function() {
    if(WebAudioController._instance !== null) return WebAudioController._instance;
    return new WebAudioController();
};

WebAudioController.noteOn = function(channelId, note, velocity, deltatime) {
    WebAudioController.instance().noteOn(channelId, note, velocity, deltatime);
};

WebAudioController.noteOff = function(channelId, note) {
    WebAudioController.instance().noteOff(channelId, note);
};

WebAudioController.prototype._indexNote = function(channelId, note) {
    return channelId + 0x100 * note;
};

WebAudioController.prototype.noteOn = function(channelId, note, velocity, deltatime) {

    if(this._objs[this._indexNote(channelId, note)]) this.noteOff(channelId, note);

    var node = WebAudioPianoNode(this.context, note, velocity / 128.0);
    node.connect(this.context.destination);
    node.start();

    this._objs[this._indexNote(channelId, note)] = node;

    if(deltatime == -1) {
        var self = this;
        window.mySetTimeout(function() { self.noteOff(channelId, note); }, 1000);
    }
};

WebAudioController.prototype.noteOff = function(channelId, note) {
    if(this._objs[this._indexNote(channelId, note)]) {
        this._objs[this._indexNote(channelId, note)].stop();
        this._objs[this._indexNote(channelId, note)].disconnect();
        this._objs[this._indexNote(channelId, note)] = undefined;
    }
};
