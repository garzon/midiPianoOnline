define(function(){
    function MidiData(raw_data) {
        // This is a wrapper of MidiFile in jasmid.

        var midiFileObj = MidiFile(raw_data);

        var totalTicks = 0;

        // calculate absoluteTick for each event and lastTime for each noteOn event
        var lastNoteOnTickAt = {};
        function noteIdx(channel, note) { return channel * 0x100 + note; }
        for(var i=0; i<midiFileObj.tracks.length; i++) {
            var absoluteTicks = 0;
            for(var j=0; j<midiFileObj.tracks[i].length; j++) {
                var event = midiFileObj.tracks[i][j];
                absoluteTicks += event.deltaTime;
                event.absoluteTicks = absoluteTicks;

                var lastinfo = lastNoteOnTickAt[noteIdx(event.channel, event.noteNumber)];
                switch (event.subtype) {
                    case 'noteOn':
                        if(lastinfo) {
                            midiFileObj.tracks[lastinfo[0]][lastinfo[1]].lastTime = absoluteTicks - midiFileObj.tracks[lastinfo[0]][lastinfo[1]].absoluteTicks;
                        }
                        lastNoteOnTickAt[noteIdx(event.channel, event.noteNumber)] = [i, j];
                        break;
                    case 'noteOff':
                        if(lastinfo) {
                            midiFileObj.tracks[lastinfo[0]][lastinfo[1]].lastTime = absoluteTicks - midiFileObj.tracks[lastinfo[0]][lastinfo[1]].absoluteTicks;
                            lastNoteOnTickAt[noteIdx(event.channel, event.noteNumber)] = undefined;
                        }
                        break;
                }
            }
            totalTicks = Math.max(totalTicks, absoluteTicks);
        }
        for(var k in lastNoteOnTickAt) {
            var lastinfo = lastNoteOnTickAt[k];
            if(lastinfo) {
                midiFileObj.tracks[lastinfo[0]][lastinfo[1]].lastTime = -1; // no corresponding noteOff event
            }
        }

        return {
            header: midiFileObj.header,
            tracks: midiFileObj.tracks,
            totalTicks: totalTicks
        };
    }

    MidiData.loadRemoteMidi = function(path, callback) {
        function string2binary(str) {
            var ret = [];
            for(var i=0; i<str.length; i++) {
                ret = ret.concat(String.fromCharCode(str.charCodeAt(i) & 0xff));
            }
            return ret.join('');
        }

        var fetch = new XMLHttpRequest();
        fetch.open('GET', path);
        fetch.overrideMimeType("text/plain; charset=x-user-defined");
        fetch.onreadystatechange = function() {
            if(this.readyState == 4 && this.status == 200) {
                var data = this.responseText || "" ;
                callback(new MidiData(string2binary(data)));
            }
        };
        fetch.send();
    };

    return MidiData;
});
