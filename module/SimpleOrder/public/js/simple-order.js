$(document).ready(function () {
    $('#simple-order').on('change', '.category-item', function () {
        var tag = $(this);
        var data = {};
        data['parentId'] = $(this).val();
        if (parseInt(data['parentId']) > 0) {
            tag.addClass('ajax-loading-inline-select');
            $.ajax({
                url: SimpleOrder.url,
                type: 'POST',
                data: data,
                complete: function () {
                    tag.removeClass('ajax-loading-inline-select');
                },
                success: function (data) {
                    if (data.status == 1) {
                        tag.parent().next().children('select').html(data.html);
                    }
                },
                error: System.AjaxError
            });
        }
    });
});