<SCRIPT LANGUAGE="JavaScript">

function popUp(URL) 
{
day = new Date();
id = day.getTime();
eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=800,height=600,left = 112,top = 84');");
}
</script>

<script src="http://maps.google.com/maps?file=api&v=2.61&key=$google_key" type="text/javascript"></script>
<script type="text/javascript">
//<![CDATA[

  var sidebar_html = "";
    
      // arrays to hold copies of the markers and html used by the sidebar
      // because the function closure trick doesnt work there
      var gmarkers = [];
      var htmls = [];
      var i = 0;
      // arrays to hold variants of the info window html with get direction forms open
      var to_htmls = [];
      var from_htmls = [];

/*
 GxMarker version 1.2

 SYNOPSIS
    This version is compatible with Google Maps API Version 2

    A more full-featured marker that supports tooltips and hover events.  The
    first iteration just supports triggering of mouse over events, and tooltips.
   
    To setup a tooltip, pass in a third parameter (after the icon) to the
    GxMarker class:
        var marker = new GxMarker( new GPoint(lat,lng), icon, "My Tooltip" );
        map.addOverlay(marker);

    Or:
        var marker = new GxMarker( new GPoint(lat,lng) );
        marker.setTooltip("My Tooltip");
        map.addOverlay(marker);

    As of 1.1, changes to setTooltip() should work after the initial invocation

    Please refer to http://code.toeat.com/package/gxmarker for additional
    documentation.
    
    TESTED PLATFORMS:
        Linux: Firefox
        Windows: Firefox, IE6
        Mac OS X (Panther): Safari

    There is no warranty of functionality of this code, if you wish to use it
    and it does not work for you, I recommend you submit a patch.  This software
    is licensed under the GNU Lesser General Public License (LGPL):
    the full text at: http://opensource.org/licenses/lgpl-license.php
	
	Update: 04/07/06 - modified to load with API v2.44+ of the Google Maps API
	Modified by Robert Aspinall - raspinall (AT) gmail (dot) com
*/

