/**
 * Created with PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 7/13/14
 * Time: 10:31 AM
 */
$(document).ready(function () {
    var SearchSliders = {
        items: {},
        updateValue: function (el, low, high) {
            var valueLow = SearchSliders.getValue(el, low);
            var valueHigh = SearchSliders.getValue(el, high);

            $(el).val(valueLow + ',' + valueHigh);
            $(el).siblings('.price_from').html(insertcomma(valueLow.toString()));
            $(el).siblings('.price_to').html(insertcomma(valueHigh.toString()));
        },
        getValue: function (el, val) {
            var id = $(el).attr('id');
            var steps = SearchSliders.items[id].steps;

            if (steps)
                return SearchSliders.items[id].steps[val];

            return val;
        },
        __log: function (position, min, max) {
            if (min == 0 && position == 0)
                return 0;
            // position will be between 0 and 100
            var minp = 0;
            var maxp = 100;

            // The result should be between 100 an 10000000
            var minv = min == 0 ? 0 : Math.log(min);
            var maxv = Math.log(max);
            // calculate adjustment factor
            var scale = (maxv - minv) / (maxp - minp);

            return parseInt(Math.exp(minv + scale * (position - minp)));
        }
    };
    $('input.slider').each(function () {
        var min = parseInt($(this).data('min'));
        var max = parseInt($(this).data('max'));
        var step = parseInt($(this).data('step'));
        var el = $(this);
        var id = $(el).attr('id');
        var __slider = {steps: false};



        var steps = parseInt((max - min) / step);
        if (steps > 100) {
            __slider.steps = {};
            for (var i = 0; i <= 100; i++) {
                __slider.steps[i] = SearchSliders.__log(i, min, max);
            }
            min = 0;
            max = 100;
            step = 1;
        }

        SearchSliders.items[$(el).attr('id')] = __slider;

        var slider = $("<div></div>")
            .attr('id', $(el).attr('id') + '_slider')
            .slider({
                range: true,
                min: min,
                max: max,
                step: step,
                values: [min, max],
                slide: function (event, ui) {
                    SearchSliders.updateValue(el, ui.values[0], ui.values[1]);
                }
            }).insertAfter(el);

        var values = $(el).hide().val().split(',');

        if (values.length == 2) {
            $(slider).slider("values", 0, values[0]);
            $(slider).slider("values", 1, values[1]);
        }
        $("<div class='price_from pull-right flip'></div>").insertAfter(slider).html(insertcomma(SearchSliders.getValue(el, $(slider).slider("values", 0)).toString()));
        $("<div class='price_to pull-left flip'></div>").insertAfter(slider).html(insertcomma(SearchSliders.getValue(el, $(slider).slider("values", 1)).toString()));
        $(el).parent().addClass('clearfix');
        $(el).hide().val(0 + ',' + SearchSliders.getValue(el, $(slider).slider("values", 1)));
    });
});