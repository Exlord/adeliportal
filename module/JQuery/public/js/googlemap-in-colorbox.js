$(document).ready(function () {
    var flagloadScriptGeneral = true;
    jQuery(".view-real-estate-google-map").colorbox({
        html: '<div id="map_canvas_all" style="width:600px; height:450px;"></div>',
        scrolling: false,
        width: "600px",
        height: "470px",
        onComplete: function () {
            loadScriptGeneral(flagloadScriptGeneral);
            flagloadScriptGeneral = false;
        }
    });
});
function loadScriptGeneral(flagloadScriptGeneral) {

    var script = document.createElement("script");
    script.type = "text/javascript";
    script.src = "http://maps.googleapis.com/maps/api/js?key=AIzaSyCx2MPpr2ZlzC6GkqBQqI1n5YTmIUBl9OE&sensor=false&callback=initializeGeneral";
    if (flagloadScriptGeneral)
        document.body.appendChild(script);
    else
        initializeGeneral();
};

function initializeGeneral() {
    var lat = googlemap_lat;
    var offset = lat.search(',');
    var lat1 = lat.slice(0, offset);
    var lat2 = lat.slice(offset + 1, lat.length);
    var imageG = new google.maps.MarkerImage(marker_icon);
    var myLatlngG = new google.maps.LatLng(lat1, lat2);
    var myOptionsG = {
        zoom: 16,
        center: myLatlngG,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    var mapG = new google.maps.Map(document.getElementById("map_canvas_all"), myOptionsG);
    var markerG = new google.maps.Marker({
        position: myLatlngG,
        map: mapG,
        title: "Google Map",
        icon: imageG
    });
};
