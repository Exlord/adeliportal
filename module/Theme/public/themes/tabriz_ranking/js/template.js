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

    $('.search-block .block-title').click(function () {
        if ($('body').hasClass('ltr')) {
            if ($('.search-block').hasClass('show')) {
                $('.search-block').animate({left: "-=150"}, 200).removeClass('show');
            } else {
                $('.search-block').animate({left: "+=150"}, 200).addClass('show');
            }
        }else
        {
            if ($('.search-block').hasClass('show')) {
                $('.search-block').animate({right: "-=150"}, 200).removeClass('show');
            } else {
                $('.search-block').animate({right: "+=150"}, 200).addClass('show');
            }
        }
    });


});
