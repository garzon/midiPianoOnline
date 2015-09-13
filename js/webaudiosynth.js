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

    var debug_output = '{\n';

    var list = [];
    for(var i in synth) list = list.concat(i);

    var slope_flag = true;
    for(var i=1; i<list.length-1; i++) {
        var amp = Math.pow(10, (synth[list[i]]-maxdb)/10);
        if((synth[list[i]] < synth[list[i+1]]) != slope_flag) {
            slope_flag = !slope_flag;
            if(amp > 0.001 && synth[list[i]] > synth[list[i-1]] && synth[list[i]] > synth[list[i+1]]) {
                debug_output += "'"+list[i]+"': "+synth[list[i]]+',\n';
                ret[list[i]/standardFreq] = amp;
            }
        }
    }
    console.log(debug_output + '}');
    console.log(standardFreq);

    return {
        data: ret,
        standardFreq: standardFreq
    };
}