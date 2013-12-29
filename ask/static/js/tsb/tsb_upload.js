$(document).ready(function() {

    $('#image_file').change(function() {
        fileSelected();
    });

    $('#btn_image').click(function() {
        $('#image_file').click();
    });

    // common variables
    var iBytesUploaded = 0;
    var iBytesTotal = 0;
    var iPreviousBytesLoaded = 0;
    var iMaxFilesize = 1048576 * 2;
    // 2MB
    var oTimer = 0;
    var sResultFileSize = '';
    
    var base64decode=function(input) {
        var output = '';
        var chr1, chr2, chr3 = '';
        var enc1, enc2, enc3, enc4 = '';
        var i = 0;
        if (input.length%4!=0){
            return '';
        }
        var base64test = /[^A-Za-z0-9\+\/\=]/g;
        if (base64test.exec(input)){
            return '';
        }
        do {
            enc1 = this._keys.indexOf(input.charAt(i++));
            enc2 = this._keys.indexOf(input.charAt(i++));
            enc3 = this._keys.indexOf(input.charAt(i++));
            enc4 = this._keys.indexOf(input.charAt(i++));
            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;
            output = output + String.fromCharCode(chr1);
            if (enc3 != 64){
                output+=String.fromCharCode(chr2);
            }
            if (enc4 != 64){
                output+=String.fromCharCode(chr3);
            }
            chr1 = chr2 = chr3 = '';
            enc1 = enc2 = enc3 = enc4 = '';
        } while (i < input.length);
        return output;
    };

    //判断图片方向
    var getorientation = function(binfile) {
        function getbyteat(offset) {
            return binfile.charCodeAt(offset) & 0xFF;
        }

        function getbytesat(offset, length) {
            var bytes = [];
            for (var i = 0; i < length; i++) {
                bytes[i] = binfile.charCodeAt((offset + i)) & 0xFF;
            }
            return bytes;
        }

        function getshortat(offset, bigendian) {
            var shortat = bigendian ? (getbyteat(offset) << 8) + getbyteat(offset + 1) : (getbyteat(offset + 1) << 8) + getbyteat(offset);
            if (shortat < 0) {
                shortat += 65536;
            }
            return shortat;
        }

        function getlongat(offset, bigendian) {
            var byte1 = getbyteat(offset);
            var byte2 = getbyteat(offset + 1);
            var byte3 = getbyteat(offset + 2);
            var byte4 = getbyteat(offset + 3);
            var longat = bigendian ? (((((byte1 << 8) + byte2) << 8) + byte3) << 8) + byte4 : (((((byte4 << 8) + byte3) << 8) + byte2) << 8) + byte1;
            if (longat < 0)
                longat += 4294967296;
            return longat;
        }

        function getslongat(offset, bigendian) {
            var ulongat = getlongat(offset, bigendian);
            if (ulongat > 2147483647) {
                return ulongat - 4294967296;
            } else {
                return ulongat;
            }
        }

        function getstringat(offset, length) {
            var str = [];
            var bytes = getbytesat(offset, length);
            for (var i = 0; i < length; i++) {
                str[i] = String.fromCharCode(bytes[i]);
            }
            return str.join('');
        }

        function readtagvalue(entryoffset, tiffstart, dirstart, bigend) {
            var type = getshortat(entryoffset + 2, bigend);
            var numvalues = getlongat(entryoffset + 4, bigend);
            var valueoffset = getlongat(entryoffset + 8, bigend) + tiffstart;
            var offset, vals;
            switch(type) {
                case 1:
                case 7:
                    if (numvalues == 1) {
                        return getbyteat(entryoffset + 8, bigend);
                    } else {
                        offset = numvalues > 4 ? valueoffset : (entryoffset + 8);
                        vals = [];
                        for (var n = 0; n < numvalues; n++) {
                            vals[n] = getbyteat(offset + n);
                        }
                        return vals;
                    }
                case 2:
                    offset = numvalues > 4 ? valueoffset : (entryoffset + 8);
                    return getstringat(offset, numvalues - 1);
                case 3:
                    if (numvalues == 1) {
                        return getshortat(entryoffset + 8, bigend);
                    } else {
                        offset = numvalues > 2 ? valueoffset : (entryoffset + 8);
                        vals = [];
                        for (var n = 0; n < numvalues; n++) {
                            vals[n] = getshortat(offset + 2 * n, bigend);
                        }
                        return vals;
                    }
                case 4:
                    if (numvalues == 1) {
                        return getlongat(entryoffset + 8, bigend);
                    } else {
                        vals = [];
                        for (var n = 0; n < numvalues; i++) {
                            vals[n] = getlongat(valueoffset + 4 * n, bigend);
                        }
                        return vals;
                    }
                case 5:
                    if (numvalues == 1) {
                        var numerator = getlongat(valueoffset, bigend);
                        var denominator = getlongat(valueoffset + 4, bigend);
                        var val = new Number(numerator / denominator);
                        val.numerator = numerator;
                        val.denominator = denominator;
                        return val;
                    } else {
                        vals = [];
                        for (var n = 0; n < numvalues; n++) {
                            var numerator = getlongat(valueoffset + 8 * n, bigend);
                            var denominator = getlongat(valueoffset + 4 + 8 * n, bigend);
                            vals[n] = new Number(numerator / denominator);
                            vals[n].numerator = numerator;
                            vals[n].denominator = denominator;
                        }
                        return vals;
                    }
                case 9:
                    if (numvalues == 1) {
                        return getslongat(entryoffset + 8, bigend);
                    } else {
                        vals = [];
                        for (var n = 0; n < numvalues; n++) {
                            vals[n] = getslongat(valueoffset + 4 * n, bigend);
                        }
                        return vals;
                    }
                case 10:
                    if (numvalues == 1) {
                        return getslongat(valueoffset, bigend) / getslongat(valueoffset + 4, bigend);
                    } else {
                        vals = [];
                        for (var n = 0; n < numvalues; n++) {
                            vals[n] = getslongat(valuesoffset + 8 * n, bigend) / getslongat(valueoffset + 4 + 8 * n, bigend);
                        }
                        return vals;
                    }
            }
        }

        function readtags(tiffstart, dirstart, strings, bigend) {
            var entries = getshortat(dirstart, bigend);
            var tags = {}, entryofffset, tag;
            for (var i = 0; i < entries; i++) {
                entryoffset = dirstart + i * 12 + 2;
                tag = strings[getshortat(entryoffset, bigend)];
                tags[tag] = readtagvalue(entryoffset, tiffstart, dirstart, bigend);
            }
            return tags;
        }

        function readexifdata(start) {
            if (getstringat(start, 4) != 'Exif') {
                return false;
            }
            var bigend;
            var tags, tag;
            var tiffoffset = start + 6;
            if (getshortat(tiffoffset) == 0x4949) {
                bigend = false;
            } else if (getshortat(tiffoffset) == 0x4D4D) {
                bigend = true;
            } else {
                return false;
            }
            if (getshortat(tiffoffset + 2, bigend) != 0x002A) {
                return false;
            }
            if (getlongat(tiffoffset + 4, bigend) != 0x00000008) {
                return false;
            }
            var tifftags = {
                0x0112 : "Orientation"
            };
            tags = readtags(tiffoffset, tiffoffset + 8, tifftags, bigend);
            return tags;
        }

        if (getbyteat(0) != 0xFF || getbyteat(1) != 0xD8) {
            return false;
        }
        var offset = 2;
        var length = binfile.length;
        var marker;
        while (offset < length) {
            if (getbyteat(offset) != 0xFF) {
                return false;
            }
            marker = getbyteat(offset + 1);
            if (marker == 22400 || marker == 225) {
                return readexifdata(offset + 4);
            } else {
                offset += 2 + getshortat(offset + 2, true);
            }
        }
    };

    function fileSelected() {

        // get selected file element
        var oFile = document.getElementById('image_file').files[0];

        // filter for image files
        var rFilter = /^(image\/bmp|image\/gif|image\/jpeg|image\/png|image\/tiff)$/i;
        if (! rFilter.test(oFile.type)) {
            alert('上传文件不是图片格式');
            return;
        }

        // little test for filesize
        if (oFile.size > iMaxFilesize) {
            //alert('上传文件太大');
            //return;
        }

        // get preview element

        var oImage = document.getElementById('preview');
        //var oImage = new Image();

        //定义允许图片宽度，当宽度大于这个值时等比例缩小
        var iwidth = 280;
        //定义允许图片高度，当宽度大于这个值时等比例缩小
        var iheight = 210;

        // prepare HTML5 FileReader
        var oReader = new FileReader();
        oReader.onload = function(e) {

            // e.target.result contains the DataURL which we will use as a source of the image
            oImage.src = e.target.result;

            oImage.onload = function() {

                var draw_width = iwidth;
                var draw_height = iheight;

                //alert(oImage.width + "," + oImage.height + "," + oImage.width / oImage.height + "," + iwidth / iheight);

                if (oImage.width / oImage.height > iwidth / iheight && oImage.width >= iwidth) {
                    draw_width = iwidth;
                    draw_height = Math.ceil(iwidth / oImage.width * oImage.height);
                } else if (oImage.width / oImage.height <= iwidth / iheight && oImage.height >= iheight) {
                    draw_height = iheight;
                    draw_width = Math.ceil(iheight / oImage.height * oImage.width);
                }

                //判断压缩比
                var detectverticalsquash = function(img, imgheight) {
                    var tmpcanvas = document.createElement('canvas');
                    tmpcanvas.width = 1;
                    tmpcanvas.height = imgheight;
                    var tmpctx = tmpcanvas.getContext('2d');
                    tmpctx.drawImage(img, 0, 0);
                    var data = tmpctx.getImageData(0, 0, 1, imgheight).data;
                    var sy = 0;
                    var ey = imgheight;
                    var py = imgheight;
                    while (py > sy) {
                        var alpha = data[(py - 1) * 4 + 3];
                        if (alpha === 0) {
                            ey = py;
                        } else {
                            sy = py;
                        }
                        py = (ey + sy) >> 1;
                    }
                    var ratio = py / imgheight;
                    return (ratio === 0) ? 1 : ratio;
                };

                //旋转画布（自动旋转照片）
                var transformcoordinate = function(canvas, ctx, width, height, orientation) {
                    switch(orientation) {
                        case 5:
                        case 6:
                        case 7:
                        case 8:
                            canvas.width = height;
                            canvas.height = width;
                            break;
                        default:
                            canvas.width = width;
                            canvas.height = height;
                    }
                    switch(orientation) {
                        case 2:
                            ctx.translate(width, 0);
                            ctx.scale(-1, 1);
                            break;
                        case 3:
                            ctx.translate(width, height);
                            ctx.rotate(Math.PI);
                            break;
                        case 4:
                            ctx.translate(0, height);
                            ctx.scale(1, -1);
                            break;
                        case 5:
                            ctx.rotate(0.5 * Math.PI);
                            ctx.scale(1, -1);
                            break;
                        case 6:
                            ctx.rotate(0.5 * Math.PI);
                            ctx.translate(0, -height);
                            break;
                        case 7:
                            ctx.rotate(0.5 * Math.PI);
                            ctx.translate(width, -height);
                            ctx.scale(-1, 1);
                            break;
                        case 8:
                            ctx.rotate(-0.5 * Math.PI);
                            ctx.translate(-width, 0);
                            break;
                    }
                };

                //调整画布大小
                var canvas = document.getElementById('canvas');
                var ctx = canvas.getContext("2d");
                ctx.save();

                // 重置canvas宽高
                canvas.width = draw_width;
                canvas.height = draw_height;

                var imgfilebinary = oImage.src.replace(/data:.+;base64,/, '');
                if ( typeof atob == 'function') {
                    imgfilebinary = atob(imgfilebinary);
                } else {
                    imgfilebinary = base64decode(imgfilebinary);
                }
                var orientation = getorientation(imgfilebinary);
                orientation = orientation.Orientation;

                var vertsquashratio = detectverticalsquash(oImage, oImage.height);
                
                transformcoordinate(canvas, ctx, draw_width, draw_height, orientation);

                ctx.drawImage(oImage, 0, 0, oImage.width, oImage.height, 0, 0, draw_width, draw_height / vertsquashratio);

                ctx.restore();

                var dataurl = canvas.toDataURL("image/png");
                //$("#preview").src=e.target.result;
                $("#preview").css("display", "block");
                //document.getElementById('preview').style.display = 'block';
                $('#image_data').val(dataurl);
                $('#_is_attach').val('1');
                $('#image_name').val($('#image_file').val());

            };
        };

        // read selected file as DataURL
        oReader.readAsDataURL(oFile);
    }

});
