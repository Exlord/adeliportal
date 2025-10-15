$(document).ready(function () {

    $('#agentId').change(function () {
        var tag = $(this);
        var val = $(this).val();
        if (parseInt(val) > 0) {
            tag.parent().addClass('ajax-loading-inline');
            var data = {};
            data['agentId'] = val;
            $.ajax({
                url: agentArea.getAreaUrl,
                type: 'POST',
                data: data,
                complete: function () {
                    tag.parent().removeClass('ajax-loading-inline');
                },
                success: function (data) {
                    $('#otherInfoAgentArea').slideDown(500);
                    if (data.status)
                        $('#allAgentAreaIds').html(data.html);
                    else
                        $('#allAgentAreaIds').html('');
                },
                error: System.AjaxError
            });
        } else {
            $('#allAgentAreaIds').html('');
            $('#otherInfoAgentArea').slideUp(500);
        }
    });

    $('#areaId').change(function () {
        var val = $(this).val();
        var text = $("#areaId option:selected").text();
        var html = '<div data-id="' + val + '"><label>' + text + '</label><a href="#delete" class="remove-icon delete-agent-area-id" data-id="' + val + '"></a></div>';
        $('#allAgentAreaIds').append(html);
    });

    $('#allAgentAreaIds').on('click', '.delete-agent-area-id', function (e) {
        e.preventDefault();
        $(this).parent().remove();
    });

    $('#AgentAreaSave').click(function (e) {
        e.preventDefault();
        var tag = $(this);
        tag.parent().addClass('ajax-loading-inline');
        var agentId = $('#agentId').val();
        var areaId = _getAreaId();
        if(!areaId)
            areaId = parseInt($('#areaId').val());
        if (agentId && areaId) {
            var data = {};
            data['areaId'] = areaId;
            data['agentId'] = agentId;
            $.ajax({
                url: agentArea.insertUrl,
                type: 'POST',
                data: data,
                complete: function () {
                    tag.parent().removeClass('ajax-loading-inline');
                },
                success: function (data) {
                    if(data.status)
                        System.AjaxMessage(agentArea.msgSuccessAgentId);
                    else
                        System.AjaxMessage(agentArea.msgErrorAgentId);
                },
                error: System.AjaxError
            });
        } else
            System.AjaxMessage(agentArea.msgEmptyAgentId);
    });

});

function _getAreaId() {
    var id = '';
    $('#allAgentAreaIds').children().each(function () {
        id += $(this).data('id') + ',';
    });
    if (id)
        id = id.substr(0, id.length - 1);
    return id;
}