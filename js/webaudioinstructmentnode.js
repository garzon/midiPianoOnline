define(function() {

    function WebAudioInstructmentNode(context, note, volume, channelId) {
        this.oscillators = [];
        this.envelopes = [];

        for (var i in this.synth.data) {
            var freq = i * frequencyFromNoteNumber(note);

            var oscillator = context.createOscillator();
            var envelope = context.createGain();

            oscillator.frequency.value = freq;
            envelope.gain.value = volume * this.synth.data[i][0];
            oscillator.type = 'triangle';

            oscillator.connect(envelope);

            this.envelopes = this.envelopes.concat(envelope);
            this.oscillators = this.oscillators.concat(oscillator);
        }
    }

    WebAudioInstructmentNode.prototype.connect = function (webAudioNode) {
        for (var i in this.envelopes) this.envelopes[i].connect(webAudioNode);
    };

    WebAudioInstructmentNode.prototype.start = function (when) {
        if (typeof when == 'undefined') when = 0;
        for (var i in this.oscillators) this.oscillators[i].start(when);
    };

    WebAudioInstructmentNode.prototype.stop = function (when) {
        if (typeof when == 'undefined') when = 0;
        for (var i in this.oscillators) this.oscillators[i].stop(when);
    };

    WebAudioInstructmentNode.prototype.disconnect = function () {
        for (var i in this.oscillators) this.oscillators[i].disconnect();
        for (var i in this.envelopes) this.envelopes[i].disconnect();
    };

    WebAudioInstructmentNode.instructmentSet = {};

    WebAudioInstructmentNode.registerInstructmentId = function(id, node) {
        WebAudioInstructmentNode.instructmentSet[id] = node;
    };

    return WebAudioInstructmentNode;
});