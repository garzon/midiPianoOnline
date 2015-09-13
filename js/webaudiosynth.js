define(function() {

    function WebAudioSynth(synth) {
        var ret = {};

        var standardFreq = 0;
        var maxdb = -99999999999;
        for (var i in synth) {
            if (synth[i][0] == 0) continue;
            if (maxdb < synth[i][1]) {
                maxdb = synth[i][1];
                standardFreq = synth[i][0];
            }
        }

        var debug_output = '[\n';
        var slope_flag = true;
        //var last_freq = 1;
        for (var i = 1; i < synth.length - 1; i++) {
            if (synth[i][0] == 0) continue;
            var amp = synth[i][1];
            if ((amp < synth[i + 1][1]) != slope_flag) {
                slope_flag = !slope_flag;
                if (amp > 0.01 && amp > synth[i - 1][1] && amp > synth[i + 1][1] && synth[i][0] >= 0.8 * standardFreq && synth[i][0] <= 2.8 * standardFreq) {
                    //if(synth[i][0]/last_freq < Math.pow(2, 5/24)) continue;
                    //last_freq = synth[i][0];
                    ret[synth[i][0] / standardFreq] = [amp, synth[i][2]];
                    if (ret[synth[i][0] / standardFreq][1] < 0) ret[synth[i][0] / standardFreq][1] += Math.PI * 2;
                    debug_output += "[" + synth[i][0] + "," + synth[i][1] + ',' + synth[i][2] + '],\n';
                }
            }
        }
        console.log(debug_output + ']');

        return {
            data: ret,
            standardFreq: standardFreq
        };
    }

    return WebAudioSynth;
});