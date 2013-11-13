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
    var iMaxFilesize = 10485760;
    // 10MB
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
            alert('上传文件太大');
            return;
        }

        // get preview element

        var oImage = document.getElementById('preview');

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

                //alert(oImage.naturalWidth / oImage.naturalHeight + "," + iwidth / iheight);

                if (oImage.naturalWidth / oImage.naturalHeight >= iwidth / iheight) {
                    if (oImage.naturalWidth > iwidth) {
                        draw_width = iwidth;
                        draw_height = (oImage.naturalHeight * iwidth) / oImage.naturalWidth;
                    } else {
                        draw_width = oImage.naturalWidth;
                        draw_height = oImage.naturalHeight;
                    }
                } else {
                    if (oImage.naturalHeight > iheight) {
                        draw_height = iheight;
                        draw_width = (oImage.naturalWidth * iheight) / oImage.naturalHeight;
                    } else {
                        draw_width = oImage.naturalWidth;
                        draw_height = oImage.naturalHeight;
                    }
                }

                //调整画布大小
                var canvas = document.getElementById('canvas');
                $("#canvas").attr("width", draw_width);
                $("#canvas").attr("height", draw_height);
                var ctx = canvas.getContext("2d");

                ctx.drawImage(oImage, 0, 0, draw_width, draw_height);

                var dataurl = canvas.toDataURL("image/png");
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
