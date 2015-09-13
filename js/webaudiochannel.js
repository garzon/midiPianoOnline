define(['WebAudioController'], function(WebAudioController) {
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

    WebAudioChannel.prototype.mute = function() {
        WebAudioController.mute(this.channelId);
    };

    return WebAudioChannel;
});