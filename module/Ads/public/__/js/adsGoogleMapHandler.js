/**
 * Created with PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 8/24/14
 * Time: 11:22 AM
 */
var Ads = {
    map: null,
    Lat: 0,
    Long: 0,
    dialog: null,
    map_selected_loc: null,
    init: function () {
        if (Ads.dialog == null)
            Ads.map_selected_loc = $('#map_selected_loc');
        else
            $('#map-dialog').remove();

        var flagMapLoaded = false;
        if (typeof System.Pages != 'undefined') {
            if (System.Pages.Resources.GoogleMap == 1)
                flagMapLoaded = true;
        }
        if (!flagMapLoaded) {
            var script = document.createElement("script");
            script.type = "text/javascript";
            script.src = "http://maps.google.com/maps/api/js?sensor=false&callback=Ads.googleMapLoaded";
            document.body.appendChild(script);
            if (typeof System.Pages != 'undefined')
                System.Pages.Resources.GoogleMap = 1;
            flagMapLoaded = true;
        } else {
            Ads.googleMapLoaded();
        }

        $('#find-google').click(function (e) {
            e.preventDefault();
            if (Ads.dialog == null) {
                Ads.dialog = $('#map-dialog').dialog({
                    autoOpen: true,
                    modal: false,
                    height: 550,
                    width: 800,
                    top: 60
                });
            } else
                Ads.dialog.dialog('open');

            $('#select_return').click(function () {
                var loc = $('#map_selected_loc').text();
                //$('#googleLatLong').val(loc);
                //$('#google_text').html('Lat: ' + Ads.Lat + '<br/>Longitude: ' + Ads.Long);
                $('.map_icon').css("display", "block").attr({title: loc});
                $('#google').val(loc);
                $('input[name=googleLatLong]').val(loc);
                Ads.dialog.dialog('close');
            });
            $('#select_cancel').click(function () {
                $('#googleLatLong').val('0');
                $('#google_text').html('');
                $('.map_icon').hide();
                Ads.dialog.dialog('close');
            });

            if (Ads.map == null) {
                var location = new google.maps.LatLng(38.06921249960978, 46.300392157863826);//tabriz
                Ads.map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 12,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    center: location
                });
            }

            google.maps.event.addListener(Ads.map, 'click', function (event) {
                Ads.Lat = event.latLng.lat();
                Ads.Long = event.latLng.lng();
                $(Ads.map_selected_loc).text(event.latLng.lat() + ',' + event.latLng.lng());
                placeMarker(event.latLng);
            });
            function placeMarker(location) {
                var marker = new google.maps.Marker({
                    position: location,
                    setMap: map
                });
            }

        });
    },
    googleMapLoaded: function () {
        $('#find-google').removeClass('disabled').children('i').remove();
    }
};