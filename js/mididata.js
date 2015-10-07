define(['OutputStream', 'jasmid-MidiFile'], function(OutputStream, MidiFile) {
    function MidiData(raw_data) {
        // This is a wrapper of MidiFile in jasmid.

        var midiFileObj = MidiFile(raw_data);

        var self;

        function save() {
            var buffer = OutputStream();

            buffer.writeRaw('MThd');
            buffer.writeInt32(6);
            buffer.writeInt16(midiFileObj.header.formatType);
            buffer.writeInt16(midiFileObj.header.trackCount);
            buffer.writeInt16(midiFileObj.header.ticksPerBeat);

            for (var i = 0; i < midiFileObj.header.trackCount; i++) {
                var trackLen = 0;
                for(var j in midiFileObj.tracks[i]) {
                    trackLen += midiFileObj.tracks[i][j].rawData.length;
                }

                buffer.writeRaw('MTrk');
                buffer.writeInt32(trackLen);
                for(var j in midiFileObj.tracks[i]) {
                    buffer.writeRaw(midiFileObj.tracks[i][j].rawData);
                }
            }

            return buffer.getOutput();
        }

        function reload() {
            // calculate absoluteTick for each event and lastTime for each noteOn event
            var lastNoteOnTickAt = {};
            self.totalTicks = 0;

            function noteIdx(channel, note) { return channel * 0x100 + note; }
            function ticksToMs(ticks, beatsPerMinute) {
                var msPerTick = 60000 / (midiFileObj.header.ticksPerBeat * beatsPerMinute);
                return msPerTick * ticks;
            }

            var currentEventAtTrackId = [];
            for(var i=0; i<midiFileObj.tracks.length; i++) {
                var absoluteTicks = 0;
                currentEventAtTrackId = currentEventAtTrackId.concat(-1);
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
                    }
                }
                self.totalTicks = Math.max(self.totalTicks, absoluteTicks);
            }
            for(var k in lastNoteOnTickAt) {
                var lastinfo = lastNoteOnTickAt[k];
                if(lastinfo) {
                    midiFileObj.tracks[lastinfo[0]][lastinfo[1]].lastTime = -1; // no corresponding noteOff event
                }
            }

            self.setTempoEvent = [];
            self.totalTime = 0;
            var absoluteTime = 0, finishFlag = false, beatsPerMinute = 120;
            absoluteTicks = 0;
            while(!finishFlag) {
                var deltatime = 99999999;
                finishFlag = true;
                for(var i=0; i<midiFileObj.tracks.length; i++) {
                    if(currentEventAtTrackId[i]+1 >= midiFileObj.tracks[i].length) continue;
                    finishFlag = false;
                    deltatime = Math.min(deltatime, midiFileObj.tracks[i][currentEventAtTrackId[i]+1].absoluteTicks - absoluteTicks);
                }
                if(finishFlag) break;
                absoluteTicks += deltatime;
                absoluteTime += ticksToMs(deltatime, beatsPerMinute);
                for(var i=0; i<midiFileObj.tracks.length; i++) {
                    if(currentEventAtTrackId[i]+1 >= midiFileObj.tracks[i].length) continue;
                    var event = midiFileObj.tracks[i][currentEventAtTrackId[i]+1];
                    if(event.absoluteTicks === absoluteTicks) {
                        currentEventAtTrackId[i] += 1;
                        event.absoluteTime = absoluteTime;
                        if(event.subtype === 'setTempo') {
                            beatsPerMinute = 60000000 / event.microsecondsPerBeat;
                            self.setTempoEvent = self.setTempoEvent.concat(event);
                        }
                    }
                }
            }
            self.totalTime = absoluteTime;
        }

        function insertEvent(event, trackId, tick, time) {
            event.absoluteTicks = tick;
            event.absoluteTime = time;
            event.lastTime = 1;

            var track = midiFileObj.tracks[trackId];
            var haystack = [];
            for(var i in track) {
                haystack = haystack.concat(track[i].absoluteTicks);
            }
            var idx = binarySearch(haystack, tick, 99999999, 0, track.length-1);

            var lastEvent = undefined, nextEvent = undefined;
            if(idx > 0) lastEvent = track[idx-1];
            if(idx !== -1) nextEvent = track[idx];
            if(idx === -1)
                if(track.length > 1) lastEvent = track[track.length-2];

            var lastEventTick = 0;
            if(nextEvent) nextEvent.setDeltaTime(nextEvent.absoluteTicks - tick);
            if(lastEvent) lastEventTick = lastEvent.absoluteTicks;
            if(idx === -1) { // recorded after "end of track"
                nextEvent = track[track.length-1];
                nextEvent.absoluteTicks = tick+1;
                nextEvent.absoluteTime = time+1;
                nextEvent.setDeltaTime(1);
                idx = track.length-1;
            }

            event.setDeltaTime(tick - lastEventTick);

            midiFileObj.tracks[trackId] =
                track.slice(0, idx)
                    .concat(event)
                    .concat(track.slice(idx, track.length));
        }

        function removeEvent(trackId, tick, note, channel) {
            var track = midiFileObj.tracks[trackId];
            var haystack = [];
            for(var i in track) {
                haystack = haystack.concat(track[i].absoluteTicks);
            }
            var idx = binarySearch(haystack, tick, 99999999, 0, track.length-1);
            if (idx !== -1) console.log(idx, track[idx]);
            while(idx !== -1 && idx < track.length && track[idx].absoluteTicks === tick && (track[idx].channel !== channel || track[idx].noteNumber !== note)) {
                idx++;
            }
            if(idx === -1 || idx >= track.length || track[idx].absoluteTicks !== tick || track[idx].channel !== channel || track[idx].noteNumber !== note) {
                console.warn('remove - event not found: ', trackId, tick, note, channel);
                return;
            }

            var lastEvent = undefined, nextEvent = undefined;
            if(idx > 0) lastEvent = track[idx-1];
            if(idx !== track.length-1) nextEvent = track[idx+1];

            var event = track[idx];

            if(nextEvent) {
                if(lastEvent)
                    nextEvent.setDeltaTime(nextEvent.absoluteTicks - lastEvent.absoluteTicks);
                else nextEvent.setDeltaTime(nextEvent.absoluteTicks);
            }

            midiFileObj.tracks[trackId] = track.slice(0, idx).concat(track.slice(idx+1, track.length));

            return event;
        }

        self = {
            header: midiFileObj.header,
            tracks: midiFileObj.tracks,
            save: save,
            reload: reload,
            insertEvent: insertEvent,
            removeEvent: removeEvent
        };

        return self;
    }

    MidiData.loadRemoteMidi = function(path, callback, failcallback) {
        function string2binary(str) {
            var ret = [];
            console.log(str.length);
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
                try {
                    var ret = MidiData(string2binary(data));
                } catch(e) {
                    if(typeof failcallback === 'function') failcallback(e);
                    return;
                }
                callback(ret);
            }
        };
        fetch.send();
    };

    return MidiData;
});
