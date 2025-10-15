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
    $('.col_top_menu').removeClass('no-script');

    $('.col_top_menu ul li').hoverIntent(
        function () {
            $(this).children('ul').show(200);
        },
        function () {
            $(this).children('ul').hide(300);
        }
    );
    if($('.next'))
        $('.next').removeClass('next').addClass('nexts');
    if($('.prev'))
        $('.prev').removeClass('prev').addClass('prevs');

    $('.paginationControl').buttonset();

});

