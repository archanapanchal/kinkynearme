<!-- 

<html>
  <head>
    <title>reCAPTCHA demo: Explicit render for multiple widgets</title>
    <script type="text/javascript">
      var verifyCallback = function(response) {
        alert(response);
      };
      var widgetId1;
      var widgetId2;
      var onloadCallback = function() {
      
        widgetId1 = grecaptcha.render('example1', {
          'sitekey' : '6LeVDOUdAAAAACBoEYeF6tON_xIlSx2gBEgyex2U',
          'theme' : 'light'
        });
        widgetId2 = grecaptcha.render(document.getElementById('example2'), {
          'sitekey' : '6LeVDOUdAAAAACBoEYeF6tON_xIlSx2gBEgyex2U'
        });
        grecaptcha.render('example3', {
          'sitekey' : '6LeVDOUdAAAAACBoEYeF6tON_xIlSx2gBEgyex2U',
          'callback' : verifyCallback,
          'theme' : 'dark'
        });
      };
    </script>
  </head>
  <body>
   
    <form action="javascript:alert(grecaptcha.getResponse(widgetId1));">
      <div id="example1"></div>
      <br>
      <input type="submit" value="getResponse">
    </form>
    <br>
   
    <form action="javascript:grecaptcha.reset(widgetId2);">
      <div id="example2"></div>
      <br>
      <input type="submit" value="reset">
    </form>
    <br>
  
    <form action="?" method="POST">
      <div id="example3"></div>
      <br>
      <input type="submit" value="Submit">
    </form>
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"
        async defer>
    </script>
  </body>
</html> -->


<html>
  <head>
    <title>reCAPTCHA demo: Simple page</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  </head>
  <body>
    <form action="?" method="POST">
      <div class="g-recaptcha" data-sitekey="6LeVDOUdAAAAACBoEYeF6tON_xIlSx2gBEgyex2U"></div>
      <br/>
      <input type="submit" value="Submit">
    </form>
  </body>
</html>
