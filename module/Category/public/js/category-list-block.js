$(document).ready(function () {
    $('#data_category_list_block__catId').parent().append('<div id="log-cat"></div>');
    $('#data_category_list_block__itemId').parent().append('<div id="log-item"></div>');

    $(function () {
        function logCat(message) {
            $("#log-cat").scrollTop(0);
        }

        function logItem(message) {
            $("#log-item").scrollTop(0);
        }

        $(".category-list-input").autocomplete({

            source: function (request, response) {
                var url = $('#data_category_list_block__catId').data('url');
                $.ajax({
                    url: url,
                    dataType: "json",
                    method: "POST",
                    data: {
                        term: request.term
                    },
                    success: function (data) {
                        response($.map(data, function (item) {
                            return {
                                label: item.title,
                                value: item.catId
                            }
                        }));
                    }
                });
            },
            minLength: 2,
            select: function (event, ui) {
                logCat(ui.item ?
                    $("#log-cat").html("<label class='data_category_list_block__catId' data-id='" + ui.item.value + "' id=" + ui.item.value + " >" + ui.item.label + "</label>") :
                    "Nothing selected");
            },
            open: function () {
                $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
            },
            close: function () {
                $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
            }
        });

        $(".item-list-input").autocomplete({

            source: function (request, response) {
                var url = $('#data_category_list_block__itemId').data('url');
                var catId = $('#data_category_list_block__catId').val();
                var data = {};
                data['term'] = request.term;
                data['catId'] = catId;
                $.ajax({
                    url: url,
                    dataType: "json",
                    method: "POST",
                    data: data,
                    success: function (data) {
                        response($.map(data, function (item) {
                            return {
                                label: item.title,
                                value: item.itemId
                            }
                        }));
                    }
                });
            },
            minLength: 2,
            select: function (event, ui) {
                logItem(ui.item ?
                    $("#log-cat").html("<label id=" + ui.item.itemId + " >" + ui.item.label + "</label>") :
                    "Nothing selected");
            },
            open: function () {
                $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
            },
            close: function () {
                $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
            }
        });
    });

});
