/**
 * Created with PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/9/14
 * Time: 3:58 PM
 */
$(document).ready(function () {
    $('#admin_nav > ul > li').hoverIntent(
        function () {
            $(this).children('ul').show();
        },
        function () {
            $(this).children('ul').hide();
        }
    );
    /*$("#admin_nav ul.menu").menubar({
     */
    /*position: {
     within: $("#demo-frame").add(window).first()
     }*/
    /*
     });*/
});