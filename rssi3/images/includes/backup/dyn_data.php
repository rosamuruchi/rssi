<?php
#date_default_timezone_set('America/Argentina/Ushuaia');

# Include db_ip which stands for the connection to the database and the creation of an array of ip's.

include('db_ip.php');
$countr = 0;

# foreach ip as $key. countr is for getting the quantity of html elements created as well as this way in dyn_tables.php
foreach ($ip_arr as $key) {
$ip_name = str_replace('.', '', $key).'.php';
$countr++;
# query select ip and order it by date
$consulta = "select * from info WHERE ip_db='$key' order by date_p";
$resultado = $db->query($consulta);
# highchart func
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
echo "\n            text: '$key ";
# same query but limit to 1 to get the interface of the device
$doqu = $db->query($consulta." DESC LIMIT 1");
$getin = $doqu->fetch_assoc(); 
echo " -  ".$getin['sys_name'];
echo " '";
echo "\n        },";
echo "\n";
echo "			subtitle: {\n text: '<a href=\"./details.php?ip_cont=$key\">details</a>'\n},";
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