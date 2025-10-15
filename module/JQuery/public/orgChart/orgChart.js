/**
 * Created with PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 5/4/14
 * Time: 2:31 PM
 */
function OrgChart(data, options) {

    var defaults = {
        renderType: 'html',// canvas | html,
        renderTo: 'body',
        verticalGap: 20
    };
    this.settings = $.extend({}, defaults, options);

    //the chart data
    this.data = data;

    var renderTo = $(this.settings.renderTo);
    renderTo.addClass('orgChart').addClass(this.settings.renderType + '_orgChart');
    var viewport = $('<div class="viewport"></div>').appendTo(renderTo);
    var container = $('<div class="chart-container"></div>').appendTo(viewport);

    var buttons_container = $('<div class="buttons btn-group"></div>').prependTo(renderTo);

    var scale = {
        x: 1,
        y: 1
    };

    this.draw = function () {
        _draw.call(this, this.data, 1, container);
        $(container).children('.wrapper').children('.h-line').remove();
        $(container).children('.wrapper').children('.v-line.to-parent').remove();
        _drawLines.call(this);

        $(container).css({width: 10000});
        var topNode = $(container).children('.wrapper');
        $(container).css({width: $(topNode).width() + 50, height: $(topNode).height() + 50, margin: 'auto'});
        _renderButtons();
    };

    var _draw = function (nodes, lvl, container) {
        var that = this;
        $.each(nodes, function (index, item) {
            var wrapper = $('<div class="wrapper"></div>');

            //node content
            var node = $("<span class='node'></span>")
                .addClass("lvl" + lvl)
                .attr('id', 'orgChart_node_' + index + '_' + lvl)
                .html(item.content);
            wrapper.html(node);

            // horizontal line
            var line = $('<span class="h-line"></span>');
            if (index == 0)
                line.css({width: '50%', right: 0});
            else if (index == nodes.length - 1)
                line.css({width: '50%', left: 0});
            else
                line.css({ left: 0});
            $(wrapper).prepend(line);

            //child nodes
            if (item.hasOwnProperty('nodes')) {

                //vertical line to child
                line = $('<span class="v-line to-child"></span>').css({
                    height: that.settings.verticalGap / 2
                });
                $(wrapper).prepend(line);

                _draw.call(that, item.nodes, ++lvl, wrapper);
                lvl--;
            }

            //vertical line to parent
            line = $('<span class="v-line to-parent"></span>').css({
                height: that.settings.verticalGap / 2
            });
            $(wrapper).prepend(line);


            container.append(wrapper);
        });
    };

    var _drawLines = function () {
        var that = this;
        $(container).find('.v-line').each(function () {
            if ($(this).hasClass('to-child')) {
                var height = $(this).parent().children('.node').outerHeight();
                $(this).css({top: height + (that.settings.verticalGap / 2)});
            }
        });

        $(container).find('.h-line').each(function () {
            if ($(this).parent().parent().children('.wrapper').length < 2)
                $(this).remove();
        });
    };

    var _renderButtons = function () {
        $('<a><span class="glyphicon glyphicon-zoom-in text-success"></span> Zoom In</a>')
            .attr({
                'class': 'toolbar-button btn btn-default',
                'id': "zoom-in",
                'href': "#zoom-in",
                title: "Zoom In",
                rel: "tooltip"
            })
            .click(function (e) {
                e.preventDefault();
                scale.x += 0.1;
                scale.y += 0.1;
                $(container).transition({
                    scale: [scale.x, scale.y]
                });
            })
            .appendTo(buttons_container);

        $('<a><span class="glyphicon glyphicon-search text-primary"></span> Zoom Reset</a>')
            .attr({
                'class': 'toolbar-button btn btn-default',
                'id': "zoom-reset",
                'href': "#zoom-reset",
                title: "Zoom Reset",
                rel: "tooltip"
            }).click(function (e) {
                e.preventDefault();
                scale.x = 1;
                scale.y = 1;
                $(container).transition({
                    scale: [scale.x, scale.y]
                }).animate({left: 0, top: 0});
            })
            .appendTo(buttons_container);

        $('<a><span class="glyphicon glyphicon-zoom-out text-danger"></span> Zoom Out</a>')
            .attr({
                'class': 'toolbar-button btn btn-default',
                'id': "zoom-out",
                'href': "#zoom-out",
                title: "Zoom Out",
                rel: "tooltip"
            }).click(function (e) {
                e.preventDefault();
                scale.x -= 0.1;
                scale.y -= 0.1;
                $(container).transition({
                    scale: [scale.x, scale.y]
                });
            })
            .appendTo(buttons_container);

        $(container).draggable();
    };
}