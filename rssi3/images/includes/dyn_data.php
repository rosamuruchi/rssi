<?php
//date_default_timezone_set('America/Argentina/Ushuaia');

// Include db_ip which stands for the connection to the database and the creation of an array of ip's.

include('db_ip.php');

// from ip equipo array get each key element inside the array (get each id per single loop) also, get its value as $ip, or the value..
// assignated to that id, or ip 
foreach ($id_equipo_array as $id => $ip) {
# query select everything where id of equipments equals to loop and order it by date
$consulta = "select * from data WHERE id_equipo = '$id' order by date_p";
$resultado = $db->query($consulta);
# highchart func
echo "\tHighcharts.setOptions({\n";
echo "\t\tglobal: {\n";
echo "\t\tuseUTC: false\n";
echo "\t\t\t\n}   });";
echo "\n$.getJSON('https://cdn.rawgit.com/highcharts/highcharts/2c6e896/samples/data/aapl-c.json', function (data) {";
echo "\n    // Create the chart";
echo "\n    Highcharts.stockChart('container$id', {";
echo "\n";
echo "\n";
echo "\n        rangeSelector: {";
echo "\n            selected: 1";
echo "\n        },";
echo "\n";
echo "\n        title: {";
$zona = '';
if($ip[2] == 3) {
	$zona = 'CABA';
}
elseif ($ip[2] == 2) {
	$zona = 'Buenos Aires';
	# code...
}
else {
	$zona = 'Exterior';
	# code...
}
echo "\n            text: '($id - $zona)  $ip[0] ";
# same query but limit to 1 to get the interface of the device

echo " -  ".$ip[1];
echo " '";
echo "\n        },";
echo "\n";
echo "\n";
echo "			yAxis: { max: 101 },";
echo "\n";
echo "\n        series: [{";
echo "\n            name: 'RX',";
echo "\n            data: [ ";
	while ($estadistica = $resultado->fetch_assoc()) {
		echo "\n  [".strtotime($estadistica['date_p'])."000".",".$estadistica['signl']."],";
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
		echo "\n  [".strtotime($esta['date_p'])."000".",".$esta['tx_signal']."],";
	}
echo "\n],";
echo "\n            tooltip: {";
echo "\n 				valueDecimals: 0";
echo "\n 			}";
echo "\n}]";
echo "\n    });";
echo "\n});";
echo "\n";

}
?>