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

                //调整画布大小
                var canvas = document.getElementById('canvas');
                var ctx = canvas.getContext("2d");
                ctx.save();

                // 重置canvas宽高
                canvas.width = draw_width;
                canvas.height = draw_height;
                //alert("5,h:"+draw_height+",w:"+draw_width);
                //ctx.drawImage(oImage, 0, 0, draw_width, draw_height);
                var vertsquashratio = detectverticalsquash(oImage, oImage.height);

                ctx.drawImage(oImage, 0, 0, oImage.width, oImage.height, 0, 0, draw_width, draw_height/vertsquashratio);

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
