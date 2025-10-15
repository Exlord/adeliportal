/**
 * Created with PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/4/14
 * Time: 9:46 AM
 */
$(document).ready(function () {
    System.UI.Buttons.render(document);
    $(document).on('afterPageLoad', function (e) {
        System.UI.Buttons.render(e.page.data.content);
    });
});
System.UI.Buttons = {
    render: function (parent) {
        $('input:submit, input:reset, input:button', parent).each(function () {
            var button = $("<button></button>");
            if ($(this).attr('type') != 'undefined')
                $(button).attr('type', $(this).attr('type'));

            if ($(this).attr('class') != 'undefined')
                $(button).attr('class', $(this).attr('class'));

            if ($(this).attr('name') != 'undefined')
                $(button).attr('name', $(this).attr('name'));

            if ($(this).attr('id') != 'undefined')
                $(button).attr('id', $(this).attr('id'));

            $(button).attr('value', $(this).val());
            $(button).html($(this).val());

            $(this).replaceWith(button);
        });
        $('.button,.grid_button', parent).each(function () {
            var icon_p = '';
            var icon_s = '';
            var disabled = false;
            var text = true;

            if ($(this).hasClass('disabled'))
                disabled = true;

            if ($(this).hasClass('icon_button'))
                text = false;

            icon_p = $(this).data('icon-p');
            icon_s = $(this).data('icon-s');
            if (icon_p != 'undefined' && icon_s != 'undefined') {
                if ($(this).hasClass('delete_button'))
                    icon_s = "ui-icon-trash";
                else if ($(this).hasClass('edit_button'))
                    icon_s = "ui-icon-wrench";
                else if ($(this).hasClass('locked_button')) {
                    icon_s = "ui-icon-locked";
                }
                else if ($(this).hasClass('add_button'))
                    icon_s = "ui-icon-plus";
                else if ($(this).hasClass('save_button'))
                    icon_s = "ui-icon-disk";
                else if ($(this).hasClass('save_new_button')) {
                    icon_p = "ui-icon-plus";
                    icon_s = "ui-icon-disk";
                }
                else if ($(this).hasClass('cancel_button'))
                    icon_s = "ui-icon-circle-minus";
                else if ($(this).hasClass('refresh_button'))
                    icon_s = "ui-icon-refresh";
                else if ($(this).hasClass('first_button'))
                    icon_s = "ui-icon-seek-end";
                else if ($(this).hasClass('prev_button'))
                    icon_s = "ui-icon-seek-next";
                else if ($(this).hasClass('next_button'))
                    icon_p = "ui-icon-seek-prev";
                else if ($(this).hasClass('last_button'))
                    icon_p = "ui-icon-seek-first";
                else if ($(this).hasClass('yes_button'))
                    icon_s = "ui-icon-check";
                else if ($(this).hasClass('no_button'))
                    icon_s = "ui-icon-closethick";
                else if ($(this).hasClass('login_button'))
                    icon_s = "ui-icon-unlocked";
                else if ($(this).hasClass('close_button')) {
                    icon_s = "ui-icon-closethick";
                }
                else if ($(this).hasClass('search_button'))
                    icon_s = "ui-icon-search";
                else if ($(this).hasClass('archive_button'))
                    icon_s = "ui-icon-folder-collapsed";
                else if ($(this).hasClass('setting_button'))
                    icon_s = "ui-icon-gear";
                else if ($(this).hasClass('image_button'))
                    icon_s = "ui-icon-image";
                else if ($(this).hasClass('print_button'))
                    icon_s = "ui-icon-print";
                else if ($(this).hasClass('mail_button'))
                    icon_s = "ui-icon-mail-closed";
            }

            $(this).button({disabled: disabled, text: text, icons: {primary: icon_p, secondary: icon_s } });
        });
        $('.buttonset', parent).buttonset();
    }
};