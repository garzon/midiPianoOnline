define(['WebAudioInstructmentNode'], function(WebAudioInstructmentNode) {

    function WebAudioHornNode(context, note, volume, channelId) {
        var freq = frequencyFromNoteNumber(note);

        this.context = context;

        this.velocityGain = context.createGain();
        this.velocityGain.gain.value = volume;

        this.vco0 = context.createOscillator();
        this.vco0Gain = context.createGain();
        this.vco1 = context.createOscillator();
        this.vco1Gain = context.createGain();

        this.vcf0 = context.createBiquadFilter();
        this.lfo = context.createOscillator();
        this.lfo0Gain = context.createGain();
        this.lfo1Gain = context.createGain();
        this.modVcfGain = context.createGain();
        this.vco0.frequency.value = freq;
        this.vco1.frequency.value = freq;

        // initial values
        this.lfo.frequency.value = 5.5;
        this.lfo0Gain.gain.value = 1;
        this.lfo1Gain.gain.value = 7;

        this.vco0.type = 'sawtooth';
        this.vco0.detune.value = 0;
        this.vco1.type = 'sawtooth';
        this.vco1.detune.value = -5;
        this.vcf0.Q.value = 1;
        this.vcf0.frequency.value = freq;
        this.modVcfGain.gain.value = 50;

        // connect vcoGain
        this.vco0.connect(this.vco0Gain);
        this.vco0Gain.connect(this.velocityGain);
        this.vco1.connect(this.vco1Gain);
        this.vco1Gain.connect(this.velocityGain);

        // connect vco to vcf
        this.velocityGain.connect(this.vcf0);

        // connect lfo to vcfGain
        this.lfo.connect(this.modVcfGain);
        // connect vcfgain to vcf detune
        this.modVcfGain.connect(this.vcf0.detune);
        // connect lfo to lfoGain
        this.lfo.connect(this.lfo0Gain);
        // connect lfoGain to vco frequency
        this.lfo0Gain.connect(this.vco0.frequency);
        this.lfo1Gain.connect(this.vco1.frequency);
    }

    WebAudioHornNode.prototype.connect = function (webAudioNode) {
        this.vcf0.connect(webAudioNode);
    };

    WebAudioHornNode.prototype.start = function (when) {
        if (typeof when == 'undefined') when = 0;

        var attack=0.5, decay=0.5, sustain=0.5, release=0.5;
        var target0 = this.vco0Gain.gain;
        var now = this.context.currentTime + when;
        var rootValue0 = rootValue1 = 1;

        target0.linearRampToValueAtTime(rootValue0, now + attack);
        target0.linearRampToValueAtTime(sustain * rootValue0, now + attack + decay);

        var target1 = this.vco1Gain.gain;
        target1.linearRampToValueAtTime(rootValue1, now + attack);
        target1.linearRampToValueAtTime(sustain * rootValue1, now + attack + decay);

        this.vco0.start(when);
        this.vco1.start(when);
        this.lfo.start(when);
    };

    WebAudioHornNode.prototype.stop = function (when) {
        if (typeof when == 'undefined') when = 0;
        this.vco0.stop(when);
        this.vco1.stop(when);
        this.lfo.stop(when);
    };

    WebAudioHornNode.prototype.disconnect = function () {
        for(var i in this)
            if(this[i].hasOwnProperty('disconnect'))
                this[i].disconnect();
    };

    var ret = New(WebAudioHornNode);

    WebAudioInstructmentNode.registerInstructmentId(4, ret);

    return ret;
});