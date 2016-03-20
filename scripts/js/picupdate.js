$(document).ready(function() {

    $("#dialog").dialog({
        // height: 500,
        width: 600,
        resizable: false,
        modal: true,
        autoOpen: false,
    });

    $("#image").change(function() {
        $("#preview-avatar").html('');
        $("#preview-avatar").html('Uploading...');
        $("#photoForm").ajaxForm({
            target: '#preview-avatar',
            success: function() {
                $("#dialog").dialog('option', 'buttons', {
                    'Crop & Save': function(event) {                        
                        var params = {
                                targetUrl: 'upload.php?action=save',
                                   action: 'save',
                                   x_axis: $("#hdn-x1-axis").val(),
                                   y_axis: $("#hdn-y1-axis").val(),
                                  x2_axis: $("#hdn-x2-axis").val(),
                                  y2_axis: $("#hdn-y2-axis").val(),
                              thumb_width: $("#hdn-thumb-width").val(),
                             thumb_height: $("#hdn-thumb-height").val()
                            };
                        saveCropImage(params);
                        $(this).dialog("close");
                    },
                    Cancel: function() {
                        $(this).dialog("close");
                    }
                });
                $("#dialog").dialog("open"); 
                $("img#photo").imgAreaSelect({
                    aspectRatio: '1:1',
                    onSelectEnd: getSizes,
                         parent: $("#dialog"),
                });
                $("#image_name").val($("#photo").attr('file-name'));
            }
        }).submit();
    });


    function getSizes(img, obj) {
        var x_axis       = obj.x1;
        var x2_axis      = obj.x2;
        var y_axis       = obj.y1;
        var y2_axis      = obj.y2;
        var thumb_width  = obj.width;
        var thumb_height = obj.height;

        if (thumb_width > 0) {
            $("#hdn-x1-axis").val(x_axis);
            $("#hdn-x2-axis").val(x2_axis);
            $("#hdn-y1-axis").val(y_axis);
            $("#hdn-y2-axis").val(y2_axis);
            $("#hdn-thumb-width").val(thumb_width);
            $("#hdn-thumb-height").val(thumb_height);
        }
        else {
            alert("Please select an area to crop.");
        }
    }

    function saveCropImage(params) {
        $.ajax({
                 url: params['targetUrl'],
               cache: false,
            dataType: "html",
                data: {
                    action: params['action'],
                        id: $("#hdn-profile-id").val(),
                      type: 'ajax',
                        w1: params['thumb_width'],
                        h1: params['thumb_height'],
                        x1: params['x_axis'],
                        x2: params['x2_axis'],
                        y1: params['y_axis'],
                        y2: params['y2_axis'],
                image_name: $("#image_name").val()
                },
                type: 'POST',
                success: function(response) {
                    $(".imgareaselect-border1,.imgareaselect-border2,.imgareaselect-border3,.imgareaselect-border4,.imgareaselect-border2,.imgareaselect-outer").css('display', 'none');
                    $("#currentavatar").attr('src', response);
                    $("#photoForm").val('');
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert('Status Code: ' + xhr.status + ' Error Message: ' + thrownError);
                }
        });
    }
});