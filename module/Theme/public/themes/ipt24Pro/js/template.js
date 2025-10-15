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
    $('.main-menu ul.navigation li,.sticky-header, #header-tel,.main-menu ul.navigation,#section-2 a,#section-2 img').addClass('header-transition');
    $(window).scroll(function () {
        compactStickyHeader();
    });
    compactStickyHeader();

    $('.main-menu ul.navigation li ul').each(function () {
        $(this)
            .addClass('dropdown-menu')
            .parent()
            .hover(
            function () {
                $(this).children('ul').show();
            },
            function () {
                $(this).children('ul').hide();
            }
        );
    });

    $('.main-menu ul.navigation > li > a').each(function () {
        var title = $(this).attr('title');
        if (title != '' && title != 'undefined' && typeof title != 'undefined') {
            $(this).data('tooltip', title);
        }
    }).tooltip({
        position: {my: "left+15 center", at: "right center"},
        items: '[title]',
        content: function () {
            return $(this).data("tooltip");
        }
    });
    $('.main-menu ul.navigation > li > a *').tooltip({
        position: {my: "left+15 center", at: "right center"},
        items: '[title]',
        content: function () {
            return $(this).closest('a').data("tooltip");
        }
    });

    $('#fixed-menu-toggle').click(function () {
        if ($(this).parent().hasClass('open'))
            $(this).parent().animate({right: 0}).removeClass('open');
        else {
            $(this).parent().animate({right: -155}).addClass('open');
        }
    });
});
function compactStickyHeader() {
    var scrollTop = $(window).scrollTop();
    var stickyHeader = $('.sticky-header');
    if (!stickyHeader.hasClass('compact') && scrollTop >= 10) {
        stickyHeader.addClass('compact');
    }

    if (scrollTop <= 10) {
        stickyHeader.removeClass('compact');
    }
}