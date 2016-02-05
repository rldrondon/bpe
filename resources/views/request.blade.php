<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BikePonyExpress - Request Pickup</title>
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
          <h1 class="title">Request Pickup</h1>
          <h4> – NEW DELIVERY – </h4>
          <br>
        </div>
      </div>
    </header>  

    <div class="row">
      <div class="small-12 medium-6 large-6 columns center padded">
        <div id="map-canvas" class="map-canvas"></div>
      </div>

      <div class="small-12 medium-6 large-6 columns center padded">
        <div class="boxy padded">
          <form data-abide id="request" name="request" action="/request" method="POST">
            <div class="sender_addr-field">
              <label>Sender Address <small>required</small>
                <input name="sender_address" id="sender_address" type="text" required>
              </label>
              <small class="error">Please specify the address of the sender.</small>
            </div>
            <div class="sender_email-field">
              <label>Sender Email <small>required</small>
                <input name="sender_email" type="email" required>
              </label>
              <small class="error">Please specify the email of the sender.</small>
            </div>
            <div class="sender_info-field">
              <label>Sender Info
                <input name="sender_info" type="text">
              </label>
            </div>

            <div class="recipient_addr-field">
              <label>Recipient Address <small>required</small>
                <input name="recipient_address" id="recipient_address" type="text" required>
              </label>
              <small class="error">Please specify the address of the recipient.</small>
            </div>
            <div class="recipient_email-field">
              <label>Recipient Email <small>required</small>
                <input name="recipient_email" type="email" required>
              </label>
              <small class="error">Please specify the email of the recipient.</small>
            </div>
            <div class="recipient_info-field">
              <label>Recipient Info
                <input name="recipient_info" type="text">
              </label>
            </div>

            <input id="sender_position" name="sender_position" type="hidden">
            <input id="recipient_position" name="recipient_position" type="hidden">

            <button class="radius" type="submit">Request Pickup</button>
          </form>

          <div class="hidden" id="sender_response">
            <input id="sender_location" name="location" type="hidden">
          </div>
          <div class="hidden" id="recipient_response">
            <input id="recipient_location" name="location" type="hidden">
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

      function initialize() {
        var mapOptions = {
          center: new google.maps.LatLng(45.0734887,7.6756066),
          zoom: 12
        };    
        window.map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
      }
      
      google.maps.event.addDomListener(window, 'load', initialize);
    </script>

    <script>

      // Turin's city area bounds 
      var southWest = new google.maps.LatLng(45.01648354363044, 7.595269075585975);
      var northEast = new google.maps.LatLng(45.13043705135183, 7.7559441244141);
      turinBounds = new google.maps.LatLngBounds(southWest,northEast);

      $(document).ready(function() {

        // Sender address geocoding + autocomplete:

        $("#sender_address").geocomplete({
          map: false,
          details: "#sender_response",
          bounds: turinBounds
        }).bind("geocode:result", function(event, result){

          var senderLatLng = getLatLngFromString($("#sender_location").val());
          if(!turinBounds.contains(senderLatLng)){
            alert("We are sorry, but we currenty deliver in central Turin only");
            document.getElementById("sender_address").blur();
            document.getElementById("sender_address").value="";
            return false;
          }

          $("#sender_position").val( $("#sender_location").val() );

          if(typeof senderMarker !== "undefined") 
            senderMarker.setMap(null);
          if(typeof recipientMarker !== "undefined")
            recipientMarker.setMap(null);

          window.map.setCenter(senderLatLng);

          senderMarker = new google.maps.Marker({
            position: senderLatLng,
            map: map,
            title: "Sender: " + $("#sender_address").val()
          });
        
        });

        // Recipient address:

        $("#recipient_address").geocomplete({
          map: false,
          details: "#recipient_response",
          bounds: turinBounds
        }).bind("geocode:result", function(event, result){

          recipientLatLng = getLatLngFromString($("#recipient_location").val())
          if(!turinBounds.contains(recipientLatLng)){
            alert("We are sorry, but we currenty deliver in central Turin only");
            document.getElementById("recipient_address").blur();
            document.getElementById("recipient_address").value="";
            return false;
          }

          $("#recipient_position").val( $("#recipient_location").val() );

          if(typeof senderMarker !== "undefined") 
            senderMarker.setMap(null);
          if(typeof recipientMarker !== "undefined")
            recipientMarker.setMap(null);

          window.map.setCenter(recipientLatLng);

          recipientMarker = new google.maps.Marker({
            position: recipientLatLng,
            map: map,
            title: "Recipient: " + $("#recipient_address").val()
          });

        });

      });

      function getLatLngFromString(ll) {
        var latlng = ll.split(',')
        return new google.maps.LatLng(parseFloat(latlng[0]), parseFloat(latlng[1])); 
      }

    </script>
  </body>
</html>
