$(document).ready(function () {

    var loadResorce = true;

    $("img.lazy").lazyload({
        effect: "fadeIn"
    });


    var flagLoaf = true;
    var countPage = 2;
    $(window).scroll(function () {
        var height = parseInt($('.view_projects_index').parent().outerHeight()) - 700;
        if ($(this).scrollTop() >= height && flagLoaf == true && countPage <= Projects.maxPage) {
            flagLoaf = false;
            $('.view-projects-loader').addClass('ajax-loading');
            var data = {};
            data['page'] = countPage;
            $.ajax({
                url: Projects.url,
                type: 'POST',
                data: data,
                complete: function () {
                    $('.view-projects-loader').removeClass('ajax-loading');
                },
                success: function (data) {
                    $('.view_projects_index').append(data.html);
                    $("img.lazy").lazyload({
                        effect: "fadeIn"
                    });
                    setTimeout(function () {
                        flagLoaf = true;
                    }, 1000);
                    countPage ++;
                }
            });
        }
    });


   /* $('.view-projects article').hover(function () {
        $(this).children('div').slideDown(200);
    }, function () {
        $(this).children('div').slideUp(600);
    });*/

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