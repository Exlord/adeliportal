/**
 * Created with JetBrains PhpStorm.
 * User: Exlord
 * Date: 6/5/13
 * Time: 10:36 AM
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function () {

    $('#admin_nav ul.navigation').accordion({
        heightStyle: "content",
        active: 1
    });

    var setSelector = "#admin_nav ul.navigation";
    var setCookieName = "listOrder";
    var setCookieExpiry = 7;

    function getOrder() {
        // save custom order to cookie
        $.cookie(setCookieName, $(setSelector).sortable("toArray"), { expires: setCookieExpiry, path: "/" });
    }

    function restoreOrder() {
        var list = $(setSelector);
        if (list == null) return;

        var cookie = $.cookie(setCookieName);
        if (!cookie) return;

        var IDs = cookie.split(",");
        var items = list.sortable("toArray");
        var rebuild = new Array();
        for (var v = 0, len = items.length; v < len; v++) {
            rebuild[items[v]] = items[v];
        }

        for (var i = 0, n = IDs.length; i < n; i++) {

            var itemID = IDs[i];
            if (itemID in rebuild) {
                var item = rebuild[itemID];
                var child = $("ul.ui-sortable").children("#" + item);
                var savedOrd = $("ul.ui-sortable").children("#" + itemID);
                child.remove();
                $("ul.ui-sortable").filter(":first").append(savedOrd);
            }
        }
    }

    $("#admin_nav ul.navigation li").each(function (index, el) {
        $(this).attr('id', 'accordion_row_' + index);
    });

    $(setSelector).sortable({
        axis: "y",
        cursor: "move",
        helper: 'clone',
        update: function () {
            getOrder();
        },
        stop: function (event, ui) {
            ui.item.children("a").triggerHandler("focusout");
        }
    });

    // here, we reload the saved order
    restoreOrder();

    $("#admin_nav ul.navigation").addClass("ui-accordion ui-widget ui-helper-reset ui-accordion-icons")
        .children("li").children('a')
        .addClass("ui-accordion-header ui-helper-reset ui-state-default ui-corner-top ui-corner-bottom")
        .prepend('<span class="ui-accordion-header-icon ui-icon ui-icon-plus"/>')
        .click(function (e) {
            e.preventDefault();
            $(this).toggleClass("ui-accordion-header-active").toggleClass("ui-state-active")
                .toggleClass("ui-state-default").toggleClass("ui-corner-bottom")
                .find("> .ui-icon").toggleClass("ui-icon-plus").toggleClass("ui-icon-minus")
                .end().next().toggleClass("ui-accordion-content-active").toggle();
            return false;
        })
        .parent().children('ul').addClass("ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom").hide();

    $('#admin_nav a').addClass('ajax_page_load');
    $('.ui-accordion-header').each(function () {
        $(this).attr('href', '#');
    });

});

