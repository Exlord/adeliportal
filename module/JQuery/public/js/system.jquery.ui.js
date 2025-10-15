/**
 * Created with PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/4/14
 * Time: 9:48 AM
 */
$(document).ready(function () {
    if ($.widget)
        $.widget.bridge('uitooltip', $.ui.tooltip);
    System.UI.spinner(document);
    System.UI.tooltip(document);
    System.UI.watermark(document);
    System.UI.select2(document);
    $(document).on('afterPageLoad', function (e) {
        System.UI.spinner(e.page.data.content);
        System.UI.tooltip(e.page.data.content);
        System.UI.watermark(e.page.data.content);
        System.UI.select2(document);
    });
});
System.UI = {
    spinner: function (parent) {
        if (jQuery().spinner) {
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
                    numberFormatType: number_format
//            stop: function (event, ui) {
//                if (item.hasClass('updateAble_order')) {
//                    $('.grid #update_order').show();
//                }
//            }
                });
                //$(this).keyup(function () {
                //    numFormat(this);
                //});
                if ($(this).hasClass('form-control'))
                    $(this).removeClass('form-control').closest('.ui-spinner').addClass('form-control');
            });
        }
    },
    tooltip: function (parent) {
        if (jQuery().tooltip) {
            if ($.widget) {
                $(parent).uitooltip({
//            track: true,
                    items: '[data-tooltip]',
                    content: function () {
                        var element = $(this);
//                if (element.is("[title]")) {
//                    if (element.attr('rel') == 'tooltip')
//                        return element.attr("title");
//                }
//                else
                        if (element.is('[data-tooltip]'))
                            return element.attr("data-tooltip");
//                return false;
                    }
//            close: function( event, ui ) {ui.tooltip.open()}
                });
            }
        }
    },
    watermark: function (parent) {
        $('input[type=text],input[type=password],textarea', parent).each(function () {
            var mark = $(this).attr('watermark');

            if (typeof mark !== 'undefined' && mark !== false) {
                $(this).attr('placeholder', mark);
//                $(this).watermark(mark, {className: 'watermark'});
            }
        });
        $('input[type=text],input[type=password],textarea', parent)
            .focus(function () {
                var placeholder = $(this).attr('placeholder');
                if (typeof placeholder != 'undefined' && placeholder)
                    $(this).data('placeholder', placeholder).removeAttr('placeholder');
            })
            .blur(function () {
                var placeholder = $(this).data('placeholder');
                if (typeof placeholder != 'undefined' && placeholder)
                    $(this).attr('placeholder', placeholder);
            });
    },
    select2: function (parent) {
        if (jQuery().select2) {
            $('select.select2', parent).select2({
                closeOnSelect: false,
                allowClear: true
            });
        }
    }
};