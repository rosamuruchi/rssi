<html>
  <head>
    <link rel="stylesheet" type="text/css" href="css/css.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css">
    <script src="https://apis.google.com/js/api:client.js"></script>
    <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script>
    <script src="http://malsup.github.com/jquery.form.js"></script>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
       <style>
    @import url("https://fonts.googleapis.com/css?family=Source+Sans+Pro:300");
    .label{
    font-family: 'Roboto Slab', serif;
    float:right;
    position: relative;
    top:-29px;
    left: -60px;
    font-size: xx-large;
    color:#424242;
    }
    #customBtn{
    position: absolute;    
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 20%;
    }
    body {
      background: url("../images/background_IDC.jpg");
      background-size: 100%;
    }
.no-js #loader { display: none;  }
.js #loader { display: block; position: absolute; left: 100px; top: 0; }
.se-pre-con {
  position: fixed;
  left: 0px;
  top: 0px;
  width: 100%;
  height: 100%;
  z-index: 9999;
  background: url(../images/preloader/128x128/Preloader_8.gif) center no-repeat #fff;
}
.avatar img {
  display: block;
  margin: 0 auto;
  border-radius: 100%;
  box-shadow: 0 0 0 0.1em #ffffff;
}
#avatar_text_h1 {
  height: 10%; 
  width:100%;
  display:flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-family: "Source Sans Pro", Helvetica, sans-serif;
  font-size: 3em;
  letter-spacing: 0.22em;
  margin: 0 0 0.525em 0;
}
.avatar_text {
  height: 0%; 
  width:100%;
  display:flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-family: "Source Sans Pro", Helvetica, sans-serif;
  font-size: 2em;
}

    </style>
    <script>
  //paste this code under head tag or in a seperate js file.
  // Wait for window load
  $(window).load(function() {
    // Animate loader off screen
    $(".se-pre-con").fadeOut("slow");;
  });
</script>
    <script>
      var googleUser = {};
      var startApp = function()
      {
        gapi.load('auth2',
        function()
        {
          //ID de los usuarios que pueden ingresar al programa de tareas ---- el 310***.. es de tecoar, lo administra gustavo
          $davidRamosId = "310937943960-u4sdr4qlhp24ps0iqm258vtgtsehuo7e.apps.googleusercontent.com";
          // Retrieve the singleton for the GoogleAuth library and set up the client.
          auth2 = gapi.auth2.init({client_id: $davidRamosId, cookiepolicy: 'single_host_origin', scope: 'profile'});
          attachSignin(document.getElementById('customBtn'));
        });
      };

      function attachSignin(element)
      {
        console.log(element.id);
        auth2.attachClickHandler(element, {},
        function(googleUser)
        {
          var email = googleUser.getBasicProfile().getEmail();

          window.location.href = "./session_start.php?username=" + email;
        },
        function(error)
        {
          alert("No se pudo acceder");
        });
      }
    </script>
  </head>
  <body>
      <div class="se-pre-con"></div>
              <header>
          <span class="avatar"><img src="../images/avatar.jpg" alt=""></span>
                    <h1 id="avatar_text_h1">Tecoar</h1>
                    <p class="avatar_text">Estadisticas de RSSI</p>
              </header>
    <form>
      <!-- In the callback, you would hide the gSignInWrapper element on a successful sign in -->
      <div class="div_contenedor_login">
        <div class="div_login">
          <div id="gSignInWrapper">
             <img src="./images/signin.png" id="customBtn" class="customGPlusSignIn" >
              <span class="icon" type="submit"></span>
              <!--span class="buttonText" type="submit">Google</span-->
            </div>
          </div>
        </div>
      </div>
      <script>startApp();</script>
    </form>
  </body>
</html>
