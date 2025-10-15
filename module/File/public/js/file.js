$(document).ready(function () {

    $('body').on('click', ".fileDelete", function (e) {
        e.preventDefault();
        var tag = $(this);
        $('.box-file-collection').addClass('ajax-loading');
        var classItem = tag;
        $.ajax({
            type: "GET",
            url: tag.data('src'),
            complete: function () {
                $('.box-file-collection').removeClass('ajax-loading');
            },
            success: function (msg) {
                if (msg.status) {
                    classItem.parent().next().next().attr('value', '');
                    classItem.parent().remove();
                }
                else
                    System.AjaxMessage(error_message);
            }
        })
    });

});