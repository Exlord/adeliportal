$(document).ready(function () {

    $('.real-estate-word-export').click(function (e) {
        var allId = getId();
        if (allId != '') {
            $(this).attr('href', realEstate.exportWordRoute + '?isExport=1&exportType=word&exportId=' + allId);
        } else {
            e.preventDefault();
            System.AjaxMessage(realEstate.addMessage);
        }
    });
    $('.real-estate-print').click(function (e) {
        var allId = getId();
        if (allId != '') {
            $(this).attr('href', realEstate.exportRoute + '?exportType=print&exportId=' + allId);
        } else {
            e.preventDefault();
            System.AjaxMessage(realEstate.addMessage);
        }
        $(this).attr('target', '_blank');
    });
    $('.approve').click(function (e) {
        e.preventDefault();
        loadAjax(1, $(this));
    });
    $('.disapprove').click(function (e) {
        e.preventDefault();
        loadAjax(0, $(this));
    });
    $('.real-estate-view-admin').click(function (e) {
        e.preventDefault();
        var url = $(this).data('id');
        $('.real-estate-admin-view-popup').dialog({
            autoOpen: true,
            modal: true,
            height: 600,
            width: 800,
            top: 100,
            close: function (event, ui) {
                $(this).dialog("destroy");
                $(".real-estate-admin-view-popup").hide();
                $(".real-estate-admin-view-popup").html('');
            }
        });
        $(".real-estate-admin-view-popup").load(url);
    });


});

function loadAjax(status, thisTag) {
    thisTag.addClass('ajax-loading-inline');
    var id = getId();
    if (id) {
        var data = {};
        data['id'] = id;
        data['value'] = status;
        data['field'] = 'status';
        $.ajax({
            url: realEstate.updateRoute,
            type: 'POST',
            data: data,
            success: function (data) {
                thisTag.removeClass('ajax-loading-inline');
                if (data.callback)
                    eval(data.callback);
            }
        });
    }
    else
        System.AjaxMessage(realEstate.addMessage);
}

function getId() {
    var ids = '';
    $('.grid .row_selector:checked').each(function (index, el) {
        ids += $(el).parent().next().html() + ',';
    });
    if (ids != '')
        ids = ids.slice(0, -1);
    return ids;
}