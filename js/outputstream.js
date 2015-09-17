define(function() {
    function OutputStream() {
        var buffer = '';
        var self;

        function writeRaw(str) {
            for(var i in str) {
                buffer += String.fromCharCode(str.charCodeAt(i) & 0xFF);
            }
            return self;
        }

        function writeVarInt(int) {
            var res = [];
            while (int !== 0) {
                var thisByte = int & 0x7F;
                int = int >>> 7;
                res.push(thisByte);
            }
            res.reverse();
            var i;
            for(i=0; i<res.length-1; i++) writeInt8(res[i] | 0x80);
            writeInt8(res[i]);
            return self;
        }

        function writeInt32(int) {
            writeInt16((int & 0xFFFF0000) >>> 16);
            writeInt16(int & 0xFFFF);
            return self;
        }

        function writeInt16(int) {
            writeInt8((int & 0xFF00) >>> 8);
            writeInt8(int & 0xFF);
            return self;
        }

        function writeInt8(int) {
            buffer += String.fromCharCode(int & 0xFF);
            return self;
        }

        function getOutput() {
            return buffer;
        }

        self = {
            writeVarInt: writeVarInt,
            writeRaw: writeRaw,
            writeInt32: writeInt32,
            writeInt16: writeInt16,
            writeInt8: writeInt8,
            getOutput: getOutput
        };

        return self;
    }

    return OutputStream;
});