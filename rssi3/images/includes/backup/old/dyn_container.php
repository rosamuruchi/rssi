<?php
include('db_ip.php');

$counter=0;
foreach ($ip_arr as $key) {
	$counter++;
	echo "<div id='container$counter' style='height: 400px; max-width: 46.875em'></div>";

}
?>