$(document).ready(function () {

    $('.quick-send-mail-button').click(function (e) {
        e.preventDefault();
        var countError = 0;
        countError += checkValue($('#quick_send_mail_name'));
        countError += checkValue($('#quick_send_mail_email'));
        countError += checkValue($('#quick_send_mail_text'));
        countError += checkValue($('.captcha'));
        if (countError == 0) {
            var tags = $(this);
            tags.addClass('ajax-loading-inline');
            var data = $('#quick_send_mail').serialize();
            $.ajax({
                url: url_quick_send_mail,
                type: 'POST',
                data: data,
                complete: function () {
                    tags.removeClass('ajax-loading-inline');
                },
                success: function (data) {
                    if (data.status == 1)
                        System.AjaxMessage('Success');
                    else
                        System.AjaxMessage('Error : Do not Send email');
                    $('#divQuickSendMail').html(data.html);
                }
            });
        }

    });

});

function checkValue(tag) {
    if (tag.val()) {
        tag.removeClass('error-empty-value');
        return 0;
    } else {
        tag.addClass('error-empty-value');
        return 1;
    }
}
