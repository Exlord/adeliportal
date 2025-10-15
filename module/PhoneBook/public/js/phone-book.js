$(document).ready(function () {

    $('.send-phone-book-sms').click(function (e) {
        e.preventDefault();
        var mobileNumber = getMobileNumber();
        if (mobileNumber != '') {
            $("#dialog-send-sms").dialog({
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                title: data.titleSms,
                close: function (event, ui) {
                    $(this).dialog("destroy");
                    $("#dialog-send-sms").hide();
                },
                buttons: [
                    {
                        text: data.sendBtn,
                        icons: { primary: "ui-icon-check"},
                        click: function () {
                            var dialog = $(this);
                            $('#txt-sms').addClass('ajax-loading-inline');
                            var data = {};
                            data['number'] = mobileNumber;
                            data['message'] = $('#txt-sms').val();
                            $.ajax({
                                url: data.urlSms,
                                type: 'POST',
                                data: data,
                                complete: function () {
                                    $('#txt-sms').removeClass('ajax-loading-inline');
                                },
                                success: function (data) {
                                    if (data.status == 1) {
                                        System.AjaxMessage(data.successMessage);
                                    } else
                                        System.AjaxMessage(data.errorMessage);
                                }
                            });
                        }
                    },
                    {
                        text: data.btnCancel,
                        icons: { primary: "ui-icon-closethick"},
                        click: function () {
                            $(this).dialog("destroy");
                            $("#dialog-send-sms").hide();
                        }
                    }
                ]
            });
        }
        else
            System.AjaxMessage(data.addMessage);
    });

    $('.send-phone-book-email').click(function (e) {
        e.preventDefault();
        var email = getEmail();
        if (email != '') {
            $("#dialog-send-email").dialog({
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                title: data.titleEmail,
                close: function (event, ui) {
                    $(this).dialog("destroy");
                    $("#dialog-send-email").hide();
                },
                buttons: [
                    {
                        text: data.btnNext,
                        icons: { primary: "ui-icon-check"},
                        click: function () {
                            var url = data.urlEmail;
                            url = url.replace('***', $('#newsTemplate').val());
                            url = url.replace('****', email);
                            window.location.href = url;
                        }
                    },
                    {
                        text: data.btnCancel,
                        icons: { primary: "ui-icon-closethick"},
                        click: function () {
                            $(this).dialog("destroy");
                            $("#dialog-send-email").hide();
                        }
                    }
                ]
            });
        } else
            System.AjaxMessage(data.addMessage);
    });

    $('.phone-book-word-export').click(function (e) {
        var allId = getId();
        if (allId != '') {
            $(this).attr('href', data.urlExport + '?type=single&allId=' + allId);
        } else
        {
            e.preventDefault();
            System.AjaxMessage(data.addMessage);
        }

    });

    $('.phone-book-word-export-all').click(function () {
            $(this).attr('href', data.urlExport + '?type=all');
    });

    $('.phone-book-print').click(function (e) {
        var allId = getId();
        if (allId != '') {
            $(this).attr('href', data.urlPrint + '?type=single&allId=' + allId);
        } else
        {
            e.preventDefault();
            System.AjaxMessage(data.addMessage);
        }
        $(this).attr('target', '_blank');

    });

    $('.phone-book-print-all').click(function () {
        $(this).attr('href', data.urlPrint + '?type=all');
        $(this).attr('target', '_blank');
    });

    function getEmail() {
        var email = '';
        $('.grid .row_selector:checked').each(function (index, el) {
            email += $(el).parent().next().next().next().html() + ',';
        });
        if (email != '')
            email = email.slice(0, -1);
        return email;
    }

    function getMobileNumber() {
        var mobileNumber = '';
        $('.grid .row_selector:checked').each(function (index, el) {
            mobileNumber += $(el).parent().next().next().next().next().html();
        });
        if (mobileNumber != '')
            mobileNumber = mobileNumber.slice(0, -1);
        return mobileNumber;
    }

    function getId() {
        var ids = '';
        $('.grid .row_selector:checked').each(function (index, el) {
            ids += $(el).parent().next().html()+',';
        });
        if (ids != '')
            ids = ids.slice(0, -1);
        return ids;
    }

});