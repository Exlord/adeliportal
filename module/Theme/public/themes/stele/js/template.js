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

    $('.col_top_menu li ul').each(function () {
        $(this).parent().addClass('has-child').prepend("<span class='arrow'></span>");
    });
    $('.col_top_menu .navigation > li').each(function () {
        $(this).prepend("<span class='bg-animate'></span>");
    });
    $('.col_top_menu').removeClass('no-script');

    $('.col_top_menu ul li').hover(
        function () {
            if ($(this).children('.bg-animate').length) {
                $(this).children('.bg-animate').slideDown(200);
                $(this).children('a').css('color', '#ffffff');
            }
            $(this).children('ul').show(200);
        },
        function () {
            if ($(this).children('.bg-animate').length){
                $(this).children('.bg-animate').slideUp(150);
                $(this).children('a').css('color', '#0091d0');
            }
            $(this).children('ul').hide(300);
        }
    );

});


$(window).bind("load", function() {
    $('.col_top_menu .navigation').css('visibility','visible').effect("bounce", {times: 7},1000);
});

