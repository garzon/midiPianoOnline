define(function() {

    function WebMidiInstructmentNode(context, note, volume, channelId) {
        var self = this;
        this._startFunc = function() {
            WebMidiInstructmentNode.midi_output.sendRawMessage([0xC0+channelId, self.instructmentId]);
            WebMidiInstructmentNode.midi_output.sendRawMessage([0x90+channelId, note, Math.round(volume*127)]);
        };
        this._stopFunc = function() {
            WebMidiInstructmentNode.midi_output.sendRawMessage([0x80+channelId, note, 0]);
        };
        this.instructmentId = 0;
    }

    WebMidiInstructmentNode.midi_output = undefined;

    WebMidiInstructmentNode.prototype.connect = function (webAudioNode) {
        // nothing happened
    };

    var sleepAndCall = function(delay, callback) {
        if(delay > 0) window.setTimeout(callback, delay*1000);
        else callback();
    };

    WebMidiInstructmentNode.prototype.start = function (when) {
        if (typeof when == 'undefined') when = 0;
        sleepAndCall(when, this._startFunc);
    };

    WebMidiInstructmentNode.prototype.stop = function (when) {
        if (typeof when == 'undefined') when = 0;
        sleepAndCall(when, this._stopFunc);
    };

    WebMidiInstructmentNode.prototype.disconnect = function () {
        // nothing happened
    };

    WebMidiInstructmentNode.instructmentSet = {};

    WebMidiInstructmentNode.registerInstructmentId = function(id, node) {
        WebMidiInstructmentNode.instructmentSet[id] = node;
    };

    for(var i=0; i<128; i++) {
        var node = (function(j) {
            return function(context, note, volume, channelId) {
                var ret = new WebMidiInstructmentNode(context, note, volume, channelId);
                ret.instructmentId = j;
                return ret;
            };
        })(i);

        WebMidiInstructmentNode.registerInstructmentId(i, node);
    }

    return WebMidiInstructmentNode;
});