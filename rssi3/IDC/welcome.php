<?php
// Initialize the session
session_start();
 
// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
  header("location: login.php");
  exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.2/modernizr.js"></script>
    <style type="text/css">
        @import url("https://fonts.googleapis.com/css?family=Source+Sans+Pro:300");
body{
    font: 14px sans-serif;
    text-align: center;
    background: url("../images/background_IDC.jpg") no-repeat center center fixed #000; 
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
    }
.txt-h1 {
    color: white;
    margin-top: 11%;
    text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;
}
.txt-h2 {
    color: white;
    margin-top: 12%;
    text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;
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

</head>
<body>
    <div class="se-pre-con"></div>
<header>
          <span class="avatar"><a href="../"><img src="../images/avatar.jpg" alt="" /></a></span>
                    <h1 id="avatar_text_h1">Tecoar</h1>
                    <p class="avatar_text">Estadisticas de RSSI</p>
              </header>
        <h1 class="txt-h1"><b><?php echo $_SESSION['username']; ?></b> has iniciado sesion</h1>
        <h1 class="txt-h2"><b><?php if($_GET['estado'] === 'equipo_agregado') echo "Agregado con exito";
        elseif($_GET['estado']==='equipo_eliminado') echo "Removido con exito";
        elseif ($_GET['estado'] === 'equipo_modificado') echo "Modificado con exito";
        else echo ""; ?></h1>
        <hr />
    <a href="agregar_ip.php" class="btn btn-default">Agregar datos GANANCIA <span class="glyphicon glyphicon-plus-sign"></span></a>
    <a href="modificar.php" class="btn btn-default">Modificar datos GANANCIA <span class="glyphicon glyphicon-pencil"></span></a>
    <a href="../index.html" class="btn btn-default">Ir a los graficos <span class="glyphicon glyphicon-signal"></span></a>
    <a href="logout.php" class="btn btn-default">Cerrar sesion <span class="glyphicon glyphicon-log-in"></span></a>
</body>
</html>