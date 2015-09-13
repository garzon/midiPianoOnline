function WebAudioInstructmentNode(context, note, volume) {
    this.envelopes = [];
    this.oscillators = [];

    for(var i in WebAudioPianoNode.synth.data) {
        var oscillator = context.createOscillator();
        var envelope = context.createGain();
        oscillator.frequency.value = frequencyFromNoteNumber(note) * i;
        envelope.gain.value = volume * WebAudioPianoNode.synth.data[i];
        oscillator.connect(envelope);

        this.envelopes = this.envelopes.concat(envelope);
        this.oscillators = this.oscillators.concat(oscillator);
    }
}

WebAudioPianoNode.prototype.connect = function(webAudioNode) {
    for(var i in this.envelopes) this.envelopes[i].connect(webAudioNode);
};

WebAudioPianoNode.prototype.start = function(when) {
    if(typeof when == 'undefined') when = 0;
    for(var i in this.oscillators) this.oscillators[i].start(when);
};

WebAudioPianoNode.prototype.stop = function(when) {
    if(typeof when == 'undefined') when = 0;
    for(var i in this.oscillators) this.oscillators[i].stop(when);
};

WebAudioPianoNode.prototype.disconnect = function() {
    for(var i in this.oscillators) this.oscillators[i].disconnect();
    for(var i in this.envelopes) this.envelopes[i].disconnect();
};