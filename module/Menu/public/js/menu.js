var MenuTypes = {
    resultLabel: 'Selected Value',
    dialog: {
        cancel: 'Cancel',
        instance: null
    },
    items: {},
    render: function (menuType, items) {
        var temp = menuType.data.template;
        $.each(items, function (index, item) {
            var cleanItem = item;
            if (typeof item == "string" && item != '')
                cleanItem = item.replace('|--', '');

            temp = temp.replace('[' + index + ']', cleanItem);
        });
        return temp;
    },
    show: function (menuType, html) {
        $('.data-view .content', menuType.parent).html(html);
    },
    _addMenuTypesClickEvent: function () {
        //console.log('menu type click event');
        $('#menuTypes a').click(function (e) {
            //console.log('menu type click event fired');
            e.preventDefault();
            $('#itemUrlType').val($(this).data('type')).change();
            //console.log(MenuTypes.dialog.instance);
            if (MenuTypes.dialog.instance != null)
                $(MenuTypes.dialog.instance).dialog('close').css('display', 'none');
            $('#selected-menu-type span#menu-type-content').text($('h4', this).text());
        });
    },
    init: function () {
        $('#menu-type-params input.data-loader').each(function (e) {
            $(this).parent().parent().append("<div class='data-view'><span>" + MenuTypes.resultLabel + " : </span><span class='content'></span></div>");
            var MenuType = {
                element: $(this),
                id: $(this).data('type'),
                url: $(this).data('url'),
                parent: $(this).parent().parent(),
                data: {
                    template: $(this).data('template'),
                    cache: {}
                }
            };

            MenuTypes.items[MenuType.id] = MenuType;
            var items = {};
            $(this).parent().parent().children('input[type=hidden]').each(function (index, el) {
                items[$(this).data('field')] = $(this).val();
            });
            MenuTypes.show(MenuType, MenuTypes.render(MenuType, items));
        });
        $.each(MenuTypes.items, function (id, menuType) {
            $(menuType.element).autocomplete({
                minLength: 2,
                source: function (request, response) {
                    var term = request.term;
                    if (term in menuType.data.cache) {
                        response(menuType.data.cache[ term ]);
                        return;
                    }
                    $.getJSON(MenuTypes.items[id].url, request, function (data, status, xhr) {
                        menuType.data.cache[ term ] = data;
                        response(data);
                    });
                },
                focus: function (event, ui) {
                    $(menuType.element).val(MenuTypes.render(menuType, ui.item));
                    return false;
                },
                select: function (event, ui) {
                    $.each(ui.item, function (index, item) {
                        var cleanItem = item;
                        if (typeof item == "string" && item != '')
                            cleanItem = item.replace('|--', '');
                        $("input[data-field='" + index + "']").val(cleanItem);
                    });
                    MenuTypes.show(menuType, MenuTypes.render(menuType, ui.item));
                    return false;
                }
            })
                .data("ui-autocomplete")._renderItem = function (ul, item) {
                return $("<li>")
                    .append("<a>" + MenuTypes.render(menuType, item) + "</a>")
                    .appendTo(ul);
            };
        });
        $('#selected-menu-type').click(function (e) {
            e.preventDefault();
            var height = parseInt($(window).height() * 80 / 100);
            var width = parseInt($(window).width() * 90 / 100);
            if (MenuTypes.dialog.instance != null)
                $(MenuTypes.dialog.instance).dialog('open');
            else {
                MenuTypes.dialog.instance = $("#menuTypes").css('display', '').dialog({
                        modal: true,
                        height: height,
                        maxHeight: height,
                        width: width,
                        maxWidth: width,
                        dialogClass: 'no-header',
                        buttons: [
                            {
                                text: MenuTypes.dialog.cancel,
                                click: function () {
                                    $(this).dialog("close");
                                }
                            }
                        ]
                    }
                );
                //console.log(MenuTypes.dialog.instance);
            }
//           MenuTypes._addMenuTypesClickEvent();
        });

        $('#itemUrlType').change(function () {
            $('#menu-type-params fieldset').hide();
            var value = $(this).val();
           // console.log(value);
            if (value)
                $('#menu-type-params fieldset#' + value).show();
        });
        MenuTypes._addMenuTypesClickEvent();
        if ($('#itemUrlType').val() != '') {
            $('#menuTypes a[data-type=' + $('#itemUrlType').val() + ']').click();
        }
    }
};



