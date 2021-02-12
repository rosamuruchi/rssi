<?php
include('db_ip.php');
$region = strtoupper($_GET['region']);
$query_ganancia = "SELECT ip, id_equipo, device_name, location FROM equipos WHERE location LIKE '$region' ORDER BY location DESC,device_name ASC ";
$id_equipo_array= [];
$get_ganancia = $db->query($query_ganancia);

$flag=false;
$cont=0;
$nombreEquipo;
$ipEquipo;
$locationEquipo;
if($get_ganancia->num_rows==0) echo "<h1>No hay enlaces acá</h1>"; //cantidad de tablas


while ($ids = $get_ganancia->fetch_assoc()) {
	$id_equipo_array[$ids['id_equipo']] = [$ids['ip'], $ids['device_name'], $ids['location']];
	$nombreEquipo= $ids['device_name'];
	$ip=$ids['ip'];
	$locationEquipo=$ids['location'];
	echo($nombreEquipo . " - " .$ip . " - " . $locationEquipo . "<br></br>");
}
 
	foreach ($id_equipo_array as $id => $ip)
	{
	  echo "\n\n\t\t\t\t\t<div id='container$id' style='height: 400px; min-width: 100%; max-width:100%;'></div>";
	  echo "\n\t\t\t<hr>";
		$cont++;
		
	  if($cont>1 && $flag==false)
	  {
		echo "<div> <p>SPINNER</p> </div>";
		echo "\n\t\t\t<hr>";
		
	  }
	  if($get_ganancia->num_rows == ($cont+1))				
	  {
		$flag=true;
		$cont=-1;
	  }
	  
	}
	
 ?>
 <script type='text/javascript'>

console.log("Estoy en dyn_tables");

</script>
 

