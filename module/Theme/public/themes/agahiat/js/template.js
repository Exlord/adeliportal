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




});

function startTime() {
    var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    m = checkTime(m);
    s = checkTime(s);
    $('#clock').html(h + ":" + m + ":" + s);
    var t = setTimeout(function () {
        startTime()
    }, 1000);
    today = h = m = s = null;
}

function checkTime(i) {
    if (i < 10) {
        i = "0" + i
    }
    // add zero in front of numbers < 10
    return i;
}