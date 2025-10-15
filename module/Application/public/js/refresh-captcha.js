/**
 * Created by Koushan on 1/27/14.
 */

$('#refreshcaptcha').click(function () {
    var span = $(this);
    span.removeClass('reload-captcha');
    span.addClass('tiny-ajax-loader');
    $.ajax({
        url: captchaRefreshUrl,
        type: 'POST',
        complete: function () {
            span.removeClass('tiny-ajax-loader');
            span.addClass('reload-captcha');
        },
        success: function (data) {
            console.log(data.data);
            $('#captcha-image').attr('src', data.data.src);
            $('#captcha-hidden').attr('value', data.data.id);
        }
    });
});
