$(document).ready(function () {
    $('#newsletter_sign_up #submit-save').click(function (e) {
        e.preventDefault();
        var tag = $(this);
        tag.addClass('ajax-loading-inline');
        var data = $('#newsletter_sign_up').serialize();
        $.ajax({
            url: NewsLetter.signUpUrl,
            type: 'post',
            data: data,
            complete: function () {
                tag.removeClass('ajax-loading-inline');
            },
            success: function (data) {
                $('#newsLetter_signUp_form').html(data);
            },
            error: System.AjaxError
        });
    })
});
