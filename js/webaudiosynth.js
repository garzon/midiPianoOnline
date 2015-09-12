function WebAudioSynth(synth) {
    var maxdb = -99999;
    var standardFreq = 0;

    for(var i in synth) {
        if(maxdb < synth[i]) {
            maxdb = synth[i];
            standardFreq = i;
        }
    }
    var ret = {};

    for(var i in synth) {
        var amp = Math.pow(10, (synth[i]-maxdb)/10);
        if(amp < 0.005) continue;
        ret[i/standardFreq] = amp;
    }

    return {
        data: ret,
        standardFreq: standardFreq
    };
}