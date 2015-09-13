define(function(require) {
    var WebAudioPianoNode = require('WebAudioPianoNode');

    function WebAudioController() {
        if(WebAudioController._instance !== null)
            return WebAudioController._instance;

        var AudioContext = AudioContext || webkitAudioContext; // for ios/safari
        this.context = new AudioContext();

        if (navigator.requestMIDIAccess) {
            navigator.requestMIDIAccess({
                sysex: false
            }).then(onMIDISuccess, function() {
                alert("Load MIDI failed. Please refresh this page.");
            });
        } else {
            alert("No MIDI support in your browser.");
        }

        function onMIDISuccess(midiAccess) {
            /*
             midi = midiAccess;
             var inputs = midi.inputs.values();
             // loop through all inputs
             for (var input = inputs.next(); input && !input.done; input = inputs.next()) {
             // listen for midi messages
             input.value.onmidimessage = onMIDIMessage;
             // this just lists our inputs in the console
             listInputs(input);
             }
             // listen for connect/disconnect message
             midi.onstatechange = onStateChange;*/
        }

        this._objs = {};
        this._nodeAtChannel = {};
        WebAudioController._instance = this;
    }

    WebAudioController._instance = null;

    WebAudioController.defaultInstructmentNode = WebAudioPianoNode;

    WebAudioController.instance = function() {
        if(WebAudioController._instance !== null) return WebAudioController._instance;
        return new WebAudioController();
    };

    WebAudioController.noteOn = function(channelId, note, velocity, deltatime) {
        WebAudioController.instance().noteOn(channelId, note, velocity, deltatime);
    };

    WebAudioController.noteOff = function(channelId, note) {
        WebAudioController.instance().noteOff(channelId, note);
    };

    WebAudioController.mute = function(channelId) {
        WebAudioController.instance().mute(channelId);
    };

    WebAudioController.setInstructmentNode = function(channelId, node) {
        WebAudioController.instance().setInstructmentNode(channelId, node);
    };

    WebAudioController.prototype._indexNote = function(channelId, note) {
        return channelId + 0x100 * note;
    };

    WebAudioController.prototype.noteOn = function(channelId, note, velocity, deltatime) {
        if(!this._nodeAtChannel[channelId])
            this.setInstructmentNode(channelId, WebAudioController.defaultInstructmentNode);

        if(this._objs[this._indexNote(channelId, note)]) this.noteOff(channelId, note);

        var node = this._nodeAtChannel[channelId](this.context, note, velocity / 128.0);
        node.connect(this.context.destination);
        node.start();

        this._objs[this._indexNote(channelId, note)] = node;

        if(deltatime == -1) {
            var self = this;
            window.mySetTimeout(function() { self.noteOff(channelId, note); }, 1000);
        }
    };

    WebAudioController.prototype.setInstructmentNode = function(channelId, node) {
        this._nodeAtChannel[channelId] = node;
    };

    WebAudioController.prototype.noteOff = function(channelId, note) {
        if(this._objs[this._indexNote(channelId, note)]) {
            this._objs[this._indexNote(channelId, note)].stop();
            this._objs[this._indexNote(channelId, note)].disconnect();
            this._objs[this._indexNote(channelId, note)] = undefined;
        }
    };

    WebAudioController.prototype.mute = function(channelId) {
        for(var i in this._objs) {
            if(i % 0x100 == channelId && this._objs[i]) {
                this._objs[i].stop();
                this._objs[i].disconnect();
                this._objs[i] = undefined;
            }
        }
    };

    return WebAudioController;
});

