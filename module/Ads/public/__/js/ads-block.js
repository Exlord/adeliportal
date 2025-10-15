$(document).ready(function () {
    $('body').on('change', '#data_ads_block__baseType', function () {
        var tag = $(this);
        tag.addClass('ajax-loading-inline-select');
        var baseType = $(this).val();
        var data = {};
        data['baseType'] = baseType;
        $.ajax({
            url: $('#data_ads_block__url').val(),
            type: 'POST',
            data: data,
            complete: function () {
                tag.removeClass('ajax-loading-inline-select');
            },
            success: function (data) {
                if (data.status) {
                    $('#data_ads_block__secondType').html(data.secondType);
                    $('#data_ads_block__starCount').html(data.starCountArray);
                }
            },
            error: System.AjaxError
        });
    });
});