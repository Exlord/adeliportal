$(document).ready(function(){

    $('.select-chart-id').change(function () {
        var tag = $(this);
        var val = $(this).val();
        if (parseInt(val) > 0) {
            tag.parent().addClass('ajax-loading-inline');
            var data = {};
            data['chartId'] = val;
            $.ajax({
                url: Chart.parentNodeUrl,
                type: 'POST',
                data: data,
                complete: function () {
                    tag.parent().removeClass('ajax-loading-inline');
                },
                success: function (data) {
                    // $('#otherInfoAgentArea').slideDown(500);
                    if (data.status)
                        $('#parentId').html(data.html);
                    else
                        $('#parentId').html('');
                },
                error: System.AjaxError
            });
        } else{
            $('#parentId').html('');
            // $('#otherInfoAgentArea').slideUp(500);
        }
    });

});