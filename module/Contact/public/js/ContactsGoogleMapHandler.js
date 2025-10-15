/**
 * Created with PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 8/24/14
 * Time: 11:22 AM
 */
var Contacts = {
    map: null,
    Lat: 0,
    Long: 0,
    dialog: null,
    map_selected_loc: null,
    init: function () {
        if (Contacts.dialog == null)
            Contacts.map_selected_loc = $('#map_selected_loc');
        else
            $('#map-dialog').remove();

        if (System.Pages.Resources.GoogleMap == 0) {
            var script = document.createElement("script");
            script.type = "text/javascript";
            script.src = "http://maps.google.com/maps/api/js?sensor=false&callback=Contacts.googleMapLoaded";
            document.body.appendChild(script);
            System.Pages.Resources.GoogleMap = 1;
        } else {
            Contacts.googleMapLoaded();
        }

        $('#find-google').click(function (e) {
            e.preventDefault();
            if (Contacts.dialog == null) {
                Contacts.dialog = $('#map-dialog').dialog({
                    autoOpen: true,
                    modal: true,
                    height: 550,
                    width: 800,
                    top: 60
                });
            } else
                Contacts.dialog.dialog('open');

            $('#select_return').click(function () {
                var loc = $('#map_selected_loc').text();
                $('#googleLatLong').val(loc);
                $('#google_text').html('Lat: ' + Contacts.Lat + '<br/>Longitude: ' + Contacts.Long);
                $('#google').val(Contacts.Lat + ',' + Contacts.Long);
                $('input[name=googleLatLong]').val(loc);
                Contacts.dialog.dialog('close');
            });
            $('#select_cancel').click(function () {
                $('#googleLatLong').val('0');
                $('#google_text').html('');
                Contacts.dialog.dialog('close');
            });

            if (Contacts.map == null) {
                var location = new google.maps.LatLng(38.06921249960978, 46.300392157863826);//tabriz
                Contacts.map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 12,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    center: location
                });
            }

            google.maps.event.addListener(Contacts.map, 'click', function (event) {
                Contacts.Lat = event.latLng.lat();
                Contacts.Long = event.latLng.lng();
                $(Contacts.map_selected_loc).text(event.latLng.lat() + ',' + event.latLng.lng());
            });

        });
    },
    googleMapLoaded: function () {
        $('#find-google').removeClass('disabled').children('i').remove();
    }
};