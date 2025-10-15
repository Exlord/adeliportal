/**
 * Created with PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/30/14
 * Time: 9:01 AM
 */
var LayoutDesigner = {//TODO set 0/1 for checkboxes datas
    Messages: {
        designBtnTitle: 'Design Layout',
        showCodeBtnTitle: 'Toggle Show Code',
        saveBtnTitle: 'Save',
        cancelBtnTitle: 'Cancel',
        rowNameRequired: 'Row name is required',
        columnNameRequired: 'Column name is required',
        noSelectedItem: 'No item is selected for delete!',
        cantDeletePage: "Page cannot be deleted!",
        cantEditPage: "Page cannot be edited!",
        selectRowOrColumn: "Select a row or column to add new column."
    },
    _defaultLayout: null,
    layout: null,
    selectedItem: {
        value: null,
        isRow: false,
        isColumn: false,
        isPage: false
    },
    lastColumnNumber: -1,
    lastRowNumber: -1,
    clipboard: null,
    init: function () {
        $('<div class="btn-group"></div>').insertAfter('#layout')
            .append(
                $("<a href='#layout-designer' id='open-layout-designer' class='btn btn-default'></a>")
                    .text(LayoutDesigner.Messages.designBtnTitle))
            .append($("<a href='#show-code' id='show-code' class='btn btn-default'></a>")
                .text(LayoutDesigner.Messages.showCodeBtnTitle));

        $(document).on('click', '#layout-designer #add-row', LayoutDesigner._addRow);
        $(document).on('click', '#layout-designer #add-column', LayoutDesigner._addColumn);
        $(document).on('click', '#open-layout-designer', LayoutDesigner._openLayoutDesigner);
        $(document).on('click', '#layout-content .row', LayoutDesigner._select);
        $(document).on('click', '#layout-content .layout-column', LayoutDesigner._select);
        $(document).on('click', '#layout-content', LayoutDesigner._select);
        $(document).on('click', '#layout-designer #delete-item', LayoutDesigner._delete);
        $(document).on('click', '#layout-designer #edit-item', LayoutDesigner._edit);
        $(document).on('click', '#layout-designer #cut-item', LayoutDesigner._cut);
        $(document).on('click', '#layout-designer #copy-item', LayoutDesigner._copy);
        $(document).on('click', '#layout-designer #paste-item', LayoutDesigner._paste);
        $(document).on('click', '#show-code', function (e) {
            e.preventDefault();
            $('#layout').toggle();
        });
    },
    _openLayoutDesigner: function (e) {
        e.preventDefault();
        LayoutDesigner._loadLayout();

        LayoutDesigner._sortable("#layout-content", "ui-state-highlight");
        LayoutDesigner._sortable("#layout-content .row");
        LayoutDesigner._sortable("#layout-content .row .layout-column");

        var wHeight = $(window).height();
        var wWidth = $(window).width();
        $('#layout-designer').dialog({
            height: wHeight - 22,
            width: wWidth - 21,
            modal: true,
            dialogClass: "layout-designer",
            buttons: [
                {
                    text: LayoutDesigner.Messages.saveBtnTitle,
                    click: function () {
                        var data = {};
                        LayoutDesigner.__extractRows($('#layout-content > .row'), data);
                        LayoutDesigner.layout = data;
                        data = JSON.stringify(data);
                        $('#layout').val(data);
                        $(this).dialog("close");
                    }
                },
                {
                    text: LayoutDesigner.Messages.cancelBtnTitle,
                    click: function () {
                        $(this).dialog("close");
                    }
                }
            ]
        });
//        $('#layout-content').click();
    },
    _loadLayout: function () {
        if (this.layout == null)
            this.layout = this._defaultLayout;

        var parent = $('#layout-content');

        var layout = LayoutDesigner.layout;
        parent.find('.row').remove();
        LayoutDesigner.__loadRows(layout, parent);
    },
    __loadRows: function (rows, parent) {
        $.each(rows, function (rowId, columns) {
            var row = LayoutDesigner.__makeRow(rowId);
            row.appendTo(parent);

            if (!$.isEmptyObject(columns)) {
                $.each(columns, function (colId, colSettings) {
                    colSettings['column_name'] = colId;

                    var subRows = colSettings['rows'];
                    if (typeof subRows != 'undefined')
                        delete colSettings['rows'];

                    var column = LayoutDesigner.__makeColumn(colId, colSettings);
                    column.appendTo(row);

                    if (typeof subRows != 'undefined' && !$.isEmptyObject(subRows))
                        LayoutDesigner.__loadRows(subRows, column);
                });
            }
        });
    },
    _select: function (e) {
        e.stopPropagation();
        $('#designer-toolbar a').removeClass('hidden');
        $('.selected').removeClass('selected');
//        if ($(this).hasClass('row') || $(this).hasClass('layout-column')) {
        $(this).addClass('selected');
        LayoutDesigner.selectedItem = {};
        LayoutDesigner.selectedItem.value = $(this);
        LayoutDesigner.selectedItem.isRow = $(this).hasClass('row');
        LayoutDesigner.selectedItem.isColumn = $(this).hasClass('layout-column');
        LayoutDesigner.selectedItem.isPage = !(LayoutDesigner.selectedItem.isRow || LayoutDesigner.selectedItem.isColumn);

        $('#selected-item-value').text($(this).children('span.layout-tag').text());

        if (LayoutDesigner.selectedItem.isPage) {
            $('#designer-toolbar a').addClass('hidden');
            $('#add-row,#paste-item').removeClass('hidden');
        }
        else if (LayoutDesigner.selectedItem.isRow) {
            $('#add-row').addClass('hidden');
        }
        else if (LayoutDesigner.selectedItem.isColumn) {
            $('#add-column').addClass('hidden');
        }

        if (LayoutDesigner.clipboard != null) {
            if ((LayoutDesigner.clipboard.isRow && LayoutDesigner.selectedItem.isRow) ||
                (LayoutDesigner.clipboard.isColumn && (LayoutDesigner.selectedItem.isColumn || LayoutDesigner.selectedItem.isPage))
                )
                $('#paste-item').addClass('hidden');
        }
        else
            $('a#paste-item').addClass('hidden');

        var buttonsLength = $('#designer-toolbar a:not(.hidden)').length;
        $('#designer-toolbar').css({
            top: $(this).offset().top + $('#layout-designer').scrollTop() - 30,
            left: $(this).offset().left,
            width: 12 + (buttonsLength * 30)
        }).show();
//        }
    },
    _addRow: function (e) {
        e.preventDefault();
        LayoutDesigner.__getRowDialog(function (dialog) {
            var row = LayoutDesigner.__saveRow(false, dialog);
            if (row != null)
                LayoutDesigner._sortable(row);
        });
        $('#row-name').val('row-' + (++LayoutDesigner.lastRowNumber));
    },
    _addColumn: function (e) {
        e.preventDefault();
        var parent = LayoutDesigner.__getContainerForColumn();
        if (parent == null) {
            System.AjaxMessage(LayoutDesigner.Messages.selectRowOrColumn);
            return false;
        }
        LayoutDesigner.__getColumnDialog(function (dialog) {
            var column = LayoutDesigner.__saveColumn(false, dialog);
            if (column != null)
                LayoutDesigner._sortable(column);
        });
        $('#column_name').val('column-' + (++LayoutDesigner.lastColumnNumber));
    },
    _delete: function (e) {
        e.preventDefault();
        if (LayoutDesigner.selectedItem.value == null) {
            System.AjaxMessage(LayoutDesigner.Messages.noSelectedItem);
            return false;
        }
        if (LayoutDesigner.selectedItem.isPage) {
            System.AjaxMessage(LayoutDesigner.Messages.cantDeletePage);
            return false;
        }

        $(LayoutDesigner.selectedItem.value).remove();
        return true;
    },
    _edit: function (e) {
        e.preventDefault();
        if (LayoutDesigner.selectedItem.value == null) {
            System.AjaxMessage(LayoutDesigner.Messages.noSelectedItem);
            return false;
        }
        if (LayoutDesigner.selectedItem.isPage) {
            System.AjaxMessage(LayoutDesigner.Messages.cantDeletePage);
            return false;
        }
        if (LayoutDesigner.selectedItem.isRow) {
            $('#add-layout-row-form').find('#row-name').val($(LayoutDesigner.selectedItem.value).data('id'));
            LayoutDesigner.__getRowDialog(function (dialog) {
                LayoutDesigner.__saveRow(true, dialog);
            });
        }
        if (LayoutDesigner.selectedItem.isColumn) {
            LayoutDesigner.__getColumnDialog(function (dialog) {
                LayoutDesigner.__saveColumn(true, dialog);
            });

            var form = $('#add-layout-column-form');
            var data = $(LayoutDesigner.selectedItem.value).data('data');
            console.log(data);
            $.each(data, function (id, value) {
                var el = $(form).find('#' + id);
                if ($(el).is(':checkbox')) {
                    if (value == 1)
                        $(el).prop('checked', true);
                } else
                    $(el).val(value);
            });
        }
        return true;
    },
    _cut: function (e) {
        e.preventDefault();
        if (!LayoutDesigner.selectedItem.isPage) {
            LayoutDesigner.clipboard = LayoutDesigner.selectedItem;
            $(LayoutDesigner.selectedItem.value).detach();
            $('#designer-toolbar').hide();
            LayoutDesigner.selectedItem = null;
        }
    },
    _copy: function (e) {
        e.preventDefault();
        if (!LayoutDesigner.selectedItem.isPage) {
            LayoutDesigner.clipboard = LayoutDesigner.selectedItem;
        }
    },
    _paste: function (e) {
        e.preventDefault();
        if (LayoutDesigner.clipboard != null) {
            $(LayoutDesigner.clipboard.value).removeClass('selected');
            if (LayoutDesigner.clipboard.isRow)
                LayoutDesigner.__getContainerForRow().append(LayoutDesigner.clipboard.value);
            else if (LayoutDesigner.clipboard.isColumn)
                LayoutDesigner.__getContainerForColumn().append(LayoutDesigner.clipboard.value);

            LayoutDesigner.clipboard = null;
            $('#paste-item').addClass('hidden');
        }
    },
    _sortable: function (el, placeholder) {
        var params = {
            cursor: "move",
            delay: 150,
            opacity: 0.5,
            handle: '.layout-tag'
        };
        if (placeholder)
            params.placeholder = placeholder;
        $(el).sortable(params);
    },
    __getRowDialog: function (saveCallback) {
        var form = $('#add-layout-row-form');
        $('input[type=text]', form).val('');
        $(form).dialog({
            height: 200,
            width: 300,
            modal: true,
            buttons: [
                {
                    text: LayoutDesigner.Messages.saveBtnTitle,
                    icons: { primary: "ui-icon-disk"},
                    click: function () {
                        saveCallback(this);
                    }
                },
                {
                    text: LayoutDesigner.Messages.cancelBtnTitle,
                    icons: { primary: "ui-icon-circle-minus"},
                    click: function () {
                        $(this).dialog("close");
                    }
                }
            ]
        });
    },
    __getColumnDialog: function (saveCallback) {
        var form = $('#add-layout-column-form');
        $('input[type=text]', form).val(0);
        $('input[type=checkbox]', form).prop('checked', false);
        var h = $(window).height() - 22;
        if (h > 500)
            h = 500;
        $(form).dialog({
            height: h,
            width: 500,
            modal: true,
            buttons: [
                {
                    text: LayoutDesigner.Messages.saveBtnTitle,
                    icons: { primary: "ui-icon-disk"},
                    click: function () {
                        saveCallback(this);
                    }
                },
                {
                    text: LayoutDesigner.Messages.cancelBtnTitle,
                    icons: { primary: "ui-icon-circle-minus"},
                    click: function () {
                        $(this).dialog("close");
                    }
                }
            ]
        });
    },
    __saveRow: function (edit, dialog) {
        var rowName = $('#row-name').val().replace(' ', '');
        var row = null;
        if (rowName.length == 0) {
            System.AjaxMessage(LayoutDesigner.Messages.rowNameRequired);
        } else {
            if (!edit) {
                row = LayoutDesigner.__makeRow(rowName);
                LayoutDesigner.__getContainerForRow().append(row);
            } else {
                $(LayoutDesigner.selectedItem.value)
                    .data('id', rowName)
                    .children('span.layout-tag').text(rowName);
                $(LayoutDesigner.selectedItem.value).click();
            }
            $(dialog).dialog("close");
            return row;
        }
    },
    __saveColumn: function (edit, dialog) {
        var columnName = $('#column_name').val().replace(' ', '');

        var column = null;
        if (columnName.length == 0) {
            System.AjaxMessage(LayoutDesigner.Messages.columnNameRequired);
        } else {

            var data = {"column_name": columnName};

            $('input[type=text]', dialog).each(function () {
                var id = $(this).attr('id');
                if (id != 'column_name') {
                    var val = parseInt($(this).val());
                    if (!(isNaN(val) || val == 0)) {
                        data[id] = val;
                    }
                }
            });

            $('input[type=checkbox]:checked', dialog).each(function () {
                var id = $(this).attr('id');
                data[id] = 1;
            });

//            var columnWidth = parseInt($('#column-width').val().replace(' ', ''));
//            if (columnWidth == 0 || isNaN(columnWidth))
//                columnWidth = 12;
//
//            var isMainContent = $('#main-content-column').is(':checked');
//            if (isMainContent) {
//                $('#layout-content .layout-column').data('main-content', '0');
//                isMainContent = '1';
//            } else
//                isMainContent = '0';
//
//            var canContainBlocks = ($('#can-contain-blocks').is(':checked') ? '1' : '0');


            if (!edit) {
                column = LayoutDesigner.__makeColumn(columnName, data);
                LayoutDesigner.__getContainerForColumn().append(column);
            } else {
                var currentWidth = LayoutDesigner.__getWidth($(LayoutDesigner.selectedItem.value).data('data'));
                $(LayoutDesigner.selectedItem.value)
                    .removeClass('col-md-' + currentWidth)
                    .data('data', data)
                    .addClass('col-md-' + LayoutDesigner.__getWidth(data))
                    .children('span.layout-tag').text(columnName);
                $(LayoutDesigner.selectedItem.value).click();
            }
            $(dialog).dialog("close");
            return column;
        }
    },
    __makeRow: function (rowId) {
        LayoutDesigner.lastRowNumber++;
        return $("<div></div>")
            .attr({'class': 'row', 'id': 'layout-row-' + LayoutDesigner.lastRowNumber, 'data-id': rowId})
            .append($("<span></span>").addClass('layout-tag').text(rowId))
            ;
    },
    __makeColumn: function (colId, options) {
        LayoutDesigner.lastColumnNumber++;
        return $("<div></div>")
            .addClass('col-md-' + LayoutDesigner.__getWidth(options))
            .addClass('layout-column')
            .attr({
                'id': 'layout-column-' + LayoutDesigner.lastColumnNumber
            })
            .data('data', options)
            .append($("<span></span>").addClass('layout-tag').text(colId))
            ;
    },
    __getContainerForRow: function () {
        if (LayoutDesigner.selectedItem.isColumn)
            return LayoutDesigner.selectedItem.value;
        if (LayoutDesigner.selectedItem.isRow)
            return $(LayoutDesigner.selectedItem.value).parent();

        return $('#layout-content');
    },
    __getContainerForColumn: function () {
        if (LayoutDesigner.selectedItem.isColumn)
            return $(LayoutDesigner.selectedItem.value).parent();
        if (LayoutDesigner.selectedItem.isRow)
            return LayoutDesigner.selectedItem.value;

        return null;
    },
    __extractRows: function (elements, rows) {
        $(elements).each(function (index, el) {
            var columns = {};
            var children = $(el).children('.layout-column');
            if (children.length)
                LayoutDesigner.__extractColumns(children, columns);
            var rowId = $(el).data('id');
            rows[rowId] = columns;
        });
    },
    __extractColumns: function (elements, columns) {
        $(elements).each(function (index, el) {
            var data = $(el).data('data');
            columns[data['column_name']] = data;
            var subRows = $(el).children('.row');
            if (subRows.length) {
                var rows = {};
                LayoutDesigner.__extractRows(subRows, rows);
                columns[data['column_name']].rows = rows;
            }
        })
    },
    __getWidth: function (data) {
        var devices = ['lg', 'md', 'sm', 'xs'];
        var width = false;
        $.each(devices, function (index, item) {
            var w = data['column_width_' + item];
            if (typeof w != 'undefined') {
                w = parseInt(w);
                if (w != 0) {
                    width = w;
                    return false;
                }
            }
        });
        if (width)
            return width;

        width = data['width'];
        if (typeof width != 'undefined') {
            return width;
        }

        return 12;
    }
};