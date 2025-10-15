$(document).ready(function () {

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
});