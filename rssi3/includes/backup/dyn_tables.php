<?php
include('db_ip.php');
$region = strtoupper($_GET['region']);
$query_ganancia = "SELECT ip, id_equipo, device_name, location FROM equipos WHERE location LIKE '$region' ORDER BY location DESC,device_name ASC";
$id_equipo_array= [];
$get_ganancia = $db->query($query_ganancia);
if($get_ganancia->num_rows==0) echo "<h1>No hay enlaces acá</h1>";
while ($ids = $get_ganancia->fetch_assoc()) {
    $id_equipo_array[$ids['id_equipo']] = [$ids['ip'], $ids['device_name'], $ids['location']];
}
	foreach ($id_equipo_array as $id => $ip)
	  {
		echo "\n<br />\n\t\t\t\t\t<div id='container$id' style='height: 400px; min-width: 100%; max-width:100%;'></div>";
	  echo "\n\t\t\t<hr>";
	}
 ?>