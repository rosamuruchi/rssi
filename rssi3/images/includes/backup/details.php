<!DOCTYPE html>

<html>
<head>
	<link type='text/css' rel='stylesheet' href='http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css'>
	<link type='text/css' rel='stylesheet' href='../src/css/graph.css'>
	<link type='text/css' rel='stylesheet' href='../src/css/detail.css'>
	<link type='text/css' rel='stylesheet' href='../src/css/legend.css'>
	<link type='text/css' rel='stylesheet' href='css/extensions.css?v=2'>

	<script src='../vendor/d3.v3.js'></script>
	<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js'></script>
	<script>
		jQuery.noConflict();
	</script>
	<script src='https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.15/jquery-ui.min.js'></script>
		<script src='https://code.jquery.com/jquery-3.1.1.min.js'></script>
		<script src='https://code.highcharts.com/stock/highstock.js'></script>
		<script src='https://code.highcharts.com/stock/modules/exporting.js'></script>
	<script src='js/extensions.js'></script>
<meta http-equiv='content-type' content='text/html; charset=UTF-8'>
		<title>TECOAR - AMPLIADO</title>
		<meta charset='utf-8'>
		<meta name='viewport' content='width=device-width, initial-scale=1'>
		<!--[if lte IE 8]><script src='assets/js/html5shiv.js'></script><![endif]-->
		<link rel='stylesheet' href='Identity%20by%20HTML5%20UP_files/main.css'>
		<link rel='icon' type='image/png' sizes='16x16' href='http://intranet.tecoar.com.ar/favicon.png'>
		<!--[if lte IE 9]><link rel='stylesheet' href='assets/css/ie9.css' /><![endif]-->
		<!--[if lte IE 8]><link rel='stylesheet' href='assets/css/ie8.css' /><![endif]-->
		<noscript><link rel='stylesheet' href='assets/css/noscript.css' /></noscript>
	</head>
	<body class=''>
		<!-- Wrapper -->
		<div id='wrapper'>
			<!-- Main -->
			<section id='main'>
				<header>
					<a href="./"><span class='avatar'><img src='Identity%20by%20HTML5%20UP_files/avatar.jpg' alt=''></span></a>
					<h1>Tecoar</h1>
					<p>Estadisticas de Trafico</p>
				</header>
				<!-- DIV IZQ -->
		<div style='float: left; border: 1px solid transparent;  width: 120%; margin-bottom: 50px; padding-right: 30px;'>
					<?php

							echo "<tr>\n";
							echo "\t<td>\n";
							echo "<div id='container$ip_cont' style='height: 400px; min-width: 50em; max-width:65em;'></div>";
							echo "\t</td>\n";
							echo "</tr>\n\n";
						 ?>
		</div>
			</section>
			<!-- Footer -->
			<footer id='footer'>
				<ul class='copyright'>
					<li>© Jane Doe</li><li>Design: <a href='http://html5up.net/'>HTML5 UP</a></li>
				</ul>
			</footer>
		</div>

		<!-- Scripts -->
		<!--[if lte IE 8]><script src='assets/js/respond.min.js'></script><![endif]-->
		<script>
		if ('addEventListener' in window)
		{
			window.addEventListener('load', function() { document.body.className = document.body.className.replace(/\bis-loading\b/, ''); });
			document.body.className += (navigator.userAgent.match(/(MSIE|rv:11\.0)/) ? ' is-ie' : '');
		}
		</script>
	<script>
// instantiate our graph!
<?php 
session_start();

$ip_cont = $_GET['ip_cont'];
$db = new mysqli('localhost','root','toor12', '4test');
if (mysqli_connect_errno())
{
 echo 'Error al conectar a la Base de Datos.';
 exit;
}
$hoy = date('Y-m-d');

$consulta = "select * from info WHERE ip_db='$ip_cont' order by date_p";

$resultado = $db->query($consulta);
echo "\tHighcharts.setOptions({\n";
echo "\t\tglobal: {\n";
echo "\t\tuseUTC: false\n";
echo "\t\t\t\n}   });";
echo "\n$.getJSON('https://cdn.rawgit.com/highcharts/highcharts/2c6e896/samples/data/aapl-c.json', function (data) {";
echo "\n    // Create the chart";
echo "\n    Highcharts.stockChart('container$countr', {";
echo "\n";
echo "\n";
echo "\n        rangeSelector: {";
echo "\n            selected: 1";
echo "\n        },";
echo "\n";
echo "\n        title: {";
echo "\n            text: '$ip_cont ";
$doqu = $db->query($consulta." DESC LIMIT 1");
$getin = $doqu->fetch_assoc(); 
echo " -  ".$getin['sys_name'];
echo " '";
echo "\n        },";
echo "\n";
echo "\n";
echo "\n        series: [{";
echo "\n            name: 'RX',";
echo "\n            data: [ ";
	while ($estadistica = $resultado->fetch_assoc()) {
		echo "\n  [".(strtotime($estadistica['date_p'])+3600)."000".",".$estadistica['signl']."],";
	}
echo "\n  ],";
echo "\n            tooltip: {";
echo "\n                valueDecimals: 0";
echo "\n            }";
echo "\n        }, {";
echo "\n 			name: 'TX',";
echo "\n 			data: [";
$resu = $db->query($consulta);
while ($esta = $resu->fetch_assoc()) {
		echo "\n  [".(strtotime($esta['date_p'])+3600)."000".",".$esta['tx_signal']."],";
	}
echo "\n],";
echo "\n            tooltip: {";
echo "\n 				valueDecimals: 0";
echo "\n 			}";
echo "\n}]";
echo "\n    });";
echo "\n});";
echo "\n";
?>
</script></body></html>