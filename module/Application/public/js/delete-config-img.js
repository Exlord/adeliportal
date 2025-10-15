$(document).ready(function () {

    $('.image-fav-icon-delete').click(function (e) {
        e.preventDefault();
        var data = {};
        data['favIconUrl'] = $('#favIconUrl').val();

        $.ajax({
            url: urlDeleteFavIcon,
            type: 'POST',
            data: data,
            complete: function () {
            },
            success: function (data) {
                if (data.status) {
                    $('.box-image-fav-icon').hide();
                    $('#favIconUrl').val('');
                }
                else {
                    System.AjaxMessage(data.msg);
                }
            },
            error: System.AjaxError
        });
    });

    $('.image-admin-logo-delete').click(function (e) {
        e.preventDefault();
        var data = {};
        data['adminLogoUrl'] = $('#adminLogoUrl').val();

        $.ajax({
            url: urlDeleteAdminLogo,
            type: 'POST',
            data: data,
            complete: function () {
            },
            success: function (data) {
                if (data.status) {
                    $('.box-image-admin-logo').hide();
                    $('#adminLogoUrl').val('');
                }
                else {
                    System.AjaxMessage(data.msg);
                }
            },
            error: System.AjaxError
        });
    });

});
