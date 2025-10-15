/**
 * Created with PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 5/17/14
 * Time: 11:43 AM
 */
jQuery.fn.fancyFyBg = function (options) {
    var defaults = {
        debug: false,
        colors: ['13bef4', '00b9f2', '0bbcf2', 'f0ece9', 'eeedeb', '05baf1', 'f0eceb', 'f0ece9'],
        lines: {
            count: 15,
            minWidth: 2,
            maxWidth: 5,
            minHeight: 80,
            location: ['top', 'bottom']
        },
        circles: {
            count: 10,
            maxWidth: 150,
            minWidth: 10
        }
    };

    var log = function (val) {
        if (settings.debug && window.console)
            console.log(val);
    };

    var settings = $.extend({}, defaults, options);
    $(this).html('').css({height: $(this).parent().height(), position: 'absolute', width: '100%', 'z-index': -1, top: 0, left: 0});

    var getRandomInt = function (min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    };
    var getRandomNumber = function (min, max) {
        return Math.random() * (max - min) + min;
    };

    var maxX = $(this).width();
    var maxH = $(this).height();
//    log('maxH : ' + maxH);

    var color, x, i, animateParas, w, location, speed, opacity;

    for (i = 0; i < settings.lines.count; i++) {
        var line = $('<span></span>');
        color = settings.colors[getRandomInt(0, settings.colors.length)];
        x = getRandomInt(settings.lines.maxWidth + 5, maxX - 5 - settings.lines.maxWidth);
//        log('line x : ' + x);
        w = getRandomInt(settings.lines.minWidth, settings.lines.maxWidth);
//        log('line w : ' + w);
        opacity = getRandomNumber(0.2, 0.8);
        line.css({
            backgroundColor: '#' + color,
            left: x,
            height: 0,
            width: w,
            position: 'absolute',
            display: 'block',
            borderRadius: w / 2,
            opacity: opacity,
            zIndex: getRandomInt(-10, -2),
            boxShadow: '0px 0px ' + (1 - opacity) * 10 + 'px 0px #' + color
        });
        line.css(settings.lines.location[getRandomInt(0, 1)], 0);
        $(this).append(line);

        line.animate({'height': getRandomInt(settings.lines.minHeight, 100) + '%'}, getRandomInt(400, 2000));
    }

    for (i = 0; i < settings.circles.count; i++) {
        var circle = $('<span></span>');
        color = settings.colors[getRandomInt(0, settings.colors.length)];
        w = getRandomInt(settings.circles.minWidth, settings.circles.maxWidth);
//        log('circle w :' + w);
        x = getRandomInt(settings.circles.maxWidth + 5, maxX - 5 - settings.circles.maxWidth);
//        log('circle x :' + x);

        location = settings.lines.location[getRandomInt(0, 1)];
//        log('circle location :' + location);
        opacity = getRandomNumber(0.2, 1);
        circle.css({
            backgroundColor: '#' + color,
            left: x,
            height: 0,
            width: 0,
            position: 'absolute',
            display: 'block',
            borderRadius: '50%',
            opacity: opacity,
            zIndex: getRandomInt(-8, -1),
            boxShadow: '0px 0px ' + (1 - opacity) * 20 + 'px 0px #' + color
        });

        circle.css(location, 0);
        $(this).append(circle);

        speed = getRandomInt(1000, 2000);
        animateParas = {
            height: w,
            width: w,
            left: '-=' + (w / 2)
        };
        animateParas[location] = getRandomInt(settings.circles.maxWidth + 5, maxH - 5 - settings.circles.maxWidth);
//        log('circle animate params :' + animateParas.toSource());
        circle.animate(animateParas, speed);
    }
};