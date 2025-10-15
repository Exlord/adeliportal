/**
 * Created with PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 7/10/14
 * Time: 10:32 AM
 */
var RealEstate = {
    loaded: false,
    params: {'only-items': true},
    query: {},
    url: '',
    container: '#real-estate-list',
    loadList: function () {
        var loader = overlay.clone();
        if (!RealEstate.loaded)
            loader.addClass('block');
        else
            loader.removeClass('block');
        $('#real-estate-list').append(loader);
        $.ajax({
            type: 'POST',
            data: this.params,
            url: this.url + '?' + $.param(this.query),
            complete: function () {
                loader.remove();
                RealEstate.loaded = true;
            },
            success: function (data) {
                data = $("<div></div>").append(data);
                System.UI.tooltip(data);
                $(RealEstate.container).html(data);
            },
            error: System.AjaxError
        });
    }
};