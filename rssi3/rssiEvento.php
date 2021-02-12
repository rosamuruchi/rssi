<!DOCTYPE html>
<!--
        Identity by HTML5 UP
        html5up.net | @ajlkn
        Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<!--
        Creado 04/02/2019
        Esta función sirve para cuando se envia un mensaje de alerta por mail 
        'http://intranet.tecoar.com.ar/rssi/rssiEvento.php?ip=' . $ip ;
        http://intranet.tecoar.com.ar/rssi/rssiEvento.php?ip=181.209.82.35
        
        Entonces rssiEvento.php >> llama a >> dyn_tablesEvento.php y dyn_dataEvento.php >> y les envia el IP del equipo
        dyn_tables.Evento.php => prepara el conteiner
        dyn_data.Evento.php => devuelve los datos de la tabla data, que pertenece al IP
-->


<html><head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title>TECOAR - RSSI</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--[if lte IE 8]><script src="assets/js/html5shiv.js"></script><![endif]-->
        <link rel="stylesheet" href="css/main.css">
        <link rel="icon" type="image/png" sizes="16x16" href="http://intranet.tecoar.com.ar/favicon.png">
        <!--[if lte IE 9]><link rel="stylesheet" href="assets/css/ie9.css" /><![endif]-->
        <!--[if lte IE 8]><link rel="stylesheet" href="assets/css/ie8.css" /><![endif]-->
        <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
        <script src='https://code.jquery.com/jquery-3.1.1.min.js'></script>
        <script src='https://code.highcharts.com/stock/highstock.js'></script>
        <script src='https://code.highcharts.com/stock/modules/exporting.js'></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">

        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
        <script src="http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.2/modernizr.js"></script>
     
        <script>
            //paste this code under head tag or in a seperate js file.
            // Wait for window load
            $(window).load(function () {
                // Animate loader off screen
                $(".se-pre-con").fadeOut("slow");
                ;
            });
        </script>
    </head>
    <body class="" data-gr-c-s-loaded="true">
        <div ></div>

        <!-- Wrapper -->
        <div id="wrapper">
            <!-- Main -->
            <section id="main">
               
                </select>

            <script>
                $(document).ready(function(){
                $("button").click(function(){
                    $("#divTable").delay("slow").fadeIn();
 
                    });
                });
            </script>    
                
                <!-- DIV IZQ -->
                <div id="divTable" style="float: left; border: 1px solid transparent;  width: 100%; margin-bottom: 50px;">
                    <?php include('includes/dyn_tablesEvento.php'); ?>
                </div>
                
            </section>

            <!-- Footer -->
            <footer id="footer">
                <ul class="copyright">
                    <li>© Jane Doe</li><li>Design: <a href="http://html5up.net/">HTML5 UP</a></li>
                </ul>
            </footer>
        </div>

        <!-- Scripts -->
        <!--[if lte IE 8]><script src="assets/js/respond.min.js"></script><![endif]-->
        <script>
            if ('addEventListener' in window)
            {
                window.addEventListener('load', function () {
                    document.body.className = document.body.className.replace(/\bis-loading\b/, '');
                });
                document.body.className += (navigator.userAgent.match(/(MSIE|rv:11\.0)/) ? ' is-ie' : '');
            }
        </script>

        <script type='text/javascript'>
            Highcharts.setOptions({
                global: {
                    useUTC: false
                }
            });
<?php
include('includes/dyn_dataEvento.php');
?>
console.log("incluyo dyn_dataEvento.php");
            /* setTimeout(function() {
             location.reload();
             }, 60000); */
        </script>
    </body>
</html>