function GxMarkerNamespace() {

var n4=(document.layers);
var n6=(document.getElementById&&!document.all);
var ie=(document.all);
var o6=(navigator.appName.indexOf("Opera") != -1);
var safari=(navigator.userAgent.indexOf("Safari") != -1);
var currentSpan = new GBounds();

function setCursor( container, cursor ) {
    try {
        container.style.cursor = cursor;
    }
    catch ( c ) {
        if ( cursor == "pointer" )
            setCursor("hand");
    }
};

function GxMarker( a, b, tooltip ) {
    this.inheritFrom = GMarker;
    this.inheritFrom(a,b);
    if ( !currentSpan.minX || a.x < currentSpan.minX ) currentSpan.minX = a.x;
    if ( !currentSpan.maxX || a.x > currentSpan.maxX ) currentSpan.maxX = a.x;
    if ( !currentSpan.minY || a.y < currentSpan.minY ) currentSpan.minY = a.y;
    if ( !currentSpan.maxY || a.y > currentSpan.maxY ) currentSpan.maxY = a.y;
    if ( typeof tooltip != "undefined" ) {
        this.setTooltip( tooltip );
    }
}

GxMarker.prototype = new GMarker(new GLatLng(1, 1));

GxMarker.prototype.setTooltip = function( string ) {
    this.removeTooltip();
    this.tooltip = new Object();
    this.tooltip.opacity  = 70;
    this.tooltip.contents = string;
};

GxMarker.prototype.initialize = function( a ) {
    try {
        GMarker.prototype.initialize.call(this, a);
        // Setup the mouse over/out events
		GEvent.bind(this, "mouseover", this, this.onMouseOver);
		GEvent.bind(this, "mouseout", this, this.onMouseOut);
    } catch(e) {
		alert(e);
    }
}

GxMarker.prototype.setCursor = function( cursor ) {
    var c = this.iconImage;
    // Use the image map for Firefox/Mozilla browsers
    if ( n6 && this.icon.imageMap && !safari) {
        c = this.imageMap;
    }
    // If we have a transparent icon, use that instead of the main image
    else if ( this.transparentIcon && typeof this.transparentIcon != "undefined" ) {
        c = this.transparentIcon;
    }
}

GxMarker.prototype.remove = function( a ) {
    GMarker.prototype.remove.call(this);
    this.removeTooltip();
}

GxMarker.prototype.removeTooltip = function() {
    if ( this.tooltipObject ) {
        this.map.div.removeChild(this.tooltipObject);
        this.tooltipObject = null;
    }
}

GxMarker.prototype.onInfoWindowOpen = function() {
    this.hideTooltip();
    GMarker.prototype.onInfoWindowOpen.call(this);
}

GxMarker.prototype.onMouseOver = function() {
    this.showTooltip();
    GEvent.trigger(this, "mouseover");
};

GxMarker.prototype.onMouseOut = function() {
    this.hideTooltip();
    GEvent.trigger(this, "mouseout");
};

GxMarker.prototype.showTooltip = function() {
    if ( this.tooltip ) {
        if ( !this.tooltipObject ) {
            var opacity = this.tooltip.opacity / 100;
            this.tooltipObject = document.createElement("div");
            this.tooltipObject.style.display    = "none";
            this.tooltipObject.style.position   = "absolute";
            this.tooltipObject.style.background = "#fff";
            this.tooltipObject.style.padding    = "0";
            this.tooltipObject.style.margin     = "0";
            this.tooltipObject.style.MozOpacity = opacity;
            this.tooltipObject.style.filter     = "alpha(opacity=" + this.tooltip.opacity + ")";
            this.tooltipObject.style.opacity    = opacity;
            this.tooltipObject.style.zIndex     = 50000;
            this.tooltipObject.innerHTML        = '<div class="markerTooltip">' + this.tooltip.contents + '</div>';
            map.getPane(G_MAP_MARKER_PANE).appendChild(this.tooltipObject);
		}

        var c = map.fromLatLngToDivPixel(new GLatLng(this.getPoint().y, this.getPoint().x));
		try {
        	this.tooltipObject.style.top  = c.y + "px";
        	this.tooltipObject.style.left = c.x + "px";
        	this.tooltipObject.style.display = "block";
		} catch(e) {
			//alert(e);
		}
    }
}

GxMarker.prototype.hideTooltip = function() {
    if ( this.tooltipObject ) {
        this.tooltipObject.style.display = "none";
    }
}

GMap.prototype.flushOverlays = function() {
    currentSpan = new GBounds();
    this.clearOverlays();
}

GMap.prototype.zoomToMarkers = function() {
    var span = new GSize( currentSpan.maxX - currentSpan.minX, currentSpan.maxY - currentSpan.minY );
    for ( var zoom = 0; zoom < this.spec.numZoomLevels; zoom++ ) {
        var ppd = this.spec.getPixelsPerDegree(zoom);
        var pixelSpan = new GSize(
            Math.round(span.width * ppd.x), Math.round(span.height * ppd.y));
        if ( pixelSpan.width  <= this.viewSize.width &&
             pixelSpan.height <= this.viewSize.height )
        { break; }
    }
    this.centerAndZoom( new GPoint( currentSpan.minX + (span.width/2), currentSpan.minY + (span.height/2) ), zoom);
}

function makeInterface(a) {
    var b = a || window;
    b.GxMarker = GxMarker;
}

makeInterface();
}

GxMarkerNamespace();

