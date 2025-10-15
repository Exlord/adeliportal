$(document).ready(function () {
    var all_checked = false;
    var selector =
        '.real-estate-item .is-canceled,' +
            '.real-estate-item .is-transferred,' +
            '.real-estate-item-page .is-canceled,' +
            '.real-estate-item-page .is-transferred';
    $('body')
        .off('click', '#toggle_select_all')
        .on('click', '#toggle_select_all', function () {
            if (!all_checked) {
                $('.real-estate-item .real-estate-item-select').prop('checked', 'checked');
            } else {
                $('.real-estate-item .real-estate-item-select').removeProp('checked', 'checked');
            }
            all_checked = !all_checked;
        })
        .off('click', '.commands a.setting_button')
        .on('click', '.commands a.setting_button', function (e) {
            e.preventDefault();
            var id = $(this).data('id');

            var update_row = $('#update-row-' + id);
            update_row.slideToggle();
            modify_urls['#update-row-' + id] = $(this).prop('href');
        })
        .off('click', '.commands a.save_button')
        .on('click', '.commands a.save_button', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var container = $('.update-commands#update-row-' + id);
            container.addClass('ajax-loading-inline');
            var data = {};
            var expire = $('select[name=expire]', container);
            var status = $('select[name=status]', container);
            data['expire'] = expire.val();
            data['status'] = status.val();
            data['id'] = id;
            var afterstatus = parseInt(status.val());
            var afterexpire = parseInt(expire.val());
            var keyword = $(this);

            $.ajax({
                url: modify_urls['#update-row-' + id],
                type: 'POST',
                data: data,
                complete: function () {
                    container.removeClass('ajax-loading-inline');
                },
                success: function (data) {
                    if (data.status != 1) {

                        System.AjaxMessage(data);
                    }
                    else {
                        container.slideUp(function () {
                            if (real_estate_page_status != '')
                                container.parent().parent().parent().slideUp();
                            else {

                                var el = container.parent().parent();

                                var x = null;
                                $('select[name=status] option', container).show();
                                $('select[name=status] option:selected', container).hide();
                                $('select[name=expire] option', container).show();
                                $('select[name=expire] option:selected', container).hide();

                                if (afterstatus)
                                    $('.is-not-approved,.is-transferred,.is-canceled', el).remove();
                                switch (afterstatus) {
                                    case 0 :
                                        $('h1', el).append(notApproved);
                                        break;
                                    case 1 :
                                        break;
                                    case 3 :
                                        $('.real-estate-content', el).append(isTransfer);
                                        break;
                                    case 4 :
                                        $('.real-estate-content', el).append(isCancel);
                                        break;
                                }

                                $(keyword).attr('data-status', afterstatus);

                                switch (afterexpire) {
                                    case 0 :
                                        break;
                                    case -1 :
                                        if (!$('.is-expired', el).length)
                                            $('h1', el).append(isExpired);
                                        break;
                                    case 1 :
                                    case 3 :
                                    case 6 :
                                    case 12 :
                                        $('.is-expired', el).remove();
                                        break;
                                }
                            }
                            expire.val(0);
                            status.val('');
                        });
                    }
                },
                error: System.AjaxError
            });

        })
        .off('mouseenter', selector)
        .on('mouseenter', selector, function () {
            $(this).stop().transition({rotate: '15deg', right: $(this).parent().width() - 340 + 'px', top: '-40px' });
        })
        .off('mouseleave')
        .on('mouseleave', function () {
            $('.is-canceled,.is-transferred', $(this)).stop().transition({ top: '0px', rotate: '0deg', right: '35px' });
        });

    var modify_urls = {};

    $('.update-commands .save_button').each(function () {
        var status = $(this).data('status');
        $('select[name=status] option[value=' + status + ']', $(this).parent().parent()).hide();
        if ($('.is-expired', $(this).parent().parent().parent().parent()).length)
            $('select[name=expire] option[value="-1"]', $(this).parent().parent()).hide();
    });

});


