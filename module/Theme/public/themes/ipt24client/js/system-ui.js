/**
 * Created with JetBrains PhpStorm.
 * User: Exlord
 * Date: 6/5/13
 * Time: 10:36 AM
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function () {
    __pageUIInit(document);

    $('.spinner').each(function () {
        $(this).keyup(function () {
            numFormat(this);
        })
    })

});

function __pageUIInit(parent) {
    $('.buttonset,.tool_bar', parent).buttonset();
//    $('.multi_check_list .multi_checkbox ,.multi_check_list .radio').each(function () {
//        $('legend', this).addClass('button_set_label ui-button ui-widget ui-state-default ui-state-hover ui-button-text-only');
//        $('fieldset', this).addClass('ui-button ui-widget ui-state-default ui-state-hover ui-button-text-only');
//    });
    $(".tool_bar a.disabled", parent).button({ disabled: true });
    $(".spinner", parent).each(function () {
        var number_format = null;
        if ($(this).hasClass('decimal')) {
            number_format = 'n';
        }
        var item = $(this);
        item.spinner({
            max: item.data('max'),
            min: item.data('min'),
            step: item.data('step'),
            numberFormatType: number_format,
            stop: function (event, ui) {
                if (item.hasClass('updateAble_order')) {
                    $('.grid #update_order').show();
                }
            }
        });
    });

    __buttons(parent);

    $(parent).tooltip({
        content: function () {
            var element = $(this);
            if (element.is("[title]")) {
                return element.attr("title");
            }
        }
    });
}
function __buttons(parent) {
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
    $('.button,.jqui_button,.grid_button', parent).each(function () {
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
                text = false;
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
        }


        $(this).button({disabled: disabled, text: text, icons: {primary: icon_p, secondary: icon_s } });
    });
}


function NumOnly(el) {
    $(el).each(function () {
        $(this).keydown(function (event) {
            // Allow only backspace and delete and colon
            if (
                event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 190 || event.keyCode == 110 ||
                    // Ensure that it is a number and stop the keypress
                    (event.keyCode >= 96 && event.keyCode <= 105) ||
                    (event.keyCode >= 48 && event.keyCode <= 57)
                ) {
                // let it happen, don't do anything
            }
            else {
                event.preventDefault();
            }
        });
    });
}


//-------------------- for num only and price -----------------------

function NotEmpty(el) {
    $(el).change(function () {
        if ($(this).val() == '' || $(this).val().length == 0) {
            $(this).val(0);
        }
    });
}

function intFormat(n, obj) {
    var regex = /(\d)((\d{3},?)+)$/;
    n = n.split(',').join('');

    while (regex.test(n)) {
        n = n.replace(regex, '$1,$2');
    }
    obj.value = n;
}


function numFormat(obj) {
    var n = obj.value;
    var result = true;
    if (result == true) {
        var pointReg = /([\d,\.]*)\.(\d*)$/, f;
        if (pointReg.test(n)) {
            f = RegExp.$2;
            return intFormat(RegExp.$1, obj) + '.' + f;
        }
        return intFormat(n, obj);
    }
}

//---------------------------------------------
