$(document).ready(function () {

    onFilterRegTypeChanged(1);

    NumOnly('.num');

//    $('.next-button').click(function () {
//        var id = parseInt($(this).data('id'));
//        var error = false;
//        switch (id) {
//            case 1:
//                if ($('#ownerName').val().length < 1) {
//                    $('#ownerName').addClass('error');
//                    error = true;
//                }
//
//                if ($('#ownerEmail').val().length < 1) {
//                    $('#ownerEmail').addClass('error');
//                    error = true;
//                }
//
//                if ($('#estateArea').val().length < 1) {
//                    $('#estateArea').addClass('error');
//                    error = true;
//                }
//                if ($('#addressShort').val().length < 1) {
//                    $('#addressShort').addClass('error');
//                    error = true;
//                }
//                if ($('#addressFull').val().length < 1) {
//                    $('#addressFull').addClass('error');
//                    error = true;
//                }
//                if ($('#ownerMobile').val().length < 1) {
//                    $('#ownerMobile').addClass('error');
//                    error = true;
//                }
//                break;
//            case 3:
//                if ($('#captcha').val().length < 1) {
//                    $('#captcha').addClass('error');
//                    error = true;
//                }
//                break;
//        }
//
//        if (!error) {
//            $('#step' + id).fadeOut(300, function () {
//                $('#stepIMG').removeClass('step' + id + '-img');
//                id += 1;
//                $('#step' + id).fadeIn(600);
//                $('#stepIMG').addClass('step' + id + '-img');
//            });
//        }
//
//    });

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

    //------------------------------------googlemap---------------------
    var Lat = 0;
    var Long = 0;


    var myOptions = {
        zoom: 11,
        center: new google.maps.LatLng(38.071176553984856, 46.30840301513672)
        // mapTypeId: google.maps.MapTypeId.ROADMAP

    };
    var map = new google.maps.Map(document.getElementById("map"), myOptions);


    google.maps.event.addListener(map, 'click', function (event) {
        Lat = event.latLng.lat();
        Long = event.latLng.lng();
        $('#map_selected_loc').text(event.latLng.lat() + ',' + event.latLng.lng());
        $('#googleLatLong').val(event.latLng.lat() + ',' + event.latLng.lng());
        placeMarker(event.latLng);
    });
    function placeMarker(location) {
        var marker = new google.maps.Marker({
            position: location,
            map: map
        });
    }
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

function _disable_fields(name) {
    var el = $('input[name="transferFields[' + name + ']"],input[name="transferFields[' + name + '][]"],select[name="transferFields[' + name + ']"]');
    if ($('#real_state_form').find('.f_r_slider').length < 1)
        el.closest('.form_element').parent().addClass('disabled').removeClass('enabled');

}
function _repaint_fields() {
    $('.disabled').hide(300);
    $('.enabled').show(300);
}
function _reset_fields() {
    $('.disabled').each(function () {
        $(this).removeClass('disabled').addClass('enabled');
    });
}
function _disableFiedlset() {
    $('.base_fields').each(function () {
        if ($(this) != 'undefined') {
            var disabledChildren2 = $('> div > fieldset', $(this)).children().not(".disabled,legend");
            if (disabledChildren2.length == 0) {
                $('> div > fieldset', $(this)).children().not('legend').show().removeClass('disabled');
                $(this).addClass('disabled').removeClass('enabled');
            }
        }
    });
}
function onFilterRegTypeChanged(el) {
    if ($('#real_state_form').find('.f_r_slider').length > 0) {
        switch (parseInt(el)) {
            case 2 :
                $('#transferFields_lower_rent').parent().parent().show();
                $('#transferFields_highest_rent').parent().parent().hide();
                $('#transferFields_lower_rahn').parent().parent().show();
                $('#transferFields_highest_rahn').parent().parent().hide();
                $('#transferFields_lower_price').parent().parent().hide();
                $('#transferFields_highest_price').parent().parent().hide();
                break;
            default :
                $('#transferFields_lower_rent').parent().parent().hide();
                $('#transferFields_highest_rent').parent().parent().hide();
                $('#transferFields_lower_rahn').parent().parent().hide();
                $('#transferFields_highest_rahn').parent().parent().hide();
                $('#transferFields_lower_price').parent().parent().show();
                $('#transferFields_highest_price').parent().parent().hide();
                break
        }
    } else {
        switch (parseInt(el)) {
            case 2 :
                $('#mortgagePrice').parent().parent().parent().show();
                $('#rentalPrice').parent().parent().parent().show();
                $('#priceOneMeter').parent().parent().parent().hide();
                $('#totalPrice').parent().parent().parent().hide();
                break;
            default :
                $('#mortgagePrice').parent().parent().parent().hide();
                $('#rentalPrice').parent().parent().parent().hide();
                $('#priceOneMeter').parent().parent().parent().show();
                $('#totalPrice').parent().parent().parent().show();
                break
        }
    }
}