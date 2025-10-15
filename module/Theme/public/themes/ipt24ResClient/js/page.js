$(document).ready(function () {

    var loadResorce = true;

    $("img.lazy").lazyload({
        effect: "fadeIn"
    });

    /*var countPage = 1;
    var footer_top = $('.contact-bottom').offset().top;
    $(window).scroll(function () {
        var scrollTop = parseInt($(window).scrollTop());
        if (scrollTop > footer_top && countPage==1)
        {
            countPage +=1;
            var data = {};
            data['page'] = countPage;
            $.ajax({
                url: Projects.url,
                type: 'POST',
                data: data,
                complete: function () {
                },
                success: function (data) {
                    $('.view-projects').append(data);
                    footer_top = $('.contact-bottom').offset().top;
                }
            });
        }
    });*/


    $('.view-projects article').hover(function () {
        $(this).children('div').slideDown(200);
    }, function () {
        $(this).children('div').slideUp(600);
    });

    //view down
    $('.view-certificate').on('click', '.link-certificate', function (e) {
        e.preventDefault();
        if ($('.view-certificate').find('.box-view-certificate-slide')) {
            $(".box-view-certificate-slide").remove();
            $('.view-certificate ul li').css('height', 140);
        }
        if ($('.view-certificate').find('.arrow-icon-content'))
            $(".arrow-icon-content").remove();
        //  $(".view-certificate-slide").slideUp();
        var tag = $(this);
        tag.addClass('ajax-loading-inline-select');
        var url = $(this).data('url');
        $(this).parent().append('<div class="arrow-icon-content"></div>');
        $(this).parent().parent().append('<div class="box-view-certificate-slide"><div class="top-line-content"></div><div class="view-certificate-slide"></div></div>');
        $(".view-certificate-slide").load(url, function () {


            /*----------------------*/
            var css = $('#headLinkContainer').val();
            $('#headLinkContainer').remove();

            var js = $('#headScriptContainer').val();
            $('#headScriptContainer').remove();

            var inlineJs = $('#inlineScriptContainer').val();
            $('#inlineScriptContainer').remove();

            $('#flashMessengerContainer').remove();


            if (css && css.length && loadResorce) {
                $('body').append(css);
            }

            if (js && js.length && loadResorce) {
                $('body').append(js);
            }

            if (inlineJs && inlineJs.length) {
                var inlineScriptsContainer = '<div id="inline-script"></div>';
                if (!$('#inline-script').length)
                    $('body').append(inlineScriptsContainer);
                $('#inline-script').html(inlineJs).remove();
            }
            loadResorce = false;
            /*----------------------*/


            var heightCertificate = parseInt($(this).outerHeight());
            var heightComment = parseInt($('#comments-wrapper').outerHeight());
            if (!heightComment)
                heightComment = 0;
            $(this).parent().parent().css('height', heightCertificate + heightComment + 160);
            $(this).slideDown();
            tag.removeClass('ajax-loading-inline-select');
        });
    });
    //end

});