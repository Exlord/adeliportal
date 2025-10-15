$(document).ready(function () {

    onFilterRegTypeChanged(1);

    NumOnly('.num');

    $('.next-button').click(function () {
        var id = parseInt($(this).data('id'));
        var error = false;
        switch (id) {
            case 1:
                if ($('#ownerName').val().length < 1) {
                    $('#ownerName').addClass('error');
                    error = true;
                } else
                    $('#ownerName').removeClass('error');

                if (parseInt($('#expire').val()) < 1) {
                    $('#expire').addClass('error');
                    error = true;
                } else
                    $('#expire').removeClass('error');

                /*if ($('#ownerEmail').val().length < 1) {
                 $('#ownerEmail').addClass('error');
                 error = true;
                 } else
                 $('#ownerEmail').removeClass('error');*/

                if ($('#estateArea').val().length < 1) {
                    $('#estateArea').addClass('error');
                    error = true;
                } else
                    $('#estateArea').removeClass('error');

                if ($('#addressShort').val().length < 1) {
                    $('#addressShort').addClass('error');
                    error = true;
                } else
                    $('#addressShort').removeClass('error');

                if ($('#addressFull').val().length < 1) {
                    $('#addressFull').addClass('error');
                    error = true;
                } else
                    $('#addressFull').removeClass('error');

                if ($('#ownerMobile').val().length < 1) {
                    $('#ownerMobile').addClass('error');
                    error = true;
                } else
                    $('#ownerMobile').removeClass('error');
                break;
            case 3:
                if ($('#captcha').val().length < 1) {
                    $('#captcha').addClass('error');
                    error = true;
                }
                break;
        }

        if (!error) {
            $('#step' + id).fadeOut(300, function () {
                $('#stepIMG').removeClass('step' + id + '-img');
                id += 1;
                if (parseInt(isRequest))
                    id += 1;
                $('#step' + id).fadeIn(600);
                $('#stepIMG').addClass('step' + id + '-img');
            });
        }

    });

    $('.back-button').click(function () {
        var id = parseInt($(this).data('id'));
        $('#step' + id).fadeOut(300, function () {
            $('#stepIMG').removeClass('step' + id + '-img');
            id -= 1;
            if (parseInt(isRequest))
                id -= 1;
            $('#step' + id).fadeIn(600);
            $('#stepIMG').addClass('step' + id + '-img');
        });
    });

    $(".fileDelete").click(function (e) {
        e.preventDefault();
        var classItem = $(this);
        $.ajax({
            type: "GET",
            url: $(this).data('src')
        }).success(function (msg) {
                if (msg.status) {
                    classItem.parent().next().next().attr('value', '');
                    classItem.parent().remove();
                }
                else
                    System.AjaxMessage(error_message)
            });
    });

    $('#regType').change(function () {

        var numreg = parseInt($('#regType').val());
        switch (numreg) {
            case 2 :
                $('#priceOneMeter').val('');
                $('#totalPrice').val('');
                break;
            case 1 :
            case 3 :
            case 4 :
            default :
                $('#mortgagePrice').val('');
                $('#rentalPrice').val('');
                break;
        }
        onFilterRegTypeChanged(numreg);
    });

    $('#openNewArea').click(function () {
        switch (openNewArea) {
            case 1:
                $('.select-new-area').hide();
                $('.text-new-area').show();
                openNewArea = 2;
                break;
            case 2:
                $('.text-new-area').hide();
                $('.select-new-area').show();
                openNewArea = 1;
                break;
        }

    });

    //------------------------------------googlemap---------------------
    var Lat = 0;
    var Long = 0;
    $('#find-google').click(function () {
        var height = $(document).clientHeight;
        $('#map-dialog').dialog({
            autoOpen: true,
            modal: true,
            height: 550,
            width: 800,
            top: 60
        });
        $('#select_return').button();
        $('body').on('click', '#select_return', function () {
            var loc = $('#map_selected_loc').text();
            $('#googleLatLong').val(loc);
            $('#google_text').html('Lat: ' + Lat + '<br/>Longitude: ' + Long);
            $('#map-dialog').dialog('close');
        });
        $('#select_cancel').button();
        $('body').on('click', '#select_cancel', function () {
            $('#googleLatLong').val('0');
            $('#google_text').html('');
            $('#map-dialog').dialog('close');
        });

        //key = AIzaSyCHHINs3vpJPyfIwv5u2pOB4QWlfuBr4Lg
        var myOptions = {
            zoom: 12,
            mapTypeId: google.maps.MapTypeId.ROADMAP

        };
        var map = new google.maps.Map(document.getElementById("map"), myOptions);
        var geocoder = new google.maps.Geocoder();

        //  var city = $('#city_list option:selected').text();

        geocoder.geocode({'address': 'tabriz'}, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                map.setCenter(results[0].geometry.location);
            }
        });

        google.maps.event.addListener(map, 'click', function (event) {
            Lat = event.latLng.lat();
            Long = event.latLng.lng();
            $('#map_selected_loc').text(event.latLng.lat() + ',' + event.latLng.lng());
        });
    });
    //-----------------end--------------------------

    //----------------------------------------for comma annd num only ------
    $('.withcomma').each(function () {
        $(this).keyup(function () {
            numFormat(this);
        })
    });
