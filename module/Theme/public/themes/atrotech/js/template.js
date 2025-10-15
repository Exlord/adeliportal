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

    var englishAlphabetDigitsAndWhiteSpace = /([A-Za-z0-9\.\-\_\/])+/g;

    $('.article-title').each(function (e) {
       // var result = englishAlphabetDigitsAndWhiteSpace.test($(this).html());
       // console.log(result,$(this).html(),typeof result);
        var text = $(this).html();
        if(text.search(/[^\x00-\x7E]/) != -1){
            $(this).parent().css('text-align', 'right').css('direction','ltr');
            $(this).parent().find('.typeTextfield').css('direction', 'ltr');
        }else{
            $(this).parent().css('text-align', 'left').css('direction','ltr');
            $(this).parent().find('.typeTextfield').css('direction', 'ltr');
        }
    });

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

    $('.paginationControl').buttonset();

});

$(window).bind("load", function () {
    $('.box_news').fadeTo('slow', 1);
    $('.box_info').fadeTo('slow', 1);
});