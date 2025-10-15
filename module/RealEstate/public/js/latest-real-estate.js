var latestRealEstate = {
    RegType: [],
    count:'',
    url:''
};

$(document).ready(function () {



    var estateType = $('#tabsLatestEstateType ul li').data('id');

    $('.latest-real-estate-loading').addClass('ajax-loading');
    var firstData = {};
    firstData['estateType'] = estateType;
    firstData['count'] = latestRealEstate.count;
    firstData['estateRegType'] = latestRealEstate.RegType;
    $.ajax({
        url: latestRealEstate.url,
        type: 'POST',
        data: firstData,
        complete: function () {
            $('.latest-real-estate-loading').removeClass('ajax-loading');
        },
        success: function (data) {
            if (data.status == 1) {
                $('#tabsEstateType-' + estateType).html(data.html);
            }
        }
       // error: System.AjaxError
    });

    $('#tabsLatestEstateType ul li').click(function (e) {
        e.preventDefault();
        estateType = $(this).data('id');
        if ($('#tabsEstateType-' + estateType).html().length < 2) {
            $('.latest-real-estate-loading').addClass('ajax-loading');
            var data = {};
            data['estateType'] = estateType;
            data['count'] = latestRealEstate.count;
            data['estateRegType'] = latestRealEstate.RegType;
            $.ajax({
                url: latestRealEstate.url,
                type: 'POST',
                data: data,
                complete: function () {
                    $('.latest-real-estate-loading').removeClass('ajax-loading');
                },
                success: function (data) {
                    if (data.status == 1) {
                        $('#tabsEstateType-' + estateType).html(data.html);
                    }
                }
               // error: System.AjaxError
            });
        }
    });
});
