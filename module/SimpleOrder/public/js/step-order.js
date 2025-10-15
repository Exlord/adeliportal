$(document).ready(function () {

    var stepOrderCatId = {};

    $('.main-step-order')
        .on('click', '.step-order-link',function (e) {
            e.preventDefault();
            stepOrderCatId[1] = {id: $(this).data('id')};
            var data = {};
            data['id'] = $(this).data('id');
            data['step'] = 1;
            data['info'] = stepOrderCatId;
            __AjaxLoad(data);
        }).on('click', '.step1-order-link',function (e) {
            e.preventDefault();
            stepOrderCatId[2] = {id: $(this).data('id'), name: $(this).data('name')};
            var data = {};
            data['id'] = $(this).data('id');
            data['step'] = 2;
            data['info'] = stepOrderCatId;
            __AjaxLoad(data);
        }).on('click', '.step2-order-link',function (e) {
            e.preventDefault();
            stepOrderCatId[3] = {id: $(this).data('id'), name: $(this).data('name')};
            var data = {};
            data['id'] = $(this).data('id');
            data['step'] = 3;
            data['info'] = stepOrderCatId;
            __AjaxLoad(data);
        }).on('click', '.step3-order-link',function (e) {
            e.preventDefault();
            stepOrderCatId[4] = {id: $(this).data('id'), name: $(this).data('name')};
            var data = {};
            data['id'] = $(this).data('id');
            data['step'] = 4;
            data['info'] = stepOrderCatId;
            __AjaxLoad(data);
        }).on('click', '.step4-order-link',function (e) {
            e.preventDefault();
            stepOrderCatId[5] = {id: $(this).data('id'), name: $(this).data('name')};
            var data = {};
            data['step'] = 5;
            data['info'] = stepOrderCatId;
            data['id'] = $(this).data('id');
            __AjaxLoad(data);
        }).on('click', '.step5-order-link',function (e) {
            e.preventDefault();
            stepOrderCatId[6] = {id: $(this).data('id'), name: $(this).data('name')};
            var data = {};
            data['step'] = 6;
            data['info'] = stepOrderCatId;
            data['id'] = $(this).data('id');
            __AjaxLoad(data);
        }).on('click', '.step1-custom-link',function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            $('.box-desc-step1-order').each(function () {
                $(this).addClass('hidden');
            });
            $('#descStep1order' + id).removeClass('hidden');
        }).on('click', '.step2-custom-link',function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            $('.box-desc-step2-order').each(function () {
                $(this).addClass('hidden');
            });
            $('#descStep2order' + id).removeClass('hidden');
        }).on('click', '.step3-custom-link',function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            $('.box-step3-order-tabs').each(function () {
                $(this).addClass('hidden');
            });
            $('.step3-order-link').each(function () {
                $(this).addClass('hidden');
            });
            $('#step3_order_tabs_' + id).removeClass('hidden');
            $('#step3_order_link_' + id).removeClass('hidden');
        }).on('click', '.step5-custom-link',function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            $('.box-step5-order-tabs').each(function () {
                $(this).addClass('hidden');
            });
            $('.step5-order-link').each(function () {
                $(this).addClass('hidden');
            });
            $('#step5_order_tabs_' + id).removeClass('hidden');
            $('#step5_order_link_' + id).removeClass('hidden');
        }).on('click', '.step4-custom-link', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            $('.box-desc-step4-order').each(function () {
                $(this).addClass('hidden');
            });
            $('#descStep4order' + id).removeClass('hidden');
        });

});

function __AjaxLoad(data) {
    $('.step-order-loading').show();
    $('.step-order-box-ajax-loading').addClass('ajax-loading');
    $.ajax({
        url: StepOrder.url,
        type: 'POST',
        data: data,
        complete: function () {
            $('.step-order-loading').hide();
            $('.step-order-box-ajax-loading').removeClass('ajax-loading');
        },
        success: function (data) {
            $('.main-step-order').html(data.html);
        }
    });
}