/**
 * Created with JetBrains PhpStorm.
 * User: Exlord
 * Date: 6/5/13
 * Time: 10:45 AM
 * To change this template use File | Settings | File Templates.
 */
if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function (needle) {
        for (var i = 0; i < this.length; i++) {
            if (this[i] === needle) {
                return i;
            }
        }
        return -1;
    };
}
var System = {
    Editor: {
        /**
         * insert a text at mouse position in CKEditor
         * @param myValue
         * @param editorId
         */
        insertText: function (myValue, editorId) {
            myValue = myValue.trim();
            CKEDITOR.instances[editorId].insertText(myValue);
        },
        insertHtml: function (myValue, editorId) {
            myValue = myValue.trim();
            CKEDITOR.instances[editorId].insertHtml(myValue);
        }
    },
    /**
     * Show a message with jquery ui dialog.
     * @param msg
     */
    AjaxMessage: function (msg) { //ToDo get title param
        var ajaxMsg = $("<div id='ajax-message'></div>");
        $('body').append(ajaxMsg);
        $(ajaxMsg).html(msg);
        $(ajaxMsg).dialog({
            width: 500,
            height: 'auto',
            modal: true,
            title: '',
            close: function (event, ui) {
                $(this).dialog("destroy");
                $('body div#ajax-message').remove();
            },
            open: function () {
                $('.ui-dialog-titlebar :button').blur();
            }
        });
    },
    /**
     * Method for handling ajax errors with a popup dialog
     * @param jqXHR
     * @param textStatus
     * @param errorThrown
     */
    AjaxError: function (jqXHR, textStatus, errorThrown) {
        var msg = '';
        if (jqXHR.status == 403)
            msg = jqXHR.responseText;
        else {
            msg = "readyState: " + jqXHR.readyState +
            "<br/>status: " + jqXHR.status +
            "<br/>responseText: " + jqXHR.responseText +
            "<br/>textStatus: " + textStatus +
            "<br/>errorThrown: " + errorThrown;
        }
        System.AjaxMessage(msg);
    },
    Messages: {
        timer: false,
        timerDelay: 10000,
        /**
         * Starts a queue to delete system messages
         */
        remove: function () {
            var messages = $('#system_messages').children();
            if (!this.timer) {
                if (messages.length) {
                    this.timer = setInterval(function () {
                        System.Messages.__remove();
                    }, this.timerDelay);
                }
            }
        },
        /**
         * Private Message Deleting Queue. Should NOT be used directly
         * @private
         */
        __remove: function () {
            var messages = $('#system_messages');
            if (messages.children().length) {
                messages.children(":first").slideUp(500, function () {
                    $(this).remove();
                });
            } else {
                clearInterval(this.timer);
                this.timer = false;
            }
        }
    },
    Collections: [],
    initCollection: function (container) {
        var id = $(container).data('collection-id');
        if (typeof id == 'undefined') {
            id = System.Collections.length;
            System.Collections[id] = {lastIndex: 0, count: 0};
            $(container).data('collection-id', id);
        }

        if (System.Collections[id].lastIndex == 0) {
            var items = $("fieldset.collection-item", container);
            System.Collections[id].count = items.length;
            var last = items.last();
            var lastIndex = $('input', last).first().attr('id');
            if (typeof lastIndex != 'undefined') {
                lastIndex = lastIndex.split(/(__\d+__)/);

                if (lastIndex && lastIndex.length && lastIndex.length > 2) {
                    System.Collections[id].lastIndex = parseInt(lastIndex[1].replace('__', ''));
                }
            }
        }
        return id;
    }
};
$(document).ready(function () {

    $('body')
        .on('click', '.add_collection_item', function () {
            var container = $(this).closest('fieldset').children('.collection-container');
            var id = System.initCollection(container);
            var max = 1000;
            if (System.Collections[id].count <= max) {
                var template = $(container).children('span').data('template');
                template = $(template.replace(/__index__/g, ++System.Collections[id].lastIndex));
                if (typeof System.UI != 'undefined')
                    System.UI.spinner(template);
                $(container).append(template);
                System.Collections[id].count++;

                if (jQuery().colpick) {
                    $('input[data-type=color]').colpick({
                        onChange: function (hsb, hex, rgb, el, bySetColor) {
                            if (!bySetColor) $(el).val(hex);
                        },
                        onSubmit: function (hsb, hex, rgb, el) {
                            $(el).colpickHide();
                        }
                    });
                }
            }
        })
        .on('click', '.drop_collection_item', function () {
            var container = $(this).closest('.collection-item').closest('.collection-container');
            var id = System.initCollection(container);
            System.Collections[id].count--;
            $(this).closest('.collection-item').remove();
        })
        .on('click', 'fieldset.collapsible legend', function (e) {
            e.stopPropagation();
            if ($(this).parent().hasClass('collapsed')) {
                $(this).parent().removeClass('collapsed').addClass('open').children().not('.hidden').slideDown(200);
            } else
                $(this).parent().addClass('collapsed').removeClass('open').children(':not(legend)').slideUp(200);
        });
});
(function ($, undefined) {
    $.fn.serializeObject = function () {
        var obj = {};

        $.each(this.serializeArray(), function (i, o) {
            var n = o.name,
                v = o.value;

            obj[n] = obj[n] === undefined ? v
                : $.isArray(obj[n]) ? obj[n].concat(v)
                : [obj[n], v];
        });

        return obj;
    };

})(jQuery);