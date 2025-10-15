/**
 * Created with JetBrains PhpStorm.
 * User: Exlord
 * Date: 6/1/13
 * Time: 10:23 AM
 * To change this template use File | Settings | File Templates.
 */
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
});