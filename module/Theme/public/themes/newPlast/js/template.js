/**
 * Created with JetBrains PhpStorm.
 * User: Exlord
 * Date: 6/1/13
 * Time: 10:23 AM
 * To change this template use File | Settings | File Templates.
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
    $('#login-popover-toggle')
        .click(function (e) {
            e.preventDefault()
        })
        .popover({
            content: function () {
                return $('#login-popover').html();
            },
            placement: 'bottom',
            html: true
        });

    $(document).on('contextmenu', 'img', function (e) {
        return false;
    });

});