if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
    var msViewportStyle = document.createElement("style");
    msViewportStyle.appendChild(
        document.createTextNode(
            "@-ms-viewport{width:auto!important}"
        )
    );
    document.getElementsByTagName("head")[0].appendChild(msViewportStyle);
}


$(document).ready(function () {

    $('.topItem').click(function () {
        if ($(this).hasClass('topItemDown'))
            $(this).removeClass('topItemDown').addClass('topItemUp');
        else if ($(this).hasClass('topItemUp'))
            $(this).removeClass('topItemUp').addClass('topItemDown');
        $(this).next().slideToggle();
    });

    $(document).on('nivoSliderLoaded', function (e) {
        var height = $(e.slider).height();
        $('.middle2-menu .navigation li').css('height', parseInt(height / 6) - 1);
    });

    $('.buttons').buttonset();

    if ($('#body').find('#dynamic_form_template_4 #part1')) {
        $('#dynamic_form_template_4 #buttons_submit').hide();
        $('#dynamic_form_template_4 #part2').hide();
        $('#dynamic_form_template_4 #part3').hide();
        $('#dynamic_form_template_4 #part4').hide();
    }

    $('#dynamic_form_template_4 .dynamic-form-link').click(function (e) {
        e.preventDefault();
        var flagForm4 = true;
        $("html,body").animate({scrollTop: $('#dynamic_form_template_4').offset().top - 50 }, 1000);
        var id = parseInt($(this).data('id'));
        var end = parseInt($(this).data('end'));
        var home = parseInt($(this).data('in'));
        $('#part' + home + " table td:contains('*')").each(function () {
            if (!$(this).next().find('input').val()) {
                $(this).next().find('input').css('border', '1px solid #cc0000').css('background', '#FBE2E2');
                flagForm4 = false;
            } else {
                $(this).next().find('input').css('border', '1px solid #51af4f').css('background', '#DBFFD9');
            }
        });
        if (flagForm4) {
            $('#dynamic_form_template_4 .top-header-img .img_part' + home).hide();
            $('#dynamic_form_template_4 .top-header-img .img_part' + id).css('display', 'block');
            $('#dynamic_form_template_4 #part' + home).hide();
            $('#dynamic_form_template_4 #part' + id).fadeIn(600);
            if (end == 1)
                $('#dynamic_form_template_4 #buttons_submit').show();
            else
                $('#dynamic_form_template_4 #buttons_submit').hide();
        }
    });

    if ($('#body').find('#dynamic_form_template_3 #part1')) {
        $('#dynamic_form_template_3 #buttons_submit').hide();
        $('#dynamic_form_template_3 #part2').hide();
        $('#dynamic_form_template_3 #part3').hide();
        $('#dynamic_form_template_3 #part4').hide();
    }

    $('#dynamic_form_template_3 .dynamic-form-link').click(function (e) {
        e.preventDefault();
        var flagForm3 = true;
        $("html,body").animate({scrollTop: $('#dynamic_form_template_3').offset().top - 50 }, 1000);
        var id = parseInt($(this).data('id'));
        var end = parseInt($(this).data('end'));
        var home = parseInt($(this).data('in'));
        $('#part' + home + " table td:contains('*')").each(function () {
            if (!$(this).next().find('input').val()) {
                $(this).next().find('input').css('border', '1px solid #cc0000').css('background', '#FBE2E2');
                flagForm3 = false;
            } else {
                $(this).next().find('input').css('border', '1px solid #51af4f').css('background', '#DBFFD9');
            }
        });
        if (flagForm3) {
            $('#dynamic_form_template_3 .top-header-img .img_part' + home).hide();
            $('#dynamic_form_template_3 .top-header-img .img_part' + id).css('display', 'block');
            $('#dynamic_form_template_3 #part' + home).hide();
            $('#dynamic_form_template_3 #part' + id).fadeIn(600);
            if (end == 1)
                $('#dynamic_form_template_3 #buttons_submit').show();
            else
                $('#dynamic_form_template_3 #buttons_submit').hide();
        }
    });


    if ($('#body').find('#dynamic_form_template_1 #part1')) {
        $('#dynamic_form_template_1 #buttons_submit').hide();
        $('#dynamic_form_template_1 #part2').hide();
        $('#dynamic_form_template_1 #part3').hide();
        $('#dynamic_form_template_1 #part4').hide();
    }

    $('#dynamic_form_template_1 .dynamic-form-link').click(function (e) {
        e.preventDefault();
        $("html,body").animate({scrollTop: $('#dynamic_form_template_1').offset().top - 50 }, 1000);
        var flagForm1 = true;
        var id = parseInt($(this).data('id'));
        var end = parseInt($(this).data('end'));
        var home = parseInt($(this).data('in'));

        $('#part' + home + " table td:contains('*')").each(function () {
            if (!$(this).next().find('input').val()) {
                $(this).next().find('input').css('border', '1px solid #cc0000').css('background', '#FBE2E2');
                flagForm1 = false;
            } else {
                $(this).next().find('input').css('border', '1px solid #51af4f').css('background', '#DBFFD9');
            }
        });
        if (flagForm1) {
            $('#dynamic_form_template_1 .top-header-img .img_part' + home).hide();
            $('#dynamic_form_template_1 .top-header-img .img_part' + id).css('display', 'block');
            $('#dynamic_form_template_1 #part' + home).hide();
            $('#dynamic_form_template_1 #part' + id).fadeIn(600);
            if (end == 1)
                $('#dynamic_form_template_1 #buttons_submit').show();
            else
                $('#dynamic_form_template_1 #buttons_submit').hide();
        }
    });

    if ($('#body').find('#dynamic_form_template_2 #part1')) {
        $('#dynamic_form_template_2 #buttons_submit').hide();
        $('#dynamic_form_template_2 #part2').hide();
        $('#dynamic_form_template_2 #part3').hide();
        $('#dynamic_form_template_2 #part4').hide();
    }

    $('#dynamic_form_template_2 .dynamic-form-link').click(function (e) {
        e.preventDefault();
        var flagForm2 = true;
        $("html,body").animate({scrollTop: $('#dynamic_form_template_2').offset().top - 50 }, 1000);
        var id = parseInt($(this).data('id'));
        var end = parseInt($(this).data('end'));
        var home = parseInt($(this).data('in'));
        $('#part' + home + " table td:contains('*')").each(function () {
            if (!$(this).next().find('input').val()) {
                $(this).next().find('input').css('border', '1px solid #cc0000').css('background', '#FBE2E2');
                flagForm2 = false;
            } else {
                $(this).next().find('input').css('border', '1px solid #51af4f').css('background', '#DBFFD9');
            }
        });
        if (flagForm2) {
            $('#dynamic_form_template_2 .top-header-img .img_part' + home).hide();
            $('#dynamic_form_template_2 .top-header-img .img_part' + id).css('display', 'block');
            $('#dynamic_form_template_2 #part' + home).hide();
            $('#dynamic_form_template_2 #part' + id).fadeIn(600);
            if (end == 1)
                $('#dynamic_form_template_2 #buttons_submit').show();
            else
                $('#dynamic_form_template_2 #buttons_submit').hide();
        }
    });


});
