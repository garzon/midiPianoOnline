WebAudioChannel = function(idx) {
    this.channelId = idx;
};

WebAudioChannel.prototype.noteOn = function(note, velocity, deltatime) {
    WebAudioController.noteOn(this.channelId, note, velocity, deltatime);
};

WebAudioChannel.prototype.noteOff = function(note, velocity) {
    WebAudioController.noteOff(this.channelId, note, velocity);
};

WebAudioChannel.prototype.setProgram = function(id) {

};