/**
 * Created with JetBrains PhpStorm.
 * User: Ali
 * Date: 10/17/13
 * Time: 2:47 PM
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function () {
    $('#body').on('click', '.negative-and-positive', function () {
        var span = $(this);
        var ei = parseInt($(this).parent().data('id'));
        var rateSum = $(this).siblings().find('.rate-sum');
        var type = $(this).data('id');
        var data = {};
        data['rateScore'] = type;
        data['entityType'] = NP[ei].entityType;
        data['entityId'] = ei;
        //data['date'] = NP[ei].time;
        span.removeClass();
        span.addClass('tiny-ajax-loader');
        $.ajax({
            url: url_rate_np,
            type: 'POST',
            data: data,
            complete: function () {
                span.removeClass('tiny-ajax-loader');
                if (type > 0)
                    span.addClass('icon-plus');
                else
                    span.addClass('icon-minus');
            },
            success: function (data) {

                if (data.statusNP == 1) {
                    if (parseInt(NP[ei].sum) == 1 || parseInt(NP[ei].sum) == -1)
                        sum = type;
                    else
                        sum = parseInt(type) + parseInt(NP[ei].sum);
                    NP[ei].sum = sum;

                    span.removeClass('negative-and-positive');
                    span.addClass('np-disabled');
                    span.fadeTo('slow', '0.3');

                    var span2 = span.siblings('.np-disabled');
                    if (span2) {
                        span2.removeClass('np-disabled');
                        span2.addClass('negative-and-positive');
                        span2.fadeTo('slow', '1');
                    }
                    rateSum.html(sum);
                }
            },
            error: System.AjaxError
        });
    });
});