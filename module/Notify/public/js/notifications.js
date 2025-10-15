/**
 * Created with PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 7/22/14
 * Time: 1:49 PM
 */
if (typeof Notifications == 'undefined')
    var Notifications = {
        url: '',
        sections: {
            notify: {
                unread: 0
            }
        },
        timeout: null
    };
Notifications.update = function (section) {
    if (Notifications.timeout)
        clearTimeout(Notifications.timeout);
    $.ajax({
        type: 'POST',
        url: this.url,
        success: function (data) {
            if (data.data) {
                $('.notification-navbar').replaceWith(data.data);
                Notifications.timeout = setTimeout(function () {
                    Notifications.update('all');
                }, 1000 * 60 * 5);
            }
            else
                System.AjaxMessage(data);
        },
        error: System.AjaxError
    });
};