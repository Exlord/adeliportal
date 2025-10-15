System.FormsManager = {
    Editor: {},
    Fields: {}
};
$(document).ready(function () {
    $('body')
        .on('keyup', '.selectable-fields-filter #field-title', function () {
            var val = $(this).val();
            if (val.length >= 2) {
                $('.selectable-field-item .hidden-item')
                    .removeClass('hidden-item')
                    .removeClass('visible-item');
                $('.selectable-field-item .field-item-title')
                    .each(function () {
                        var text = $(this).text();
                        if (text.contains(val))
                            $(this).parent().parent().addClass('visible-item');
                    });
                $('.selectable-field-item')
                    .not('.visible-item')
                    .addClass('hidden-item');
            }
        })
        .on('keyup', '.selectable-fields-filter #field-type', function () {
            var val = $(this).val();
            if (val.length >= 2) {
                $('.selectable-field-item .hidden-item')
                    .removeClass('hidden-item')
                    .removeClass('visible-item');
                $('.selectable-field-item .field-item-type')
                    .each(function () {
                        var text = $(this).text();
                        if (text.contains(val))
                            $(this).parent().parent().addClass('visible-item');
                    });
                $('.selectable-field-item')
                    .not('.visible-item')
                    .addClass('hidden-item');
            }
        })
        .on('click', '.selectable-field-item', function (e) {
            var status = $('input', this).is(':checked');
            $('input', this).prop('checked', !status);
            if (!status) {
                $(this).addClass('selected');
            }
            else {
                $(this).removeClass('selected');
            }
        })
        .on('click', '.selectable-field-item input[type=checkbox]', function (e) {
            e.stopPropagation();
            var status = $(this).is(':checked');
            if (status)
                $(this).parent().parent().addClass('selected');
            else
                $(this).parent().parent().removeClass('selected');
        });
});