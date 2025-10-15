/**
 * Created with PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/28/14
 * Time: 1:31 PM
 */
if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
    var msViewportStyle = document.createElement("style");
    msViewportStyle.appendChild(
        document.createTextNode(
            "@-ms-viewport{width:auto!important}"
        )
    );
    document.getElementsByTagName("head")[0].appendChild(msViewportStyle);
}
$(document).ready(function () {

    $('#user_login_link').click(function (e) {
        e.preventDefault();
    });
    $('.user_login_box').click(function (e) {
        if ($(this).hasClass('opened'))
            $(this).removeClass('opened');
        else
            $(this).addClass('opened');
        $('#user_login_popup').slideToggle();
    });

    /*$('.language-switcher-box li').hoverIntent(
        function () {
            $(this).children('ul').show().effect('bounce', {}, 300);
        },
        function () {
            $(this).children('ul').fadeOut(200);
        }
    );*/




});