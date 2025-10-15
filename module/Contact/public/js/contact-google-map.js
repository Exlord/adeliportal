$(document).ready(function () {

    var center = googleMapCenter;
    var offsets = center.search(',');
    var lat1Center = parseFloat(center.slice(0, offsets));
    var lat2Center = parseFloat(center.slice(offsets + 1, center.length));
    var zoom = parseInt(googleMapZoom);

    var locations = [];
    var count = 0;
    $('.lat-lng-google').each(function () {
        var html = $(this).parent().html();
        var lat = $(this).data('id');
        var offset = lat.search(',');
        var lat1 = parseFloat(lat.slice(0, offset));
        var lat2 = parseFloat(lat.slice(offset + 1, lat.length));
        locations[count] = [html, lat1, lat2, count];
        count += 1;
    });

    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: zoom,
      //  center: new google.maps.LatLng(lat1Center, lat2Center),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow();

var flagSet = 1;
    var bound = new google.maps.LatLngBounds();
    for (i = 0; i < locations.length; i++) {
        bound.extend( new google.maps.LatLng(locations[i][1], locations[i][2]) );


        if(i>1 && flagSet)
        {
            map.setZoom(5);
            flagSet= 0;
        }
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[i][1], locations[i][2]),
            map: map
        });

        google.maps.event.addListener(marker, 'click', (function (marker, i) {
            return function () {
                map.setCenter(marker.getPosition());
                map.setZoom(12);
                infowindow.setContent(locations[i][0]);
                infowindow.open(map, marker);
            }
        })(marker, i));

        $('.reset-map').click(function(e){
            e.preventDefault();
            infowindow.close();
            map.setCenter(new google.maps.LatLng(lat1Center, lat2Center));
            map.setZoom(5);
        });
    }

    map.setCenter(bound.getCenter());


});
