System.Widgets = {
    Items: {},
    url: '',
    load: function (el) {
        if ($('#' + el.id).length) {
            $.ajax({
                    url: this.url,
                    type: 'POST',
                    data: {fqn: el.fqn},
                    complete: function () {
                        System.Widgets.reload(el);
                    },
                    success: function (data) {
                        data = $(data.content).css({display: 'none'});
                        System.UI.spinner(data);
                        System.UI.tooltip(data);
                        System.UI.Buttons.render(data);
                        $('#' + el.id).html(data);
                        $(data).fadeIn(1000);
                    }
                }
            );
        }
    },
    reload: function (el) {
        if ($('#' + el.id).length) {
            if (el.timeout) {
                setTimeout(function () {
                    System.Widgets.load(el);
                }, el.timeout);
            }
        }
    },
    init: function () {
        $.each(this.Items, function (id, el) {
            if ($('#' + el.id + ' > div.loading').length) {
                System.Widgets.load(el);
            } else {
                System.Widgets.reload(el);
            }
//            $('#' + el.id).parent().attr('id',el.fqn);
        });

//        var w = parseInt($('#system-widgets').width() / 4)-20;
//
//        $('#system-widgets').gridster({
//            widget_selector: "div.system-widget-wrapper",
//            min_cols: 4,
//            min_rows: 2,
//            max_size_x:2,
//            widget_margins: [10, 10],
//            widget_base_dimensions: [w, w]
//        });
//        $('.system-widget-wrapper').resizable();
//
//        var setSelector = '#system-widgets';
//        var setCookieName = "widgets_order";
//        var setCookieExpiry = 7;
//
//        $('#system-widgets').sortable({
//            cursor: "move",
//            placeholder: "sortable-placeholder",
//            forceHelperSize: true,
//            forcePlaceholderSize: true,
//            connectWith: "#system-widgets",
//            items: "> div.system-widget-wrapper",
//            update: function () {
//                $.cookie(setCookieName, $(setSelector).sortable("toArray"), { expires: setCookieExpiry, path: "/" });
//            },
//            stop: function (event, ui) {
////                ui.item.children("a").triggerHandler("focusout");
//            }
//        });
    }
};