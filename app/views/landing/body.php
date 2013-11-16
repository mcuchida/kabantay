 <style>
      #map-canvas {
        height: 600px;
        margin-top: 60px;
        padding: 0px;
        border: 1px solid black;

      }
    </style>

 <div id="map-canvas"></div>



 <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true"></script>

<script>
// $(document).ready(function(){
//  initialize();
// });
var map;

function initialize() {
  var mapOptions = {
    zoom: 6,
    center: new google.maps.LatLng(12.404389, 122.594719),
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);

  // Try HTML5 geolocation
  if(navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var pos = new google.maps.LatLng(position.coords.latitude,
                                       position.coords.longitude);
      var myPoints = [
           [14.615421,120.981064],
           [14.615411,120.981569],
           [14.615053,120.981569],
           [14.61498,120.981612],
           [14.614954,120.982143],
           [14.615457,120.982132],
           [14.615447,120.982658]
        ];
      var myMarkers = [];
      for (var i in myPoints) {
          // myMarkers.push (new google.maps.LatLng (myPoints[i][0], myPoints[i][1]) );
          var points = new google.maps.LatLng (myPoints[i][0], myPoints[i][1]); 
          var infowindow = new google.maps.InfoWindow({
            map: map,
            position: points,
            content: '<img src="">'
          });
      }
     
      // var center = new google.maps.LatLng(12.404389, 122.594719);
      // map.setCenter(center);

    }, function() {
      handleNoGeolocation(true);
    });
  } else {
    // Browser doesn't support Geolocation
    handleNoGeolocation(false);
  }
}

function handleNoGeolocation(errorFlag) {
  if (errorFlag) {
    var content = 'Error: The Geolocation service failed.';
  } else {
    var content = 'Error: Your browser doesn\'t support geolocation.';
  }

  var options = {
    map: map,
    position: new google.maps.LatLng(60, 105),
    content: content
  };

  var infowindow = new google.maps.InfoWindow(options);
  map.setCenter(options.position);
}

google.maps.event.addDomListener(window, 'load', initialize);

    </script>