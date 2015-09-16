define(['OutputStream'], function(OutputStream) {

    MidiEvent = function() {
        this.rawData = '';
    };

    MidiEvent.prototype.setDeltaTime = function(tick) {
        var stream = Stream(this.rawData);
        var deltatimeLen = stream.getRawVarInt().length;
        var outputStream = OutputStream();
        outputStream.writeVarInt(tick);
        this.rawData = outputStream.getOutput() + this.rawData.slice(deltatimeLen, this.rawData.length);
        this.deltaTime = tick;
    };

    return MidiEvent;

});
