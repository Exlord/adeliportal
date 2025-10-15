/**
 * Created with JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/27/13
 * Time: 1:16 PM
 * To change this template use File | Settings | File Templates.
 */
var Grid = {};
function __setupGridDialog(url, id, el) {
    var grid_dialog = $(GridDialog.template);
    $('body').append(grid_dialog);

    $("#grid_dialog").dialog({
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        closeText: GridDialog.close_text,
        title: GridDialog.title,
        close: function (event, ui) {
            $(this).dialog("destroy").remove();
        },
        buttons: [
            {
                text: GridDialog.yes,
                icons: { primary: "ui-icon-check"},
                click: function () {
                    var grid_dialog_object = $(this);
                    $('#grid_dialog_content').addClass('ajax-loading-inline');
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {id: id},
                        complete: function () {
                            $('#grid_dialog_content').removeClass('ajax-loading-inline');
                            grid_dialog_object.dialog("destroy").remove();
                        },
                        success: function (data) {
                            if (data.status == 1)
                                __deleteGridRows(id, el);
                            else if (data.status == 0)
                                System.AjaxMessage(data.msg);
                            else
                                System.AjaxMessage(data);

                            if(data.hasOwnProperty('cmd')){
                                eval(data.cmd);
                            }
                        },
                        error: System.AjaxError
                    });
                }
            },
            {
                text: GridDialog.no,
                icons: { primary: "ui-icon-closethick"},
                click: function () {
                    $(this).dialog("destroy");
                    $("#grid_dialog").remove();
                }
            },
        ]
    });
}
function __deleteGridRows(id, el) {
    if (typeof  id == 'number') {
        var row = $(el).parent().parent('tr');
        row.fadeOut(1000, function () {
            row.remove();
        });
    } else if (typeof id == 'object') {
        $.each(id, function (index, item) {
            var row = $('#rowId' + item).parent().parent('tr');
            row.fadeOut(1000, function () {
                row.remove();
            });
        });

    }
}
$(document).ready(function () {
    System.Pages.Resources.DataGridView = 1;
    $('body')
        .off('keypress', '.grid .grid_filter_cell input')
        .on('keypress', '.grid .grid_filter_cell input', function (e) {
            if (e.which == 13) {
                var url = $(this).data('url');
                var name = $(this).attr('id');
                var value = $(this).val();
                if (value) {
                    url += '&' + name + '=' + value;
                    System.Pages.ajaxLoad(url);
                }
            }
        })
        .off('change', '.grid-filter-column select')
        .on('change', '.grid-filter-column select', function () {
            var url = $(this).val();
            if (url.length)
                System.Pages.ajaxLoad(url);
        })
        .off('click', '#toolbar_delete_selected')
        .on('click', '#toolbar_delete_selected', function (e) {
            e.preventDefault();
            var id = [];
            $('.grid .row_selector:checked').each(function (index, el) {
                id.push($(el).data('id'));
            });
            var url = $(this).attr('href');
            if (id.length)
                __setupGridDialog(url, id);
            else {
                System.AjaxMessage(GridDialog.no_item_selected);
            }
        })
        .off('click', '.grid tr')
        .on('click', '.grid tr', function () {
            $('.grid tr').removeClass('grid-row-highlight');
            $(this).addClass('grid-row-highlight');
        })
        .off('change', '.grid .toggle_select input[type=checkbox]')
        .on('change', '.grid .toggle_select input[type=checkbox]', function () {
            var id = $(this).attr('id');
            var item = id + '_item';
            var status = $(this).is(':checked');
            var items = $('.grid .' + item);
            items.prop('checked', status);
        })
        .off('click', '.grid .delete_button')
        .on('click', '.grid .delete_button', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var url = $(this).attr('href');
            __setupGridDialog(url, id, this);
        })
        .off('focus', '.grid select.grid_select')
        .on('focus', '.grid select.grid_select', function () {
            var id = $(this).attr('id');
            Grid[id] = $(this).val();
        })
        .off('change', '.grid select.grid_select')
        .on('change', '.grid select.grid_select', function (e) {
            e.stopPropagation();
            var id = $(this).attr('data-id');
            var url = $(this).attr('data-route');
            var val = $(this).val();
            var name = $(this).attr('name');
            var parent = $(this).parent();
            var oldValue = Grid[$(this).attr('id')];
            var oldClass = Grid[name][oldValue];
            parent.addClass('ajax-disabled');
            var el = $(this);
            $.ajax({
                url: url,
                type: 'POST',
                data: {id: id, field: name, value: val},
                complete: function () {
                    parent.removeClass('ajax-disabled');
                },
                success: function (data) {
                    if (data.status == 1) {
                        parent.removeClass(oldClass);
                        var newClass = Grid[name][val];
                        parent.addClass(newClass);
                    }
                    else if (data.status == 0) {
                        el.val(oldValue);
                        System.AjaxMessage(data.msg);
                    }
                    else {
                        el.val(oldValue);
                        System.AjaxMessage(data);
                    }

                    if (data.callback)
                        eval(data.callback);
                },
                error: function (a, b, c) {
                    el.val(oldValue);
                    System.AjaxError(a, b, c);
                }
            });
        });

//        .on('click', '#update_order', function () {
//            var data = {};
//            $('.grid .updateAble_order').each(function () {
//                data[$(this).data('id')] = $(this).val();
//            });
//            var url = $(this).attr('data-url');
//            var parent = $(this).parent();
//            parent.addClass('ajax-disabled');
//            $.ajax({
//                url: url,
//                type: 'POST',
//                data: {'order': data},
//                success: function (data) {
//                    parent.removeClass('ajax-disabled');
//                    if (data) {
//                        var msg = $('#system_messages_ajax', data);
//                        if (msg.length)
//                            __showSystemMessage(msg.html());
//                    }
//                    else {
//                        $('.grid #update_order').hide();
//                    }
//                },
//                error: function (jqXHR, textStatus, errorThrown) {
//                    parent.removeClass('ajax-disabled');
//                    var msg = "readyState: " + jqXHR.readyState +
//                        "\nstatus: " + jqXHR.status +
//                        "\nresponseText: " + jqXHR.responseText +
//                        "\ntextStatus: " + textStatus +
//                        "\nerrorThrown: " + errorThrown;
//                    alert(msg);
//                }
//            });
//        })

});