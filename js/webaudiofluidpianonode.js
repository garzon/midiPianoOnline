define(['WebAudioInstructmentNode'], function(WebAudioInstructmentNode) {

    var audioctx = new AudioContext();

    var buffer = {};

    function loadSample(ctx, note, callback) {
        var url = '/midiPianoOnline/sf/fluid/piano/' + noteToName(note) + '.mp3';
        var req = new XMLHttpRequest();
        req.open("GET", url, true);
        req.responseType = "arraybuffer";
        req.onload = function() {
            if(req.response) {
                ctx.decodeAudioData(req.response, function(b) {
                    buffer[note] = b;
                    callback();
                });
            }
        };
        req.send();
    }

    function WebAudioFluidPianoNode(context, note, volume, channelId) {
        this.src = context.createBufferSource();
        this.src.buffer = buffer[note];

        this.envelope = context.createGain();
        this.envelope.gain.value = volume * 8;

        this.src.connect(this.envelope);
    }

    WebAudioFluidPianoNode.prototype.connect = function (webAudioNode) {
        this.envelope.connect(webAudioNode);
    };

    WebAudioFluidPianoNode.prototype.start = function (when) {
        if (typeof when == 'undefined') when = 0;
        this.src.start(when);
    };

    WebAudioFluidPianoNode.prototype.stop = function (when) {
        if (typeof when == 'undefined') when = 0;
        this.src.stop(when);
    };

    WebAudioFluidPianoNode.prototype.disconnect = function () {
        this.src.disconnect();
        this.envelope.disconnect();
    };

    var counter = 0;
    for(var i=21; i<108; i++) {
        loadSample(audioctx, i, function() {
            counter ++;
        });
    }

    var ret = New(WebAudioFluidPianoNode);

    WebAudioInstructmentNode.registerInstructmentId(0, ret);

    return ret;
});