var FancyGallery = {
    speed: 4000,
    index: 0,
    instances: {},
    run: function () {
        $('.gallery-slide .item').each(function () {

            var firstItem = $('img', this).first();
            $(firstItem).prop('src', $(firstItem).data('src'));

            var instance = this;
            var index = FancyGallery.index;
            FancyGallery.instances[FancyGallery.index] = {item: instance, timer: null, loaded: false};
            $(this).hover(
                function () {
                    if (!FancyGallery.instances[index].loaded) {
                        $('img:not(:first)', this).each(function () {
                            $(this).prop('src', $(this).data('src'));
                        });
                        FancyGallery.instances[index].loaded = true;
                    }
                    FancyGallery.start(index);
                    $('.navigation', this).show();
                },
                function () {
                    FancyGallery.stop(index);
                    $('.navigation', this).hide();
                }
            );

            $('.navigation', this).hover(
                function () {
                    FancyGallery.stop(index);
                },
                function () {
                    FancyGallery.start(index);
                });

            $('.navigation span', this).click(function () {
                FancyGallery.activateNavItem(index, this);
            });

            FancyGallery.index++;
        });

        /*$(window).load(function () {
         $('.gallery-slide .item').each(function () {
         var a = $('a:first', this);
         $(this).css({height: $(a).outerHeight(), width: $(a).outerWidth()});
         });
         $('.gallery-slide .item a').css({position: 'absolute'});
         });*/
    },
    start: function (index) {
        FancyGallery.instances[index].timer = setInterval(function () {
            var item = FancyGallery.instances[index].item;
            var el = $('a.active', item);
            FancyGallery.hide(index, el);
            el = $(el).next();
            if (el.length == 0) {
                el = $('a', item).first();
            }

            FancyGallery.activateNavItem(index, $('.navigation span#gallery-nav-' + $(el).data('id')));
        }, FancyGallery.speed);
    },
    stop: function (index) {
        clearInterval(FancyGallery.instances[index].timer);
    },
    show: function (index, el) {
        $(el).fadeIn(800).addClass('active');
    },
    hide: function (index, el) {
        $(el).fadeOut(600).removeClass('active');
    },
    activateNavItem: function (index, el) {
        $(el).parent().children('span.active').removeClass('active');
        $(el).addClass('active');
        var item = $('a.active', FancyGallery.instances[index].item);
        FancyGallery.hide(index, item);
        FancyGallery.show(index, $('a#gallery-item-' + $(el).data('id')));
    }
};