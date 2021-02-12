<!DOCTYPE html>
<!--
	Identity by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
	-->
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>TECOAR</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!--[if lte IE 8]><script src="assets/js/html5shiv.js"></script><![endif]-->
		<link rel="stylesheet" href="Identity%20by%20HTML5%20UP_files/main.css">
		<link rel="icon" type="image/png" sizes="16x16" href="http://intranet.tecoar.com.ar/favicon.png">
		<!--[if lte IE 9]><link rel="stylesheet" href="assets/css/ie9.css" /><![endif]-->
		<!--[if lte IE 8]><link rel="stylesheet" href="assets/css/ie8.css" /><![endif]-->
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
		<script src='https://code.jquery.com/jquery-3.1.1.min.js'></script>
		<script src='https://code.highcharts.com/stock/highstock.js'></script>
		<script src='https://code.highcharts.com/stock/modules/exporting.js'></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
	</head>
	<body class="" data-gr-c-s-loaded="true">

		<!-- Wrapper -->
		<div id="wrapper">
			<!-- Main -->
			<section id="main">
				<header>
					<span class="avatar"><img src="Identity%20by%20HTML5%20UP_files/avatar.jpg" alt=""></span>
					<h1>Tecoar</h1>
					<p>Estadisticas de Trafico</p>
					<p><a href="./IDC/login.php" class="btn btn-danger">Ingresar Datos</a></p>
				</header>
				<!-- DIV IZQ -->
		<div style="float: left; border: 1px solid transparent;  width: 900px; margin-bottom: 50px; padding-right: 30px;">
						<?php include('./includes/dyn_tables.php') ?>
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

	<script type="text/javascript">
		<?php
			include('./includes/dyn_data.php');
		 ?>

	</script>


</body></html>
