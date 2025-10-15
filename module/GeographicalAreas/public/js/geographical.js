/**
 * Created with JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/11/13
 * Time: 10:09 AM
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function () {
    $('body')
        .on('change', 'select.country-selector', function () {
            var id = $(this).data('stateid').replace('.', '\\.');
            var state = $('select.state-selector#' + id);
            if (!state.length) {
                state = $('select[name=' + id + ']');
            }
            var parent = $(state).parent();
            var tag = $(this);
            tag.parent().addClass('ajax-loading-inline-select');
            var country = $(this).val();
            if (country == '')
                country = 1;
            $.ajax({
                url: '/geographical-areas-get-state-list/' + country,
                type: 'GET',
                success: function (data) {
                    tag.parent().removeClass('ajax-loading-inline-select');
                    $(state).html(data);
                },
                error: System.AjaxError
            });
        })
        .on('change', 'select.state-selector', function () {
            var id = $(this).data('cityid').replace('.', '\\.');
            var city = $('select.city-selector#' + id);
            if (!city.length) {
                city = $('select[name=' + id + ']');
            }
            var tag = $(this);
            var parent = $('#' + id).parent();
            tag.parent().addClass('ajax-loading-inline-select');
            var state = $(this).val();
            if (state == '')
                state = 0;
            $.ajax({
                url: '/geographical-areas-get-city-list/' + state,
                type: 'GET',
                success: function (data) {
                    tag.parent().removeClass('ajax-loading-inline-select');
                    $(city).html(data);
                },
                error: System.AjaxError
            });
        })
        .on('change', 'select.city-selector', function () {
            var id = $(this).data('areaid');
            var area = $('select.area-selector#' + id);
            if (!area.length) {
                area = $('select[name=' + id + ']');
            }
            if (area.length) {
                var parent = $(area).parent();
                parent.addClass('ajax-loading-inline');
                var city = $(this).val();
                if (city == '')
                    city = 0;
                $.ajax({
                    url: '/geographical-areas-get-area-list/' + city,
                    type: 'GET',
                    success: function (data) {
                        parent.removeClass('ajax-loading-inline');
                        $(area).html(data);
                    },
                    error: System.AjaxError
                });
            }
        })
        .on('change', 'select.area-selector', function () {
            var id = $(this).data('subareaid');
            var subArea = $('select.sub-area-selector#' + id);
            if (subArea.length) {
                var parent = $(subArea).parent();
                parent.addClass('ajax-loading-inline');
                var area = $(this).val();
                if (area == '')
                    area = 0;
                $.ajax({
                    url: '/geographical-areas-get-sub-area-list/' + area,
                    type: 'GET',
                    success: function (data) {
                        parent.removeClass('ajax-loading-inline');
                        $(subArea).html(data);
                    },
                    error: System.AjaxError
                });
            }
        });
});