//End GXMarker Code

      // A function to create the marker and set up the event window
      function createMarker(point,name,html,tooltip,icon) {
        var marker = new GxMarker(point,icon); //GMarker(point);
   marker.setTooltip(tooltip);
        // The info window version with the "to here" form open
        to_htmls[i] = html + '<div class=phpGoogleStoreLocator_map_balloon_body>Directions: <b>To here</b> - <a href="javascript:fromhere(' + i + ')">From here</a>' +
           '<br>Start address:<form action="http://maps.google.com/maps" method="get" target="_blank">' +
           '<input type="text" SIZE=40 MAXLENGTH=40 name="saddr" id="saddr" value="" /><br>' +
           '<INPUT value="Get Directions" TYPE="SUBMIT">' +
           '<input type="hidden" name="daddr" value="' + address + '"/>';
           //point.y + ',' + point.x + '"/>';
        // The info window version with the "to here" form open
        from_htmls[i] = html + '<br>Directions: <a href="javascript:tohere(' + i + ')">To here</a> - <b>From here</b>' +
           '<br>End address:<form action="http://maps.google.com/maps" method="get"" target="_blank">' +
           '<input type="text" SIZE=40 MAXLENGTH=40 name="daddr" id="daddr" value="" /><br>' +
           '<INPUT value="Get Directions" TYPE="SUBMIT">' +
           '<input type="hidden" name="saddr" value="' + address + '"/></div>';
           //point.y + ',' + point.x + '"/>';
        // The inactive version of the direction info
        html = html + '<div class=phpGoogleStoreLocator_map_balloon_body>Directions: <a href="javascript:tohere('+i+')">To here</a> - <a href="javascript:fromhere('+i+')">From here</a></div>';

        GEvent.addListener(marker, "click", function() {
          marker.openInfoWindowHtml('<div  class=phpGoogleStoreLocator_map_balloon_body>'+html+'</div>');
        });
        // save the info we need to use later for the sidebar
        gmarkers[i] = marker;
        htmls[i] = html;
        // add a line to the sidebar html
        sidebar_html += '<a href="javascript:myclick(' + i + ')">' + name + '</a><br>';
        i++;
        return marker;
      }


      // This function picks up the click and opens the corresponding info window
      function myclick(i) {
        gmarkers[i].openInfoWindowHtml('<div  class=phpGoogleStoreLocator_map_balloon_body>'+htmls[i]+'</div>');
      }

      // functions that open the directions forms
      function tohere(i) {
        gmarkers[i].openInfoWindowHtml('<div  class=phpGoogleStoreLocator_map_balloon_body>'+ to_htmls[i]+'</div>');
      }
      function fromhere(i) {
        gmarkers[i].openInfoWindowHtml('<div  class=phpGoogleStoreLocator_map_balloon_body>'+ from_htmls[i] +'</div>');
      }


// create the map
var lon;
var lat;
var point = new GPoint($center_lon_float,$center_lat_float);
var marker;
      var map = new GMap(document.getElementById("map"));
      map.addControl(new GLargeMapControl());
      map.addControl(new GMapTypeControl());
      map.addControl(new GScaleControl());

//Create the custom Green icon.  Google only provides red, so I've modified it with a different color.
var icon = new GIcon();
//icon.shadow = "http://www.google.com/mapfiles/shadow50.png";

icon.iconSize = new GSize($mapicon_you_width,$mapicon_you_height);

icon.shadowSize = new GSize(37, 34);
icon.iconAnchor = new GPoint(9, 34);
icon.infoWindowAnchor = new GPoint(9, 2);
icon.infoShadowAnchor = new GPoint(18, 25);
icon.image = "$mapicon_you";

map.centerAndZoom(new GPoint($center_lon_float, $center_lat_float), $zoom);
marker = createMarker(point, name, '', 'You', icon);
map.addOverlay(marker);

     
$map_results


GEvent.addListener(map, 'click', function(overlay, point) {
        if (overlay) {
          //map.removeOverlay(overlay);
        } else if (point) {

      output.innerHTML += "";
      if (map.getZoomLevel() >= zoomToLevel) {
         map.centerAndZoom(point, zoomToLevel);
      }
           map.addOverlay(new GMarker(point));
     }


   }
);

function show_marker(id)
{
    gmarkers[id].openInfoWindowHtml(htmls[id])

}
</script>
