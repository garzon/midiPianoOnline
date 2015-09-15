define(function() {
    function OutputStream() {
        var buffer = '';

        function writeRaw(str) {
            for(var i in str) {
                buffer += String.fromCharCode(str.charCodeAt(i) & 0xFF);
            }
        }

        function writeInt32(int) {
            writeInt16((int & 0xFFFF0000) >> 16);
            writeInt16(int & 0xFFFF);
        }

        function writeInt16(int) {
            writeInt8((int & 0xFF00) >> 8);
            writeInt8(int & 0xFF);
        }

        function writeInt8(int) {
            buffer += String.fromCharCode(int & 0xFF);
        }

        function getOutput() {
            return buffer;
        }

        return {
            writeRaw: writeRaw,
            writeInt32: writeInt32,
            writeInt16: writeInt16,
            writeInt8: writeInt8,
            getOutput: getOutput
        };
    }

    return OutputStream;
});