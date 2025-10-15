$(document).ready(function () {
    $('.popup-send-mail').click(function (e) {
        e.preventDefault();
        $("#divPopupQuickSendMail").dialog({
            resizable: false,
            height: 400,
            width: 400,
            modal: true,
            title: Comment.title
        });
    });

});