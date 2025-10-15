$(document).ready(function () {

    NumOnly('.num');

    addStar();

    var flagLoadGoogleMap = false;
    $('.next-button').click(function () {
        $("html,body").animate({scrollTop: $('#real_estate_form_page').offset().top - 50 }, 1000);
        $('#real_estate_form_page .back_loading').fadeIn(300);
        var regtype = parseInt($('#regType').val());
        var estateType = parseInt($('#estateType').val());
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

//                if ($('#addressShort').val().length < 1) {
//                    $('#addressShort').addClass('error');
//                    error = true;
//                } else
//                    $('#addressShort').removeClass('error');

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
                if ($('#ownerPhone').val().length < 1) {
                    $('#ownerPhone').addClass('error');
                    error = true;
                } else
                    $('#ownerPhone').removeClass('error');
                if ($('#ownerEmail').val().length > 0) {
                    if (!checkMail($('#ownerEmail').val())) {
                        $('#ownerEmail').addClass('error');
                        error = true;
                    } else
                        $('#ownerEmail').removeClass('error');
                } else
                    $('#ownerEmail').removeClass('error');
                break;
            case 2:
                if (estateType == 45) {
                    if ($('#transferFields_zirbana').val().length < 1) {
                        $('#transferFields_zirbana').addClass('error');
                        error = true;
                    } else
                        $('#transferFields_zirbana').removeClass('error');

                    if ($('#transferFields_s_m_kolle_tabaghat').val().length < 1) {
                        $('#transferFields_s_m_kolle_tabaghat').addClass('error');
                        error = true;
                    } else
                        $('#transferFields_s_m_kolle_tabaghat').removeClass('error');

                    if ($('#transferFields_s_m_dar_tabaghe').val().length < 1) {
                        $('#transferFields_s_m_dar_tabaghe').addClass('error');
                        error = true;
                    } else
                        $('#transferFields_s_m_dar_tabaghe').removeClass('error');

                    if ($('#transferFields_s_m_vahed_dar_tabaghe').val().length < 1) {
                        $('#transferFields_s_m_vahed_dar_tabaghe').addClass('error');
                        error = true;
                    } else
                        $('#transferFields_s_m_vahed_dar_tabaghe').removeClass('error');
                }
                if (regtype == 1) {
                    if ($('#priceOneMeter').val().length < 1) {
                        $('#priceOneMeter').addClass('error');
                        error = true;
                    } else
                        $('#priceOneMeter').removeClass('error');

                    if ($('#totalPrice').val().length < 1) {
                        $('#totalPrice').addClass('error');
                        error = true;
                    } else
                        $('#totalPrice').removeClass('error');
                }
                else if (regtype == 2) {
                    if ($('#mortgagePrice').val().length < 1) {
                        $('#mortgagePrice').addClass('error');
                        error = true;
                    } else
                        $('#mortgagePrice').removeClass('error');

                    if ($('#rentalPrice').val().length < 1) {
                        $('#rentalPrice').addClass('error');
                        error = true;
                    } else
                        $('#rentalPrice').removeClass('error');
                }
                break;
            case 4:
                if ($('#captcha').val().length < 1) {
                    $('#captcha').addClass('error');
                    error = true;
                }
                break;
        }
        setTimeout(function () {
            if (!error) {
                $('#step' + id).fadeOut(300, function () {
                    id += 1;
                    $('#step' + id).fadeIn(600);
                    if (id == 4 && !flagLoadGoogleMap) {
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

                        flagLoadGoogleMap = true;
                    }
                });
            }
            $('#real_estate_form_page .back_loading').fadeOut(300);
        }, 1500);

    });

    $('.back-button').click(function () {
        var id = parseInt($(this).data('id'));
        $('#step' + id).fadeOut(300, function () {
            $("html,body").animate({scrollTop: $('#real_estate_form_page').offset().top - 50 }, 1000);
            id -= 1;
            $('#step' + id).fadeIn(600);
            if (id == 1) {
                $('#transferFields_zirbana').val(0);
                $('#transferFields_masahate_zamin').val(0);
                $('#priceOneMeter').val(0);
                $('#totalPrice').val(0);
            }
        });
    });

    $(".fileDelete").click(function (e) {
        e.preventDefault();
        $('.error').each(function () {
            $(this).removeClass('error');
        });
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

    $('#openNewArea').click(function (e) {
        e.preventDefault();
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

    $('#areaId').change(function () {
        var thisTag = $(this);
        thisTag.addClass('ajax-loading-inline-select');
        var data = {};
        data['areaId'] = $(this).val();
        data['areaText'] = $(':selected', this).html();
        $.ajax({
            url: statistic_url,
            type: 'POST',
            data: data,
            complete: function () {
                thisTag.removeClass('ajax-loading-inline-select');
            },
            success: function (data) {
                if (data.status) {
                    $('.content_st').html(data.html);
                    if (!$('.box_statistic').hasClass('show-ajax'))
                        $('.box_statistic').animate({left: "+=70"}, 600).addClass('show-ajax');
                }
            },
            error: System.AjaxError
        });
    });

    $('.withcomma').each(function () {
        $(this).keyup(function () {
            numFormat(this);
        })
    });


    var m = $('#transferFields_zirbana');
    $('#estateType').change(function () {
        if (parseInt($(this).val()) == 45 || parseInt($(this).val()) == 83 || parseInt($(this).val()) == 84)
            m = $('#transferFields_zirbana');
        else
            m = $('#transferFields_masahate_zamin');

    });

//-----------------------------end----------------------
//----------------------------for change total price and oneprice and meter with change Each

    var mm = $('#priceOneMeter');
    var t = $('#totalPrice');

    m.focusout(function () {
        if ($('#regType').val() != 2) {
            var r_c_mm = removecomma(mm.val());
            var r_c_t = removecomma(t.val());
            var r_c_m = parseInt(m.val());
            if (r_c_m && r_c_mm)
                t.val(insertcomma(r_c_mm * r_c_m));
            if (r_c_t && r_c_m && r_c_m != 0 && mm == 0)
                mm.val(insertcomma(r_c_t / r_c_m));
            if (r_c_m == "")
                t.val(0);
        }

    });
    mm.focusout(function () {
        if ($('#regType').val() != 2) {
            var r_c_mm = removecomma(mm.val());
            var r_c_t = removecomma(t.val());
            var r_c_m = parseInt(m.val());
            if (r_c_m && r_c_mm)
                t.val(insertcomma(r_c_mm * r_c_m));
            if (r_c_t && r_c_mm && r_c_mm != 0 && m == 0)
                m.val(r_c_t / r_c_mm);
            if (mm.val() == "")
                t.val(0);
        }

    });
    t.focusout(function () {
        if ($('#regType').val() != 2) {
            if (isEmpty(m) || isEmpty(mm)) {
                var r_c_mm = removecomma(mm.val());
                var r_c_t = removecomma(t.val());
                var r_c_m = parseInt(m.val());
                if (r_c_t && r_c_m && r_c_m != 0)
                    mm.val(insertcomma(r_c_t / r_c_m));
                if (r_c_t && r_c_mm && r_c_mm != 0 && m == 0)
                    m.val(r_c_t / r_c_mm);
            }
        }
    });
//--------------------end------------------------


    //----------------------------------------for comma and num only ------


});

$(window).bind("load", function () {
    $('#real_estate_form_page .back_loading').fadeOut(300);
});


function isEmpty(el) {
    if (el.val() == "" || el.val() == 0)
        return true;
    return false;
}
/*function removecomma(vals) {
 return vals.split(',').join('');
 }
 function insertcomma(vals) {
 var regex = /(\d)((\d{3},?)+)$/;
 while (regex.test(vals)) {
 vals = vals.replace(regex, '$1,$2');
 }
 return vals;
 }*/
function checkMail(email) {
    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (filter.test(email)) {
        return true;
    }
    return false;
}

function addStar() {
    $('#transferFields_zirbana').parent().prepend('<span class="required" title="isEmpty" rel="tooltip">*</span>');
    $('#transferFields_s_m_kolle_tabaghat').parent().prepend('<span class="required" title="isEmpty" rel="tooltip">*</span>');
    $('#transferFields_s_m_dar_tabaghe').parent().prepend('<span class="required" title="isEmpty" rel="tooltip">*</span>');
    $('#transferFields_s_m_vahed_dar_tabaghe').parent().prepend('<span class="required" title="isEmpty" rel="tooltip">*</span>');
    $('#priceOneMeter').parent().parent().prepend('<span class="required" title="isEmpty" rel="tooltip">*</span>');
    $('#mortgagePrice').parent().parent().prepend('<span class="required" title="isEmpty" rel="tooltip">*</span>');
    $('#rentalPrice').parent().parent().prepend('<span class="required" title="isEmpty" rel="tooltip">*</span>');
    $('#totalPrice').parent().parent().prepend('<span class="required" title="isEmpty" rel="tooltip">*</span>');
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


