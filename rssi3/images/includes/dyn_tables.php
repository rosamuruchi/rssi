<?php
include('db_ip.php');

// COLOCAR EN EL GET - NECESITA PERMISOS PARA ESCRIBIR ARCHIVOS, CON EL CRONTAB, SIEMPRE SE EJECUTRA CON SUFICIENTE 
// POR ENDE, SE GENERAN LAS PAGINAS PARA CADA IP DE ESTA FORMA, CADA VEZ QUE CRON EJECUTE
// NOTA: TAMBIEN REALIZAR CONEXION A BASE DE DATOS Y CREACION DE ARRAY EN UN PHP APARTE, PARA NO VOLVER A ESCRIBIR TODO
// SI EN GET, COMO EN IN.PHP (GENERA TABLAS HTML) Y DATA 2
foreach ($id_equipo_array as $id => $ip)
  {
	echo "\n<br />\n\t\t\t\t\t<div id='container$id' style='height: 400px; min-width: 65em; max-width:65em;'></div>";
  echo "\n\t\t\t<hr>";
}
 ?>