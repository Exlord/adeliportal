/**
 * Created with PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/12/14
 * Time: 3:52 PM
 */
$(document).ready(function () {
    $('.pages-loading').remove();
    var pageW = $(window).width();
    var pageH = $(window).height();

//    var categories = $('.page .category');
//    var category = $(categories).first();
//    console.log($(category).height());
//    $('.page.categories').css({width: $(category).outerWidth() *$(categories).length, height: $(category).outerHeight()});

    var left = pageW - 240;
    $('.page.categories .category')
        .each(function () {
            $(this).css({left: left});
            left -= 220;
        })
        .click(function () {
            var clickedItem = $(this);
            var top = -60;
            $('.page.categories .category').not(clickedItem).each(function () {
                $(this).children('p').hide();
                $(this).transition({scale: 0.5, left: -60, top: top});
                top += (320 * 0.5);
            });
            $(clickedItem).transition({scale: 1, left: pageW - 240, top: 0}).children('p').fadeIn();
        });
    $('.page.categories').fadeIn();
});