<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BikePonyExpress - Delivery Tracking</title>
    <link rel="stylesheet" href="/css/normalize.css" />
    <link rel="stylesheet" href="/css/foundation.min.css" />
    <link href='http://fonts.googleapis.com/css?family=Raleway:400,800' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/css/styles.css" />
    <script src="/js/vendor/modernizr.js"></script>
  </head>
  <body>
    
    <div class="small-12 columns separator show-for-small"></div>
    <nav>
      <div class="row">
        <div class="small-12 padded">
          <a class="nav-links" href="/">&lt Back</a>
        </div>
      </div>
    </nav>

    <header class="no-margin">
      <div class="row">
        <div class="small-12 small-centered columns center">

          <h1 class="title">{{$title}}</h1>
          <h4> – DELIVERY STATUS – </h4>
          <br>
        </div>
      </div>
    </header>  

    <div class="row">
      <div class="small-12 medium-6 large-6 columns center padded">
        <div id="map-canvas" class="map-canvas"></div>
      </div>

      <div class="small-12 medium-6 large-6 columns center padded">
        <div class="boxy padded delivery-info">
          <div class="row">
            <div class="small-12 columns column-value center"><h5 class="grey">DETAILS</h5></div>
          </div>
          <div class="row">
            <div class="small-6 columns column-label">Tracking Code:</div>
            <div class="small-6 columns column-value"><a href="<?php echo Config::get('app.url'); ?>/track/{{$tracking_code}}">{{$tracking_code}}</a></div>
          </div>
          <div class="row">
            <div class="small-6 columns column-label">Status:</div>
            <div class="small-6 columns column-value">{{$state}}</div>
          </div>
          <div class="row">
            <div class="small-6 columns column-label">Estimated pickup:</div>
            <div class="small-6 columns column-value">{{$estimated_pickup}}</div>
          </div>
          <div class="row">
            <div class="small-6 columns column-label">Estimated delivery:</div>
            <div class="small-6 columns column-value">{{$estimated_delivery}}</div>
          </div>
          @if ($delivery->state == 2)
          <div class="row">
            <div class="small-6 columns column-label">Signature:</div>
            <div class="small-6 columns column-label">
              <img class="signature" src="/uploads/{{$delivery->id}}_signature.png" alt="Signature" title="signature">
            </div>
          </div>
          @endif
          <div class="row">
            <div class="small-12 columns column-value grey center"><br><p class="no-margin">Updated: {{$current_dt}}</p></div>
          </div>
        </div>
      </div>
    </div>


    <footer>
      
    </footer>
    
    <script src="/js/vendor/jquery.js"></script>
    <script src="/js/foundation.min.js"></script>
    <script src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places"></script>
    <script src="/js/jquery.geocomplete.min.js"></script>
    <script>
      $(document).foundation();
    </script>
    <script>

      var delpos = new google.maps.LatLng( {{$lat}}, {{$lng}} );

      function initialize() {
        var mapOptions = {
          center: delpos,
          zoom: 16
        };
        
        var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

        var marker = new google.maps.Marker({
          position: delpos,
          map: map,
          title: 'Your parcel'
        });
      }
      
      google.maps.event.addDomListener(window, 'load', initialize);

    </script>
  </body>
</html>
