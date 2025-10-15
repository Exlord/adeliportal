/**
 * Created with PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 7/10/14
 * Time: 10:32 AM
 */
var DigitalLibrary = {
    loaded: false,
    params: {'only-items': true},
    query: {},
    url: '',
    container: '#books-list',
    loadList: function () {
        var loader = overlay.clone();
        if (!DigitalLibrary.loaded)
            loader.addClass('block');
        else
            loader.removeClass('block');
        $(DigitalLibrary.container).append(loader);
        $.ajax({
            type: 'POST',
            data: this.params,
            url: this.url + '?' + $.param(this.query),
            complete: function () {
                loader.remove();
                DigitalLibrary.loaded = true;
            },
            success: function (data) {
                data = $("<div></div>").append(data);
                $(DigitalLibrary.container).html(data);
            },
            error: System.AjaxError
        });
    }
};