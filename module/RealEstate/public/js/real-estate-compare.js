/**
 * Created by Ajami on 7/2/14.
 */
$(document).ready(function () {
    var setCookieName = "realListCompare";
    var list = new cookieList(setCookieName);
    var setCookieExpiry = 7;

    if (list.items() && list.items()[0]) {
        if (!$('.img_compare').hasClass("link_to_compare")) {
            $('.box_compare').animate({
                width: "+=70"
            }, 800, function () {
            });
            $('.img_compare').addClass('link_to_compare');
        }
    }

    if ($('body').find('.real-add-to-compare').length) {
        var tag = $('.real-add-to-compare');
        if (list.search(tag.data('id')))
            endOfAddToCompare(tag);
    }

    $('.real-add-to-compare').click(function (e) {
        e.preventDefault();
        var tag = $(this);
        //  var list = new cookieList(setCookieName);
        if (!list.search(tag.data('id')))
            list.add(tag.data('id'));
        endOfAddToCompare(tag);
    });

    $('.delete_real_compare').click(function (e) {
        e.preventDefault();
        var tag = $(this);
        list.remove(tag.data('id'));
        var index = $(this).parent().index() + 1;
        var element = $('td:nth-child(' + index + ' ) ', tag.closest('table'));
        element.hide();
    });

    $('.box_compare').on('click', '.link_to_compare', function () {
        $(this).attr('href', real_compare_url + "?compare=" + list.items());
    });

    function endOfAddToCompare(tag) {
        tag.parent().toggle("bounce", { times: 3 }, "slow");
        if (!$('.img_compare').hasClass("link_to_compare")) {
            $('.box_compare').animate({
                width: "+=70"
            }, 800, function () {
            });
            $('.img_compare').addClass('link_to_compare');
        }
    }

});

var cookieList = function (cookieName) {
    var cookie = $.cookie(cookieName);
    var items = cookie ? cookie.split(/,/) : new Array();
    return {
        "add": function (val) {
            items.push(val);
            $.cookie(cookieName, items.join(','), { expires: 7, path: '/' });
        },
        "remove": function (val) {
            items = jQuery.grep(items, function (value) {
                return value != val;
            });
            console.log(items);
            $.cookie(cookieName, items, { expires: 7, path: '/' });
        },
        "clear": function () {
            items = null;
            $.cookie(cookieName, null, { expires: 7, path: '/' });
        },
        "items": function () {
            return items;
        },
        "search": function (val) {
            res = false;
            indx = $.inArray(val.toString(), items);
            if (indx > -1)
                res = true;
            return res;
        }
    }
};
