<?php
date_default_timezone_set('America/Argentina/Ushuaia');
$db = new mysqli('intranet.tecoar.com.ar','rssi','Rssi123.', 'rssi');
if (mysqli_connect_errno())
{
 echo 'Error al conectar a la Base de Datos. ERRROR '.  $db->connect_error;
 exit;
}


$hoy = date('Y-m-d');

//$query_ganancia = "SELECT ip, id_equipo, device_name, location FROM equipos ORDER BY location DESC, device_name ASC";

//$id_equipo_array= [];
//$get_ganancia = $db->query($query_ganancia);
//while ($ids = $get_ganancia->fetch_assoc()) {
//	$id_equipo_array[$ids['id_equipo']] = [$ids['ip'], $ids['device_name'], $ids['location']];
//
?>
