function WebAudioInstructmentNode(context, note, volume) {
    this.envelopes = [];
    this.oscillators = [];

    for(var i in this.synth.data) {
        var oscillator = context.createOscillator();
        var envelope = context.createGain();
        oscillator.frequency.value = frequencyFromNoteNumber(note) * i;
        envelope.gain.value = volume * this.synth.data[i];
        oscillator.connect(envelope);

        this.envelopes = this.envelopes.concat(envelope);
        this.oscillators = this.oscillators.concat(oscillator);
    }
}

WebAudioInstructmentNode.prototype.connect = function(webAudioNode) {
    for(var i in this.envelopes) this.envelopes[i].connect(webAudioNode);
};

WebAudioInstructmentNode.prototype.start = function(when) {
    if(typeof when == 'undefined') when = 0;
    for(var i in this.oscillators) this.oscillators[i].start(when);
};

WebAudioInstructmentNode.prototype.stop = function(when) {
    if(typeof when == 'undefined') when = 0;
    for(var i in this.oscillators) this.oscillators[i].stop(when);
};

WebAudioInstructmentNode.prototype.disconnect = function() {
    for(var i in this.oscillators) this.oscillators[i].disconnect();
    for(var i in this.envelopes) this.envelopes[i].disconnect();
};