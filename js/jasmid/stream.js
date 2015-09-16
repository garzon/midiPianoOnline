/* Wrapper for accessing strings through sequential reads */
define(function() {
    function Stream(str) {
        var position = 0;
        var outputBuffer = undefined;

        function read(length) {
            if(outputBuffer) outputBuffer.rawData += getRaw(length);
            var result = str.substr(position, length);
            position += length;
            return result;
        }

        function getRaw(length) {
            return str.substr(position, length);
        }

        /* read a big-endian 32-bit integer */
        function readInt32() {
            if(outputBuffer) outputBuffer.rawData += getRawInt32();
            var result = (
            (str.charCodeAt(position) << 24)
            + (str.charCodeAt(position + 1) << 16)
            + (str.charCodeAt(position + 2) << 8)
            + str.charCodeAt(position + 3));
            position += 4;
            return result;
        }

        function getRawInt32() {
            return str.substr(position, 4);
        }

        /* read a big-endian 16-bit integer */
        function readInt16() {
            if(outputBuffer) outputBuffer.rawData += getRawInt16();
            var result = (
            (str.charCodeAt(position) << 8)
            + str.charCodeAt(position + 1));
            position += 2;
            return result;
        }

        function getRawInt16() {
            return str.substr(position, 2);
        }

        /* read an 8-bit integer */
        function readInt8(signed) {
            if(outputBuffer) outputBuffer.rawData += getRawInt8();
            var result = str.charCodeAt(position);
            if (signed && result > 127) result -= 256;
            position += 1;
            return result;
        }

        function getRawInt8() {
            return str.substr(position, 1);
        }

        function eof() {
            return position >= str.length;
        }

        /* read a MIDI-style variable-length integer
         (big-endian value in groups of 7 bits,
         with top bit set to signify that another byte follows)
         */
        function readVarInt() {
            var result = 0;
            while (true) {
                var b = readInt8();
                if (b & 0x80) {
                    result += (b & 0x7f);
                    result <<= 7;
                } else {
                    /* b is the last byte */
                    return result + b;
                }
            }
        }

        function getRawVarInt() {
            var result = '';
            var p = position;
            while (true) {
                var char = str.substr(p, 1);
                var b = char.charCodeAt(0);
                p += 1;
                result += char;
                if (b & 0x80) {
                    // nothing
                } else {
                    /* b is the last byte */
                    return result;
                }
            }
        }

        function setOutputBuffer(buffer) {
            outputBuffer = buffer;
        }

        return {
            'eof': eof,
            'read': read,
            'readInt32': readInt32,
            'readInt16': readInt16,
            'readInt8': readInt8,
            'readVarInt': readVarInt,
            getRaw: getRaw,
            getRawInt8: getRawInt8,
            getRawInt16: getRawInt16,
            getRawInt32: getRawInt32,
            getRawVarInt: getRawVarInt,
            setOutputBuffer: setOutputBuffer
        };
    }

    return Stream;
});

