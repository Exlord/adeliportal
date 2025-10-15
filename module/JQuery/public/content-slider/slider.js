/**
 * Created with PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/15/14
 * Time: 4:02 PM
 */
var ContentSlider = {
    orientation: {Vertical: 'vertical', Horizontal: 'horizontal'},
    direction: {Left: 'left', Right: 'right', Top: 'top', Bottom: 'bottom'},
    autoScroll: {Yes: 'yes', No: 'no'},
    instances: {},
    lastIndex: 0,
    __resize: function (that, settings) {
        var itemWidth = 0;
        var itemHeight = 0;
        var itemCount = 0;

        //reset items and wrapper and viewport for resizing
        $('.item', that).css({width: '', height: ''});
        $('.wrapper', that).css({width: '', height: '', position: 'relative'});
        $('.viewport', that).css({width: '', height: ''});

        $('.item', that).each(function (el, index) {
            itemWidth = Math.max($(this).outerWidth(), itemWidth);
            itemHeight = Math.max($(this).outerHeight(), itemHeight);
            itemCount++;
        });

        settings.itemWidth = itemWidth;
        settings.itemHeight = itemHeight;
        settings.itemCount = itemCount;

        $('.item', that).css({width: itemWidth, height: itemHeight});

        var maxViewPortWidth = $('.content-slider-container').width();
        var maxViewPortHeight = $('.content-slider-container').height();

        if (settings.visibleCount && settings.visibleCount == 1) {
            if (settings.orientation == ContentSlider.orientation.Horizontal)
                settings.itemWidth = itemWidth = maxViewPortWidth;
            else if (settings.orientation == ContentSlider.orientation.Vertical)
                maxViewPortHeight = itemHeight;

            $('.item', that).css({width: settings.itemWidth, height: settings.itemHeight});
        }

        var viewPortWidth = itemWidth;
        var viewPortHeight = itemHeight;
        var wrapperWidth = itemWidth;
        var wrapperHeight = itemHeight;

        if (settings.orientation == ContentSlider.orientation.Horizontal) {
            wrapperWidth = itemWidth * itemCount;
            viewPortWidth = settings.visibleCount ? (itemWidth * settings.visibleCount) : maxViewPortWidth;
            if (viewPortWidth > maxViewPortWidth)
                viewPortWidth = maxViewPortWidth;

            settings.lastListSize = settings.visibleCount ? settings.visibleCount * itemWidth : maxViewPortWidth;
        }
        else if (settings.orientation == ContentSlider.orientation.Vertical) {
            wrapperHeight = itemHeight * itemCount;
            viewPortHeight = settings.visibleCount ? itemHeight * settings.visibleCount : maxViewPortHeight;
            if (viewPortHeight > maxViewPortHeight)
                viewPortHeight = maxViewPortHeight;

            settings.lastListSize = settings.visibleCount ? settings.visibleCount * itemHeight : maxViewPortHeight;
        }
        $('.wrapper', that).css({height: wrapperHeight, width: wrapperWidth, position: 'absolute', right: 0, top: 0});
        $('.viewport', that).css({width: viewPortWidth, height: viewPortHeight});
    },
    run: function (that, settings) {
        var directionalClass = '';
        if (settings.orientation == ContentSlider.orientation.Horizontal) {
            directionalClass = 'horizontal';
        }
        else if (settings.orientation == ContentSlider.orientation.Vertical) {
            directionalClass = 'vertical';
        }
        $(that).addClass(directionalClass);
        ContentSlider.__resize(that, settings);

        var instance = that;
        var instanceIndex = ContentSlider.lastIndex++;

        ContentSlider.instances[instanceIndex] = {el: instance, settings: settings};
        $(instance).data('content-slider-index', instanceIndex);

        $('.prev', that).click(function () {
            ContentSlider.prev(instance, settings);
        });
        $('.next', that).click(function () {
            ContentSlider.next(instance, settings);
        });

        if (settings.directionalNav == 3)
            $('.prev,.next', instance).show();

        $(that).hover(
            function () {
                ContentSlider.stop(instanceIndex);
                if (settings.directionalNav == 1)
                    $('.prev,.next', instance).fadeIn(300);
            },
            function () {
                if (settings.autocroll == ContentSlider.autoScroll.Yes) {
                    ContentSlider.start(instanceIndex);
                }
                if (settings.directionalNav == 1)
                    $('.prev,.next', instance).fadeOut(300);
            });

        if (settings.autocroll == ContentSlider.autoScroll.Yes) {
            ContentSlider.start(instanceIndex);
        }
        $(window).resize(function () {
            ContentSlider.__resize(that, settings);
        });

        $(that).removeClass('ajax-loading').find('img').addClass('img-responsive');
    },
    start: function (instanceIndex) {
        var instance = ContentSlider.instances[instanceIndex];
        instance.timer = setInterval(function () {
            ContentSlider.next(instance.el, instance.settings);
        }, instance.settings.interval);
    },
    stop: function (instanceIndex) {
        var instance = ContentSlider.instances[instanceIndex];
        clearInterval(instance.timer);
    },
    next: function (el, settings) {
        var wrapper = $('.wrapper', el);
        var prop = {};
        if (settings.orientation == ContentSlider.orientation.Horizontal) {
            if (wrapper.width() - settings.lastListSize <= -parseInt($(wrapper).css('right')))
                prop = {right: 0};
            else
                prop = {right: "-=" + settings.itemWidth * settings.slideCount};
        }
        else {
            if ((wrapper.height() - settings.lastListSize <= -parseInt($(wrapper).css('top'))))
                prop = {top: 0};
            else
                prop = {top: "-=" + (settings.itemHeight * settings.slideCount)};
        }

        wrapper.animate(prop, settings.speed);
    },
    prev: function (el, settings) {
        var wrapper = $('.wrapper', el);
        var prop = {};
        if (settings.orientation == ContentSlider.orientation.Horizontal) {
            if (parseInt($(wrapper).css('right')) < 0)
                prop = {right: "+=" + settings.itemWidth * settings.slideCount};
            else
                prop = {right: -(wrapper.width() - settings.lastListSize)};
        }
        else {
            if (parseInt($(wrapper).css('top')) < 0)
                prop = {top: "+=" + (settings.itemHeight * settings.slideCount)};
            else
                prop = {top: -(wrapper.height() - ( settings.lastListSize))};
        }

        wrapper.animate(prop, settings.speed);
    }
};
jQuery.fn.contentSlider = function (options) {
    var defaults = {
        orientation: ContentSlider.orientation.Horizontal,
        direction: ContentSlider.direction.Left,
        autocroll: ContentSlider.autoScroll.Yes,
        interval: 3000,
        speed: 500,
        directionalNav: 1,
        visibleCount: 0,
        slideCount: 1
    };
    var settings = $.extend({}, defaults, options);
    var that = this;
    $(window).load(function () {
        ContentSlider.run(that, settings);
    });
};