//-----------------------------end----------------------
//----------------------------for change total price and oneprice and meter with change Each
    var m = $('#estateArea');
    var mm = $('#priceOneMeter');
    var t = $('#totalPrice');


    m.change(function () {
        if ($('#regType').val() != 2) {
            mm.val(removecomma(mm.val()));
            t.val(removecomma(t.val()));

            if (m.val() && mm.val())
                t.val(mm.val() * m.val());
            if (t.val() && m.val() && m.val() != 0)
                mm.val(t.val() / m.val());
            if (m.val() == "")
                t.val(0);

            mm.val(insertcomma(mm.val()));
            t.val(insertcomma(t.val()));
        }

    });
    mm.change(function () {
        if ($('#regType').val() != 2) {

            mm.val(removecomma(mm.val()));
            t.val(removecomma(t.val()));


            if (m.val() && mm.val())
                t.val(mm.val() * m.val());
            if (t.val() && mm.val() && mm.val() != 0)
                m.val(t.val() / mm.val());
            if (mm.val() == "")
                t.val(0);

            mm.val(insertcomma(mm.val()));
            t.val(insertcomma(t.val()));
        }

    });
    t.change(function () {
        if ($('#regType').val() != 2) {


            if (isEmpty(m) || isEmpty(mm)) {
                mm.val(removecomma(mm.val()));
                t.val(removecomma(t.val()));

                if (t.val() && m.val() && m.val() != 0)
                    mm.val(t.val() / m.val());
                if (t.val() && mm.val() && mm.val() != 0)
                    m.val(t.val() / mm.val());

                mm.val(insertcomma(mm.val()));
                t.val(insertcomma(t.val()));
            }
        }
    });
    //--------------------end------------------------

});


function isEmpty(el) {
    if (el.val() == "" || el.val() == 0)
        return true;
    return false;
}

function removecomma(vals) {
    return vals.split(',').join('');
}
function insertcomma(vals) {
    var regex = /(\d)((\d{3},?)+)$/;
    while (regex.test(vals)) {
        vals = vals.replace(regex, '$1,$2');
    }
    return vals;
}

function _disable_fields(name) {
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
    var fieldset = el.closest('fieldset');
    if(fieldset != 'undefined'){
        var disabledChildren = $(fieldset).children('.disabled').not('legend');
        var children = $(fieldset).children('.form_element,.items').not('legend');
        if(disabledChildren.length && children.length && children.length == disabledChildren.length){
            $(disabledChildren).show().removeClass('disabled').removeClass('hidden-disabled');
            fieldset.addClass('disabled');
        }
    }
}