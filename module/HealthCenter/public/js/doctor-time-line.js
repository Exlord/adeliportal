/**
 * Created with PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 8/26/14
 * Time: 10:05 AM
 */
new Image().src = '/images/ajax_loader_tiny.gif';
if (typeof window.TimeLineDefaults == 'undefined') {
    var TimeLineDefaults = {
        reservable: true,
        resources: {
            reserveTitle: 'Reserve',
            reservedTitle: 'Reserved',
            startedTitle: 'Started',
            finishedTitle: 'Finished',
            visitedTitle: 'Visited'
        }
    };
}
var TimeLines = {
    itemWidth: false,
    wrapperWidth: 0,
    data: {},
    loader: "<span class='glyphicon ajax-loading-inline' style='width:16px;height:16px;'></span>",
    template: null,
    wrapper: null,
    load: function (btn, side) {
        var insertPosition = $(btn).closest('.time-line-item');
        var day = btn.data('day');
        if (typeof TimeLines.data[day] == 'undefined') {
            $(btn).addClass('disabled').prepend(TimeLines.loader);
            $.ajax({
                url: btn.attr('href'),
                data: {'justTimeLine': 1},
                complete: function () {
                    btn.removeClass('disabled').find('span').remove();
                },
                success: function (data) {

                    TimeLines.wrapperWidth += TimeLines.itemWidth;
                    TimeLines.wrapper.css({width: TimeLines.wrapperWidth});
                    TimeLines.data[day] = data;
                    data = $(data).css({width: TimeLines.itemWidth});

                    if (side == 'next') {
                        TimeLines.wrapper.css({left: '-=' + TimeLines.itemWidth});
                        $(data).insertBefore(insertPosition);
                        TimeLines.next();
                    } else {
                        $(data).insertAfter(insertPosition);
                        TimeLines.pre();
                    }
                },
                error: System.AjaxError
            });
        } else {
            var dayLength = $('#time-line-container-' + day).length;
            if (dayLength == 0)
                $(TimeLines.data[day]).css({width: TimeLines.itemWidth});
            if (side == 'next') {
                if (dayLength == 0)
                    $(TimeLines.data[day]).insertBefore(insertPosition);
                TimeLines.next();
            } else {
                if (dayLength == 0)
                    $(TimeLines.data[day]).insertAfter(insertPosition);
                TimeLines.pre();
            }
        }
    },
    init1: function () {
        var item = $('.time-line-item');
        this.data = {};
        TimeLines.data[item.data('day')] = item;
        TimeLines.wrapperWidth = TimeLines.itemWidth;
        item.css({width: TimeLines.itemWidth});
        $('.doctor-time-line').css(({width: TimeLines.itemWidth}));
        this.wrapper = $('.doctor-time-line-wrapper').css({width: TimeLines.itemWidth});
    },
    init2: function () {
        $('body')
            .on('click', '.next-day', function (e) {
                e.preventDefault();
                TimeLines.load($(this), 'next');
//                if (typeof TimeLines.data[$(this).data('day')] == 'undefined') {
//                    var btn = $(this).addClass('disabled').prepend(TimeLines.loader);
//                    $.ajax({
//                        url: btn.attr('href'),
//                        data: {'justTimeLine': 1},
//                        complete: function () {
//                            btn.removeClass('disabled').find('span').remove();
//                        },
//                        success: function (data) {
//                            TimeLines.wrapperWidth += TimeLines.itemWidth;
//                            $('.doctor-time-line-wrapper').css({width: TimeLines.wrapperWidth, left: '-=' + TimeLines.itemWidth});
//
//                            TimeLines.data[btn.data('day')] = data;
//                            $(data).css({width: TimeLines.itemWidth}).insertBefore($(btn).closest('.time-line-item'));
//                            TimeLines.next();
//                        },
//                        error: System.AjaxError
//                    });
//                } else {
//                    if ($('#time-line-container-' + $(this).data('day')).length == 0)
//                        $(TimeLines.data[$(this).data('day')]).css({width: TimeLines.itemWidth}).insertBefore($(this).closest('.time-line-item'));
//                    TimeLines.next();
//                }
            })
            .on('click', '.previews-day', function (e) {
                e.preventDefault();
                TimeLines.load($(this), 'pre');
//                if (typeof TimeLines.data[$(this).data('day')] == 'undefined') {
//                    var btn = $(this).addClass('disabled').append(TimeLines.loader);
//                    $.ajax({
//                        url: btn.attr('href'),
//                        data: {'justTimeLine': 1},
//                        complete: function () {
//                            btn.removeClass('disabled').find('span').remove();
//                        },
//                        success: function (data) {
//                            TimeLines.wrapperWidth += TimeLines.itemWidth;
//                            $('.doctor-time-line-wrapper').css({width: TimeLines.wrapperWidth});
//
//                            TimeLines.data[btn.data('day')] = data;
//                            $(data).css({width: TimeLines.itemWidth}).insertAfter($(btn).closest('.time-line-item'));
//                            TimeLines.pre();
//                        },
//                        error: System.AjaxError
//                    });
//                } else {
//                    if ($('#time-line-container-' + $(this).data('day')).length == 0)
//                        $(TimeLines.data[$(this).data('day')]).css({width: TimeLines.itemWidth}).insertAfter($(this).closest('.time-line-item'));
//                    TimeLines.pre();
//                }
            });
    },
    next: function () {
        $('.doctor-time-line-wrapper').animate({left: '+=' + TimeLines.itemWidth});
    },
    pre: function () {
        $('.doctor-time-line-wrapper').animate({left: '-=' + TimeLines.itemWidth});
    },
    makeTimeLine: function (container) {
        if (TimeLines.template == null) {
            var timeLineWrapper = $('<div class="time-line-wrapper panel panel-default"></div>');

            var hour = 1, left = 0, j, i, top, timeLabel, verticalLine,
                hourWidth = 100 / 6, horizontalLine;

            for (i = 1; i <= 4; i++) {
                horizontalLine = $('<div class="progress progress-bar-striped"></div>').appendTo(timeLineWrapper);
                top = (((i - 1) * 50) + 20) + 'px';
                horizontalLine.css({ top: top});
            }

            for (j = 1; j < 6; j++) {
                verticalLine = $('<div class="vertical-time-line"></div>').appendTo(timeLineWrapper);
                left = ((Math.floor(((j) * hourWidth) * 100) / 100) ) + '%';
                verticalLine.css({ left: left});
                hour++;
            }

            hour = 1;
            for (i = 1; i <= 4; i++) {
                for (j = 1; j <= 6; j++) {
                    timeLabel = $('<span class="time-label"></span>').text(hour + ':00').appendTo(timeLineWrapper);
                    top = (((i - 1) * 50) + 5) + 'px';
                    left = ((Math.floor(((j - 1) * hourWidth) * 100) / 100) + 1) + '%';
                    timeLabel.css({top: top, left: left});
                    hour++;
                }
            }
            TimeLines.template = timeLineWrapper;
        }
        var wrapper = TimeLines.template.clone();
        container.html(wrapper);
        return wrapper;
    }
};
jQuery.fn.TimeLine = function (options) {
    var defaults = {
        data: {}
    };
    var settings = $.extend({}, TimeLineDefaults, defaults, options);

    var container = $(this);
    var timeLineWrapper = TimeLines.makeTimeLine(container);

    var left = 0, top, sh, eh, em, width, maxWidth,
        reservation, hourWidth = 100 / 6, minuteWidth = 100 / 360, extraWidth;

    maxWidth = 21600;//6 hours in seconds
    $.each(settings.data, function (index, item) {
        sh = parseInt(item.sh);
        eh = parseInt(item.eh);
        em = parseInt(item.em);

        if (sh < 7) {
            top = 0;
            left = sh - 1;
        } else if (sh >= 7 && sh < 13) {
            top = 1;
            left = sh - 7;
        } else if (sh >= 13 && sh < 19) {
            top = 2;
            left = sh - 13;
        } else if (sh >= 19) {
            top = 3;
            left = sh - 19;
        }

        top = ((top * 50) + 20) + 'px';
        left = ((left * hourWidth) + (item.sm * minuteWidth)) + '%';

        width = ((item.width * 100) / maxWidth) + '%';

        reservation = $('<a class="btn btn-success time-button"></a>')
            .text(settings.resources.reserveTitle)
            .css({top: top, left: left, width: width}).appendTo(timeLineWrapper);

        if (item.resStatus && (item.resStatus == '0' || item.resStatus == '1'))
            reservation.addClass('disabled').text(settings.resources.reservedTitle);
        else if (item.status) {
            if (item.status == 1)
                reservation.addClass('disabled').text(settings.resources.startedTitle);
            else if (item.status == 2)
                reservation.addClass('disabled').text(settings.resources.finishedTitle);
        }

        if (settings.reservable) {
            reservation.attr('href', item.reserveUrl);
        } else {
            //reserved
            if (item.patientUrl && item.resStatus == '1' || item.resStatus == '5') {
                reservation.attr('href', item.patientUrl).removeClass('disabled').addClass('ajax_page_load');
            }
            else {
                reservation.addClass('disabled');
            }

            //visited
            if (item.resStatus == '5')
                reservation.text(settings.resources.visitedTitle).removeClass('btn-success').addClass('btn-info');
        }

        //check if the buttons width is not bigger than the wrappers bounds
        //if it is cut the button in 2 and add the second one to the next line
        extraWidth = false;
        if (sh <= 6 && eh > 6) {
            extraWidth = ((eh - 7) * 3600) + (em * 60);
            top = (50 + 20) + 'px';
        }
        else if (sh <= 12 && eh > 12) {
            extraWidth = ((eh - 13) * 3600) + (em * 60);
            top = ((2 * 50) + 20) + 'px';
        }
        else if (sh <= 18 && eh > 18) {
            extraWidth = ((eh - 19) * 3600) + (em * 60);
            top = ((3 * 50) + 20) + 'px';
        }
        else if (sh >= 19 && sh <= 24 && eh != 1) {
            extraWidth = ((eh - 1) * 3600) + (em * 60);
            top = (20) + 'px';
        }

        if (extraWidth) {
            width = (((item.width - extraWidth) * 100) / maxWidth) + '%';
            reservation.css({width: width}).text(' « ' + reservation.text());

            width = ((extraWidth * 100) / maxWidth) + '%';
            var extraButton = $('<a class="btn btn-success time-button"></a>')
                .text('»')
                .attr('href', '#')
                .css({top: top, left: 0, width: width}).appendTo(timeLineWrapper);

            if (reservation.hasClass('disabled'))
                extraButton.addClass('disabled');
        }
    });

    if (!TimeLines.itemWidth) {
        TimeLines.itemWidth = $('.time-line-item').width();
        TimeLines.init1();
        TimeLines.init2();
    }
};
$(document).trigger("timetableClassIsReady");
