/**
 * Created with JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/11/13
 * Time: 10:10 AM
 * To change this template use File | Settings | File Templates.
 */
var estateRegType;
var estateType;
$(document).ready(function () {
    estateRegType = $('select[name=regType]').val();
    estateType = $('select[name=estateType]').val();
    $('body')
        .on('change', 'select[name=regType]', function () {
            estateRegType = $(this).val();
            estateRegType_changed();
        })
        .on('change', 'select[name=estateType]', function () {
            estateType = $(this).val();
            estateType_changed();
        });
    estateRegType_changed();
});

function estateRegType_changed() {
    estateType_changed();
    if (estateRegType == 2) {
        $('#price_panel_2').show();
        $('#price_panel').hide();
    } else {
        $('#price_panel_2').hide();
        $('#price_panel').show();
    }
}
function estateType_changed() {

    _reset_fields();

    //disable regType based on estateType
    var flag = true;

    // console.log(12);
    if ($.inArray(estateType, estateType_regType)) {
        var x = estateType_regType[estateType];
        if (typeof x != 'undefined') {
            for (var i = 1; i <= 4; i++) {
                if (x[i] != '1') {
                    $('select[name=regType] option[value=' + i + ']').prop('disabled', true);
                } else {
                    $('select[name=regType] option[value=' + i + ']').removeProp('disabled');
                }
            }
        }
    }
    else {
        flag = false;
    }


    if (!flag)
        $('select[name=regType]').prop('disabled', true);

    if ($('select[name=regType] option:selected').prop('disabled') == true) {
        $('select[name=regType]').val('');
        estateRegType = 0;
        $('#price_panel_2').hide();
        $('#price_panel').show();
    }

    var exceptions = [];

    flag = true;
    if ($.inArray(estateType, estateType_fields)) {
        if ($.inArray(estateRegType, estateType_fields[estateType])) {

            if (estateRegType == '') {
                flag = false;
            }
            else {
                x = estateType_fields[estateType];
                if (typeof x != 'undefined') {
                    x = estateType_fields[estateType][estateRegType];
                    if (typeof x != 'undefined') {
                        $.each(x, function (index, obj) {
                            // console.log
                            if (obj != '1')
                                exceptions.push(index);
                        });
                    }
                }
            }
        }
        else {
            flag = false;
        }
    }
    else {
        flag = false;
    }


    if (!flag)
    //$('fieldset[id=]').prop('disabled', true);
        $('#estate_fields_list').hide();
    else
        $('#estate_fields_list').show();

    /* x = regType_fields;
     $.each(x, function (index, obj) {
     if (obj == '1') {
     if ($.inArray(index, exceptions) == -1)
     exceptions.push(index);
     }
     });*/
    $.each(exceptions, function (index, name) {
        _disable_fields(name);
    });

    _disableFiedlset();

    _repaint_fields();
}

/*function _disable_fields(name) {
    console.log(name);
    var el = $('input[name="transferFields[' + name + ']"],input[name="transferFields[' + name + '][]"],select[name="transferFields[' + name + ']"]');
//    el.prop('disabled', true);
//    if (el.parent().is('label'))
//        el = el.parent();

    if (el.closest('.form_element').parent().hasClass('hidden'))
        el.closest('.form_element').parent().addClass('hidden-disabled').removeClass('hidden');
    else
        el.closest('.form_element').parent().addClass('disabled');

    //---------------- hide empty parent fieldsets
//    var fieldset = el.closest('fieldset');
//    if(fieldset != 'undefined'){
//        var disabledChildren = $(fieldset).children('.disabled').not('legend');
//        var children = $(fieldset).children('.form_element,.items').not('legend');
//        if(disabledChildren.length && children.length && children.length == disabledChildren.length){
//            $(disabledChildren).show().removeClass('disabled').removeClass('hidden-disabled');
//            fieldset.addClass('disabled');
//        }
//    }
}*/

//function _repaint_fields() {
//    $('.disabled').hide(300);
//    $('.hidden').each(function () {
////        $('input,select', this).removeProp('disabled');
//        $(this).removeClass('hidden').show(300);
//        console.log($(this));
////        $('label', this).removeClass('ui-button-disabled');
//    });
//}
//function _reset_fields() {
//    $('.disabled').each(function () {
//        $(this).removeClass('disabled').addClass('hidden');
//    });
//}

