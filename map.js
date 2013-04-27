function popUp(URL) {
    day = new Date();
    id = day.getTime();
    eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=800,height=600,left = 112,top = 84');");
}


var sidebar_html = "";

// arrays to hold copies of the markers and html used by the sidebar
// because the function closure trick doesnt work there
var gmarkers = [];
var htmls = [];
var i = 0;
// arrays to hold variants of the info window html with get direction forms open
var to_htmls = [];
var from_htmls = [];


// A function to create the marker and set up the event window
function createMarker(lat, lon, name, html, tooltip, icon, address) {
    var locate = new google.maps.LatLng(lat, lon);
    gmarkers[i] = new google.maps.Marker({
        position: locate,
        icon: icon
    });
    gmarkers[i].setMap(map);
    bounds.extend(locate);
    map.fitBounds(bounds);

    // The info window version with the "to here" form open
    var to_html = html + '<div class="phpGoogleStoreLocator_map_balloon_body">Directions: <b>To here</b> - <a href="javascript:fromhere(' + i + ')">From here</a>' +
        '<br>Start address:<form action="http://maps.google.com/maps" method="get" target="_blank">' +
        '<input type="text" SIZE=40 MAXLENGTH=40 name="saddr" id="saddr" value="" /><br>' +
        '<INPUT value="Get Directions" TYPE="SUBMIT">' +
        '<input type="hidden" name="daddr" value="' + address + '"/>';

    // The info window version with the "to here" form open
    var from_html = html + '<br>Directions: <a href="javascript:tohere(' + i + ')">To here</a> - <b>From here</b>' +
        '<br>End address:<form action="http://maps.google.com/maps" method="get"" target="_blank">' +
        '<input type="text" SIZE=40 MAXLENGTH=40 name="daddr" id="daddr" value="" /><br>' +
        '<INPUT value="Get Directions" TYPE="SUBMIT">' +
        '<input type="hidden" name="saddr" value="' + address + '"/></div>';

    // The inactive version of the direction info
    html = html + '<div class="phpGoogleStoreLocator_map_balloon_body">Directions: <a href="javascript:tohere(' + i + ')">To here</a> - <a href="javascript:fromhere(' + i + ')">From here</a></div>';


    htmls[i] = new google.maps.InfoWindow({content: html});
    to_htmls[i] = new google.maps.InfoWindow({content: to_html});
    from_htmls[i] = new google.maps.InfoWindow({content: from_html});

    var currenthtml = htmls[i];
    var currentgmarker = gmarkers[i];

    google.maps.event.addListener(gmarkers[i], 'click', function() {
        currenthtml.open(map, currentgmarker);
    });


    // add a line to the sidebar html
    sidebar_html += '<a href="javascript:myclick(' + i + ')">' + name + '</a><br>';
    i++;
    return gmarkers[i];
}


// This function picks up the click and opens the corresponding info window
function myclick(i) {
    htmls[i].open(map, gmarkers[i]);
}

// functions that open the directions forms
function tohere(i) {
    to_htmls[i].open(map, gmarkers[i]);
}
function fromhere(i) {
    from_htmls[i].open(map, gmarkers[i]);
}

bounds = new google.maps.LatLngBounds();
var address = '$searchAddress';
var latlng = new google.maps.LatLng($center_lat_float, $center_lon_float);
var bounds;
var myOptions = {
    zoom: $zoom,
    center: latlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
};
var map = new google.maps.Map(document.getElementById("map"), myOptions);

var markerx = new google.maps.Marker({
    position: latlng,
    icon: '/uploads/tx_spxgooglestorelocator/icons/circle_green.gif'
});
markerx.setMap(map);
bounds.extend(latlng);


$map_results


function show_marker(id) {
    gmarkers[id].openInfoWindowHtml(htmls[id])

}
