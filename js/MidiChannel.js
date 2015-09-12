MidiChannel = function(idx) {
    this.channelId = idx;
};

MidiChannel.prototype.noteOn = function(note, velocity, deltatime) {
    MidiOutput.noteOn(this.channelId, note, velocity, deltatime);
};

MidiChannel.prototype.noteOff = function(note, velocity) {
    MidiOutput.noteOff(this.channelId, note, velocity);
};

MidiChannel.prototype.setProgram = function(id) {

};