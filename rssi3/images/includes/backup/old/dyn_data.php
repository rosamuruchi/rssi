<?php
#date_default_timezone_set('America/Argentina/Ushuaia');

include('db_ip.php');
$countr = 0;
foreach ($ip_arr as $key) {
$ip_name = str_replace('.', '', $key).'.php';
$countr++;
$consulta = "select * from info WHERE ip_db='$key' order by date_p";
$resultado = $db->query($consulta);
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
echo "\n            text: '$key'";
echo "\n        },";
echo "\n";
echo "			subtitle: {\n text: '<a href=\"./$ip_name\">details</a>'\n},";
echo "\n";
echo "\n        series: [{";
echo "\n            name: 'RX',";
echo "\n            data: [ ";
	while ($estadistica = $resultado->fetch_assoc()) {
		echo "\n  [".date(strtotime($estadistica['date_p']))."000".",".$estadistica['signl']."],";
	}
echo "\n  ],";
echo "\n            tooltip: {";
echo "\n                valueDecimals: 2";
echo "\n            }";
echo "\n        }]";
echo "\n    });";
echo "\n});";
echo "\n";

}
?>