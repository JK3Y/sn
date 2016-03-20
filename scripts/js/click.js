// ENSURES THAT THE DOM OBJECT IS FULLY LOADED BEFORE EXECUTING CODE
$(document).ready(function() {

    $("#dialog").dialog({
        height: 250,
        width: 450,
        resizable: false,
        modal: true,
        autoOpen: false
    });

    $(".btnFollow").click(function() {
        var userid = $(this).attr("id");
        $.ajax({
            type: 'POST',
            url: 'scripts/php/follow.php',
            data: 'userid=' + userid,
            dataType: 'json',
            encode: true
        })
        .done(function(data) {
            $('.follow' + userid).hide();
            $('.unfollow' + userid).show();
        })
        .fail(function(data) {
            alert('Could not follow user. Please try again.');

        });
    });

    $(".btnUnfollow").click(function() {
        var userid = $(this).attr("id");
        $.ajax({
            type: 'POST',
            url: 'scripts/php/unfollow.php',
            data: 'userid=' + userid,
            dataType: 'json',
            encode: true
        })
        .done(function(data) {
            $('.follow' + userid).show();
            $('.followdiv' + userid).slideUp();
            $('.unfollow' + userid).hide();
        })
        .fail(function(data) {
            alert('Could not unfollow user. Please try again.');
        });
    });


    $(".btnDelete").click(function() {
        var postid = $(this).attr("id");
        $("#dialog").dialog('option', 'buttons', {
            Yes: function() {
                $.ajax({
                    type: 'POST',
                    url: 'scripts/php/delete.php',
                    data: 'postid=' + postid,
                    dataType: 'json',
                    encode: true
                })
                .done(function(data) {
                    console.log(data);
                    if (data.success == true) {
                        $('.post_' + postid).slideUp();
                    }
                    else {
                        alert('Deletion failed.');
                    }
                })
                .fail(function(data) {
                    alert('Could not delete this post. Please try again.');
                });
                $(this).dialog("close");
            },
            No: function() {
                $(this).dialog("close");
            }
        });
        $("#dialog").dialog("open"); 
    });


});