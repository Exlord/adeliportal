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
    $('.main-menu a').hoverIntent({
        interval: 25,
        over: function (e) {
            e.stopPropagation();
            var p = $(this).data('padding');
            if (typeof p == 'undefined') {
                p = parseInt($(this).css('padding-right'));
                $(this).data('padding', p );
            }

            $(this).stop().animate({'padding-right': '+=30'}, 300);
        },
        out: function () {
            $(this).stop().animate({'padding-right': $(this).data('padding')}, 300);
        }
    });
});