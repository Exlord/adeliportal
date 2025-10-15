$(document).ready(function () {
    initMainMenu();
    System.Pages.setup();
    System.Messages.remove();
    System.UI.watermark(document);
    System.Forms.setup();
});
System.Pages = {
    loadedUrls: {},
    Resources: {
        DataGridView: 0,
        CKEditor: 0,
        GoogleMap: 0
    },
    current: null,
    status: 0,
    offlineAfter: 5 * 60 * 1000,
    reloadAfter: 15 * 60 * 1000,
    deleteMoreThan: 20,//TODO from admin config
    Messages: {
        online: 'This page has just been loaded and is online',
        offline: 'This page has been loaded from the cache and has not been reloaded in more than %s minutes',
        cached: 'This page has been loaded from cache',
        reload: 'Click here to reload this page',
        lastPage: 'Click here To reload the last page you visited.'
    },
    requestType: 'GET',
    requestData: {},
    ajaxLoad: function (url, reload) {

        $("body").animate({scrollTop: 0}, 500);

        $.event.trigger({
            type: "beforePageLoad"
        });

        //set the location for the new page so we can handle browsers back button
//        var location = window.location.pathname;
//        location = location.split('#')[0];
//        window.location = location + '#' + url;

        //do we have the page in the cache and do we need to reload the page
        var oldPage = url.toLowerCase();
        var loaded = false;
        if (typeof this.loadedUrls[oldPage] != 'undefined')
            loaded = true;
        //$('html,body').animate({ scrollTop: 0 }, 1000);
        var container = $('#content_body');
        container.append(overlay);

        var __requestData = System.Pages.requestData;

        __requestData['_dataGridIsReload'] = System.Pages.Resources.DataGridView;
        __requestData['_CKEditorIsLoaded'] = System.Pages.Resources.CKEditor;
        __requestData['_GoogleMapIsLoaded'] = System.Pages.Resources.GoogleMap;

        $.ajax({
            url: url,
            type: System.Pages.requestType,
            data: __requestData,
            complete: function () {
                overlay.remove();
            },
            success: function (data) {
                System.Pages.requestData = {};
                System.Pages.requestType = "GET";

                var page = {
                    url: url,
                    time: new Date(),
                    data: {
                        breadcrumbs: null,
                        css: null,
                        js: null,
                        inlineJs: null,
                        fm: null,
                        content: null
                    },
                    isFresh: !loaded,
                    status: 'online'
                };
                if (typeof(data) == 'object') {
                    if (data.hasOwnProperty('callback')) {
                        eval(data.callback);
                    }
                    return
                }
                page.data.content = data;
                System.Pages.__update(url, page);
            },
            error: System.AjaxError
        });
    },
    __update: function (url, page) {
        this.loadedUrls[url] = true;
        this.__handleAjaxLoadedPage(page);
        __setActiveMenuItem(url);
    },
    __handleAjaxLoadedPage: function (page) {

        page.data.content = $('<div></div>').html(page.data.content);
        if ($(page.data.content).children().length == 1)
            page.data.content = $($(page.data.content).html());

        $('#content_body').html(page.data.content);

        page.data.css = $('#headLinkContainer').val();
        $('#headLinkContainer').remove();

        page.data.js = $('#headScriptContainer').val();
        $('#headScriptContainer').remove();

        page.data.inlineJs = $('#inlineScriptContainer').val();
        $('#inlineScriptContainer').remove();

        page.data.fm = $('#flashMessengerContainer').val();
        $('#flashMessengerContainer').remove();

        page.data.breadcrumbs = $('#breadcrumbsContainer').val();
        $('#breadcrumbsContainer').remove();

        var data = page.data;
        if (data.css && data.css.length && page.isFresh) {
            $('body').append(data.css);
        }

        if (data.js && data.js.length && page.isFresh) {
            $('body').append(data.js);
        }

        $.event.trigger({
            type: "afterPageLoad",
            page: page
        });

        if (data.inlineJs && data.inlineJs.length) {
            var inlineScriptsContainer = '<div id="inline-script"></div>';
            if (!$('#inline-script').length)
                $('body').append(inlineScriptsContainer);
            $('#inline-script').html(data.inlineJs).remove();
        }

        if (data.breadcrumbs) {
            var breadcrumbs = $('#breadcrumbs');
            if (breadcrumbs.length == 0)
                $('<div id="breadcrumbs"></div>').insertAfter($('#system_messages'));
            $('#breadcrumbs').html(data.breadcrumbs);
        }

        if (data.fm) {
            if (data.fm.length) {
                $('#system_messages').append(data.fm);
                System.Messages.remove();
            }
        }
    },
    setup: function () {
        System.Pages.loadedUrls[window.location.pathname] = true;
        $('body').on('click', 'a.ajax_page_load', function (e) {
            e.preventDefault();
            var href = $(this).attr('href');
            if (href[0] != '#') {
                System.Pages.ajaxLoad(href);
            }
        });
    }
};
var overlay = $('<div id="ajax_loading_overlay"></div>');
function initMainMenu() {
    $('#admin_nav .menu a').addClass('ajax_page_load').click(function () {
        $(this).parents('ul').not('.menu').hide();
    });
    $('#admin_nav li ul').each(function () {
        $(this).parent().addClass('has-child');
    });
}
function __setActiveMenuItem(url) {
    var menu = $('#admin_nav a[href="' + url + '"]');
    if (menu.length) {
        $('#admin_nav li.active').removeClass('active');
        menu.parents('li').addClass('active');
    }
}
System.Forms = {
    setup: function () {
        $(document).on('click', "form input[type=submit],form button[type=submit]", function () {
            $("input[type=submit],button[type=submit]", $(this).parents("form")).removeAttr("clicked");
            $(this).attr("clicked", "true");
        });

        $(document).on('submit', 'form', function (e) {
            $(this).trigger('ajaxFormSubmit');

            if ($(this).hasClass('force-normal'))
                return true;

            e.preventDefault();

            if ($(this).data('cancel-submit') == true) {
                e.preventDefault();
                return true;
            }

            var button = $("input[type=submit][clicked=true],button[type=submit][clicked=true]");
            $(this).prepend(
                $('<input type="hidden">')
                    .attr('name', $(button).attr('name'))
                    .attr('value', $(button).val())
            );

            //------------------------ cancel button is clicked -------------------
            var isCancel = $(button).attr('name').indexOf('cancel') > -1;
            if (isCancel) {
                var cancelUrl = $(this).data('cancel');
                if (cancelUrl && typeof cancelUrl != 'undefined') {
                    System.Pages.ajaxLoad(cancelUrl);
                    return false;
                } else {
                    $(this).addClass('force-normal').submit();
                    return false;
                }
            }
            //--------------------------------------------------------------------

            // form doesn't want ajax submit , so force a normal submit
            if (!$(this).hasClass('ajax_submit')) {
                $(this).addClass('force-normal').submit();
                return false;
            }

            var container = $('#content_body');
            container.append(overlay);

            $('html,body').animate({scrollTop: 0}, 1000);

            $(this).prepend('<input type="hidden" name="systemLayout" value="form_submit">');

            var url = $(this).attr('action');
            var method = $(this).attr('method');

            // dose form has file input ?
            var hasImage = $('input[type=file]', $(this)).length > 0;

            // form has file input , we can't do a normal ajax submit
            if (hasImage) {
                var iFrame = $('<iframe name="__hidden_form_submit" src="about:blank" style="display:none;"></iframe>');
                $('body').append(iFrame);
                $(iFrame).load(function () {
                    System.Forms.__formSubmitted($(this).contents().find('body').html(), container);
                    $(this).remove();
                });
                $(this).attr('target', '__hidden_form_submit').addClass('force-normal');
                $(this).submit();
                return false;
            }
            // do a normal ajax submit
            else {
                var data = $(this).serialize();
                $.ajax({
                    url: url,
                    type: method,
                    data: data,
                    complete: function () {
                        overlay.remove();
                    },
                    success: function (result) {
                        System.Forms.__formSubmitted(result, container);
                    },
                    error: System.AjaxError
                });
            }
        });
    },
    __formSubmitted: function (result, container) {
        result = $(result);

        container.html(result);

        $.event.trigger({
            type: "afterPageLoad",
            page: {'data': {'content': result}}
        });

        var flashMessenger = $('#flashMessengerContainer').val();
        $('#flashMessengerContainer').remove();

        var inlineScript = $('#inlineScriptContainer').val();
        $('#inlineScriptContainer').remove();

        if (flashMessenger.length) {
            $('#system_messages').append(flashMessenger);
            System.Messages.remove();
        }

        if (inlineScript.length) {
            var inlineScriptsContainer = '<div id="inline-script"></div>';
            if (!$('#inline-script').length)
                $('body').append(inlineScriptsContainer);
            $('#inline-script').html(inlineScript).remove();
        }
    }
};