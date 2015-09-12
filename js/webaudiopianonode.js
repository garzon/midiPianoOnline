function WebAudioPianoNode(context, note, volume) {
    this.oscillator = context.createOscillator();
    this.envelope = context.createGain();
    this.oscillator.frequency.value = frequencyFromNoteNumber(note);
    this.envelope.gain.value = volume;
    this.oscillator.connect(this.envelope);
}

WebAudioPianoNode.prototype.connect = function(webAudioNode) {
    this.envelope.connect(webAudioNode);
};

WebAudioPianoNode.prototype.start = function(when) {
    if(typeof when == 'undefined') when = 0;
    this.oscillator.start(when);
};

WebAudioPianoNode.prototype.stop = function(when) {
    if(typeof when == 'undefined') when = 0;
    this.oscillator.stop(when);
};

WebAudioPianoNode.prototype.disconnect = function() {
    this.oscillator.disconnect();
    this.envelope.disconnect();
};