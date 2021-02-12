<?php
include('db_ip.php');

// COLOCAR EN EL GET - NECESITA PERMISOS PARA ESCRIBIR ARCHIVOS, CON EL CRONTAB, SIEMPRE SE EJECUTRA CON SUFICIENTE 
// POR ENDE, SE GENERAN LAS PAGINAS PARA CADA IP DE ESTA FORMA, CADA VEZ QUE CRON EJECUTE
// NOTA: TAMBIEN REALIZAR CONEXION A BASE DE DATOS Y CREACION DE ARRAY EN UN PHP APARTE, PARA NO VOLVER A ESCRIBIR TODO
// SI EN GET, COMO EN IN.PHP (GENERA TABLAS HTML) Y DATA 2

$ip_cont = 0;
$pos_c= 0;
echo "<table class='m_table'>\n";
foreach ($ip_arr as $key)
  {
  $ip_cont++;
  if (!fmod($pos_c,2))
  	echo '<tr>';
	echo "\t<td>\n";
	echo "<div id='container$ip_cont' style='height: 400px; min-width: 29em; max-width:29em;'></div>";
	echo "\t</td>\n";
	echo"\n\n";
  if (fmod($pos_c,2)) echo '</tr>';
  $pos_c++;
  }
echo "</table>";
 ?>