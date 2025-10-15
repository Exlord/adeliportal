/**
 * Created with PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 3/2/14
 * Time: 3:57 PM
 */
//
var Search = {
    url: '',
    container: null,
    instances: {
        0: {position: '', cache: {}, initiated: false, timer: false}
    },
    langDirection: 'ltr',
    searchIndicatorClass: 'search-box-searching',
    showResult: function (data, item, id) {
        Search.hide();
        $('.row', Search.container).html(data);
        var pos = Search.instances[id].position;
        pos.of = item;
        $(Search.container).position(pos).show();
    },
    search: function (keyword, item, id) {
        $(item).siblings('span.search-indicator').addClass(Search.searchIndicatorClass);
        $.ajax({
            url: Search.url,
            data: {keyword: keyword},
            type: 'POST',
            complete: function () {
                $(item).siblings('span.search-indicator').removeClass(Search.searchIndicatorClass);
            },
            success: function (data) {
                Search.instances[id].cache[keyword] = data;
                Search.showResult(data, item, id);
            },
            error: System.AjaxError
        });
    },
    init: function (id) {
        if (this.container == null)
            this.container = $('<div id="search-block-result" class="container-fluid dropdown-menu"><div class="row"></div></div>').appendTo('body');

        Search.searchIndicatorClass += ' ' + Search.langDirection;
        $(this.container).addClass(Search.langDirection);

        var item = $('#search-input-' + id);
        $(item).attr('autocomplete', 'off');
        $(item).keyup(function (e) {
            if (e.which == 116 || e.which == 17 || e.which == 18 || e.which == 16 || e.which == 37 || e.which == 38 || e.which == 39)
                return false;

            var val = $(this).val();
            if (val.length >= 2) {
                clearTimeout(Search.instances[id].timer);
                if (val in Search.instances[id].cache) {
                    Search.showResult(Search.instances[id].cache[val], item, id);
                    return;
                }
                Search.instances[id].timer = setTimeout(function () {
                    Search.search(val, item, id)
                }, 500);
            }
        });
        $(document).click(function () {
            Search.hide();
        });
    },
    hide: function () {
        $(Search.container).hide().css({left: 'auto', right: 'auto', top: 'auto'});
    }
};
$(document).ready(function () {

});


//        $.widget("custom.catcomplete", $.ui.autocomplete, {
//            _renderMenu: function (ul, items) {
//                $(ul).addClass('inline-search-result');
//                var that = this,
//                    currentCategory = "",
//                    currentSubMenu = null;
//
//                $.each(items, function (index, item) {
//                    if (item.category != currentCategory) {
//                        var li = $("<li class='ui-autocomplete-category'>" + item.category + "</li>");
//                        currentSubMenu = $("<div></div>").appendTo(li);
//                        ul.append(li);
//                        currentCategory = item.category;
//                    }
//                    that._renderItemData(currentSubMenu, item);
//                });
//            },
//            _renderItem: function (ul, item) {
//                return $("<li>")
//                    .append(item.label)
//                    .appendTo(ul);
//            }
//        });
//
//        $('form input.inline-search').each(function (index, item) {
//            Search.cache[index] = {};
//            $(item).catcomplete({
//                position: { my: "right top", at: "right bottom" },
//                minLength: 2,
//                source: function (request, response) {
//                    var term = request.term;
//                    if (term in Search.cache[index]) {
//                        response(Search.cache[index][ term ]);
//                        return;
//                    }
//                    $.getJSON(Search.url, request, function (data, status, xhr) {
//                        Search.cache[index][ term ] = data;
//                        response(data);
//                    });
//                },
//                focus: function (event, ui) {
//                    return false;
//                },
//                select: function (event, ui) {
//                    console.log($(ui.item.label).attr('href'));
////                    window.location = $(ui.item.label).attr('href');
//                    return false;
//                },
//                search: function (event, ui) {
//                }
//            });
//        });