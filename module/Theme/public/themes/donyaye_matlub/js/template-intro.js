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

    var counter_display = 1;
    $('.box_circle_menu .navigation li .title_circle_menu').each(function () {
        if (counter_display != 1)
            $(this).addClass('display_none');
        counter_display++;
    });

    var timeoutId = 0;
    var flag_defaulMenu = true;
    $('.box_circle_menu .navigation li .icon_circle_menu').hoverIntent(function () {
        var img = $(this).data('id');
        if (img)
           $('#logoImg'+img).removeClass('display_none');
        $('#defaultLinkLogo').addClass('display_none');

        $('.box_circle_menu .navigation li .title_circle_menu').addClass('display_none');
        $(this).siblings('.title_circle_menu').removeClass('display_none').effect("slide", {direction: 'right'}, 600);
        $(this).effect("shake", {direction: 'left', distance: 10, times: 1}, 600);
        clearTimeout(timeoutId);
        flag_defaulMenu = false;
    }, function () {
        flag_defaulMenu = true;
        $('.box_circle_menu .navigation li .title_circle_menu').addClass('display_none');
        $('.logo_menu_1').addClass('display_none');
        timeoutId = setTimeout(function () {
            if (flag_defaulMenu) {
                $('.box_circle_menu .navigation li .default_menu').removeClass('display_none').effect("slide", {direction: 'left'});
                $('#defaultLinkLogo').removeClass('display_none');
                flag_defaulMenu = false;
            }
        }, 2000)

    });

    var counter = 1;
    $('.box_circle_menu .navigation li').each(function () {
        switch (counter) {
            case 1 :
                $(this).css({"right": "250px", "top": "10px"});
                break;
            case 2 :
                $(this).css({"right": "325px", "top": "57px"});
                break;
            case 3 :
                $(this).css({"right": "370px", "top": "112px"});
                break;
            case 4 :
                $(this).css({"right": "390px", "top": "190px"});
                break;
            case 5 :
                $(this).css({"right": "372px", "top": "269px"});
                $(this).children().children('.title_circle_menu').css('margin-top', '20px');
                break;
            case 6 :
                $(this).css({"right": "318px", "top": "332px"});
                $(this).children().children('.title_circle_menu').css('margin-top', '22px');
                break;
            case 7 :
                $(this).css({"right": "250px", "top": "377px"});
                $(this).children().children('.title_circle_menu').css('margin-top', '25px');
                break;
        }
        counter++;
    });

});