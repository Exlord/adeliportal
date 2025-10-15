/**
 * Created by Ajami on 12/2/14.
 */

$(document).ready(function () {

    $('.f_select').each(function () {
        if ($(this).val() == 'select')
            $(this).parent().next().show();
        else
            $(this).parent().next().hide();
    });

    $('.f_select').change(function () {
        if ($(this).val() == 'select') {
            $(this).parent().next().show();
        } else {
            $(this).parent().next().hide();
        }
    });

});
