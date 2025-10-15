/**
 * Created with PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/4/14
 * Time: 10:37 AM
 */
$(document).ready(function () {
    $('#admin_nav').removeClass('no-script');
    $('#admin_nav li').hover(
        function () {
            $(this).children('ul').show();
        }, function () {
            $(this).children('ul').hide();
        }
    );
});