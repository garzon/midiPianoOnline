define(function() {

    function WebAudioController() {
        if(WebAudioController._instance !== null)
            return WebAudioController._instance;

        var AudioContext = AudioContext || webkitAudioContext; // for ios/safari
        this.context = new AudioContext();

        this._objs = {};
        this._nodeAtChannel = {};
        WebAudioController._instance = this;
    }

    WebAudioController._instance = null;

    WebAudioController.instance = function() {
        if(WebAudioController._instance !== null) return WebAudioController._instance;
        return new WebAudioController();
    };

    WebAudioController.noteOn = function(channelId, note, velocity, deltatime) {
        WebAudioController.instance()._noteOn(channelId, note, velocity, deltatime);
    };

    WebAudioController.noteOff = function(channelId, note) {
        WebAudioController.instance()._noteOff(channelId, note);
    };

    WebAudioController.mute = function(channelId) {
        WebAudioController.instance()._mute(channelId);
    };

    WebAudioController.setInstructmentSet = function(nodeListObj) {
        WebAudioController.instance()._setInstructmentSet(nodeListObj);
    };

    WebAudioController.setInstructmentNode = function(channelId, id) {
        WebAudioController.instance()._setInstructmentNode(channelId, id);
    };

    WebAudioController.prototype._setInstructmentSet = function(nodeListObj) {
        var minId= 99999, node = undefined;
        for(var i in nodeListObj) {
            if(i < minId) {
                minId = i;
                node = nodeListObj[i];
            }
        }
        if(node !== undefined) {
            this._nodeListObj = nodeListObj;
            WebAudioController.defaultInstructmentNode = node;
        }
    };

    WebAudioController.prototype._setInstructmentNode = function(channelId, id) {
        var node = this._nodeListObj[id];
        if(typeof node == 'undefined') node = WebAudioController.defaultInstructmentNode;
        this._nodeAtChannel[channelId] = node;
    };

    WebAudioController.prototype._indexNote = function(channelId, note) {
        return channelId + 0x100 * note;
    };

    WebAudioController.prototype._noteOn = function(channelId, note, velocity, deltatime) {
        if(!this._nodeAtChannel[channelId])
            this.setInstructmentNode(channelId, WebAudioController.defaultInstructmentNode);

        if(this._objs[this._indexNote(channelId, note)]) this._noteOff(channelId, note);

        var node = this._nodeAtChannel[channelId](this.context, note, velocity / 128.0, channelId);
        node.connect(this.context.destination);
        node.start();

        this._objs[this._indexNote(channelId, note)] = node;

        if(deltatime == -1) {
            var self = this;
            window.mySetTimeout(function() { self._noteOff(channelId, note); }, 1000);
        }
    };

    WebAudioController.prototype._noteOff = function(channelId, note) {
        if(this._objs[this._indexNote(channelId, note)]) {
            this._objs[this._indexNote(channelId, note)].stop();
            this._objs[this._indexNote(channelId, note)].disconnect();
            this._objs[this._indexNote(channelId, note)] = undefined;
        }
    };

    WebAudioController.prototype._mute = function(channelId) {
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

