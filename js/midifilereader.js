function MidiFileReader(raw_data) {
    // This is a wrapper of MidiFile in jasmid.

    var midiFileObj = MidiFile(raw_data);

    midiFileObj.totalTicks = 0;

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
        midiFileObj.totalTicks = Math.max(midiFileObj.totalTicks, absoluteTicks);
    }
    for(var k in lastNoteOnTickAt) {
        var lastinfo = lastNoteOnTickAt[k];
        if(lastinfo) {
            midiFileObj.tracks[lastinfo[0]][lastinfo[1]].lastTime = -1; // no corresponding noteOff event
        }
    }

    return midiFileObj;
}