<!DOCTYPE html>
<!--
	Identity by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
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
		
		<style>
			.no-js #loader { display: none;  }
			.js #loader { display: block; position: absolute; left: 100px; top: 0; }
			.se-pre-con {
				position: fixed;
				left: 0px;
				top: 0px;
				width: 100%;
				height: 100%;
				z-index: 9999;
				background: url(./images/preloader/128x128/Preloader_6.gif) center no-repeat #fff;
			}
		</style>
		
		<script>
			//paste this code under head tag or in a seperate js file.
			// Wait for window load
			$(window).load(function() {
				// Animate loader off screen
				$(".se-pre-con").fadeOut("slow");;
			});
			console.log("Empieza la ejecucion en rssi.php");
		</script>
	
	</head>

	<body class="" data-gr-c-s-loaded="true">
	
	<div class="se-pre-con"></div>
	
	

		<!-- Wrapper -->
		<div id="wrapper">
			<!-- Main -->
			<section id="main">
				<header>
					<a href="./"><span class="avatar"><img src="./images/avatar.jpg" alt=""></span></a>
					<h1>Tecoar</h1>
					<p>Estadisticas de RSSI</p>
					<p><a href="./IDC/welcome.php" class="btn btn-danger">Ingresar Datos</a></p>
				</header>
				<!-- DIV IZQ -->
		<div id="divTable" style="float: left; border: 1px solid transparent;  width: 100%; margin-bottom: 50px;">
			<?php
				 include('includes/dyn_tables.php');

			?>

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
			window.addEventListener('load', function() { document.body.className = document.body.className.replace(/\bis-loading\b/, ''); });
			document.body.className += (navigator.userAgent.match(/(MSIE|rv:11\.0)/) ? ' is-ie' : '');
		}
		</script>

	<script type='text/javascript' async="async">

/*$(function () {
    $(document).ready(function() {
        Highcharts.setOptions({
            global: {
                useUTC: false
            }
        });

		var chart;
        $('#container$id').highcharts({
            chart: {
                type: 'spline',
                animation: Highcharts.svg, // don't animate in old IE
                marginRight: 10,
                events: {
                    load: function() {
                        
                    }
                }
            }	
		});
	});

});*/
	    Highcharts.setOptions({
	        global: {
	            useUTC: false
			}   
			
	    });
		console.log("Estoy en Rssi-origin");

	    <?php
		include('includes/dyn_data.php');
		?>
		console.log("Incluyo dyn_data.php");
		
		/* setTimeout(function() {
		    location.reload();
		}, 60000);*/ 
	</script>
</body>
</html>
