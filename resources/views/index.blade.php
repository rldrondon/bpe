<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BikePonyExpress - Welcome!</title>
    <link rel="stylesheet" href="/css/normalize.css" />
    <link rel="stylesheet" href="/css/foundation.min.css" />
    <link href='http://fonts.googleapis.com/css?family=Raleway:400,800' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/css/styles.css" />
    <script src="/js/vendor/modernizr.js"></script>
  </head>
  <body>
    
    <header>
       <div class="row">
        <div class="small-11 medium-8 small-centered columns center">
          <h1 class="title"><img title="BikePonyExpress" src="/img/bpe_logotext.png"></h1>
        </div>
      </div>
    </header>
    

    <div class="row">
      <div class="small-12 medium-6 large-6 columns center padded">
        <div class="boxy">
          <h3>I want to ship something</h3>
          <br>
          <a href="/request" class="button radius">Request Pickup</a>
        </div>
      </div>
      <div class="small-12 columns separator show-for-small"></div>
      <div class="small-12 medium-6 large-6 columns center padded">
      <div class="boxy">
          <h3>I want to track a delivery</h3>
          <br>
          <div class="row">
            <div class="small-11 medium-9 large-9 small-centered columns center">
              <form>
                <div class="row collapse" style="margin-top:.35em">
                  <div class="small-9 columns">
                    <input id="tracking_code" type="text" placeholder="Insert a Tracking Code" onkeydown="return fakeSubmit(event);">
                  </div>
                  <div class="small-3 columns">
                    <div id="tracking_submit" class="button postfix center">Go!</div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <footer>
    </footer>
    
    <script src="/js/vendor/jquery.js"></script>
    <script src="/js/foundation.min.js"></script>
    <script>
      $(document).foundation();
    </script>
    <script>
      $( document ).ready(function(){
        $("#tracking_submit").click(function(){
          if( $("#tracking_code").val() != "" )
            window.location.href = "/track/" + $("#tracking_code").val();
        });
      });

      function fakeSubmit(event)
      {
        if (event.keyCode == 13) {
          if( $("#tracking_code").val() != "" )
            window.location.href = "/track/" + $("#tracking_code").val();
          return false;
        }
      }
    </script>
  </body>
</html>
