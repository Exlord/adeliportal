/**
 * Created by Ajami on 10/14/14.
 */
$(document).ready(function () {

    $("#pm_popup_btn").click(function () {
        var tags = $(this);
        tags.parent().addClass('ajax-loading-inline-select');
        $.ajax({
            url: PM.newUrl,
            type: 'GET',
            // data: data,
            complete: function () {
                tags.parent().removeClass('ajax-loading-inline-select');
            },
            success: function (data) {
                $('#pm_popup').html(data);
                $('input[name=to]').parent().hide();
                if (PM.viewType == 'slider') {
                    $('#pm_view_helper_desc').hide();
                    $('#pm_popup').fadeIn(800);
                }
                if (PM.viewType = 'popup') {
                    $("#pm_popup").dialog({
                        resizable: false,
                        height: 400,
                        width: 400,
                        modal: true,
                        title: PM.title
                    });
                }
            },
            error: System.AjaxError
        });

    });

    $('#pm_popup').on('click', '#pm .btn', function (e) {
        e.preventDefault();
        var data = $('#pm').serialize();
        $.ajax({
            url: PM.newUrl,
            type: 'POST',
            data: data,
            complete: function () {
                $('#pm_popup').removeClass('ajax-loading');
            },
            success: function (data) {
                $('#pm_popup').dialog("close");
                if (data.status == 1)
                    System.AjaxMessage(PM.msg);
                else
                    System.AjaxMessage(PM.error);
            },
            error: System.AjaxError
        });
    });

});
