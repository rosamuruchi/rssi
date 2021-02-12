<?php
include('db_ip.php');
$countr = 0;

// dynamic graphic generator. 

// $ip_arr is the list of IPs inside the db_ip.php, that is the db connection plus 
// creating the array of IPs.

// Basically, each Rickshaw graphic needs 10 elements to properly display or work
// these are: x_axis, y_axis, preview, hoverdetail, annotator, legend, shelving, order, 
// highlights and tickstreatment

// to diplay multiple rickshaw graphs, you need to create multiple instances of the Rickshaw 
// Class, so, with a foreach of the quantity of IPs, dinamycally creates the quantity of 
// rickshaws class instances or rickshaw objects

// Then, render the graphic.     The countr is for enumarate the ip and use this to select 
// the html elements in which this graphic will be display. eg #chart1, next ip loop, #chart2...

// the rendering position is very important, it'll not work if the rendering is not after is own // rickshaw object: var new Rickshaw ----- then----> render ----then ---> var new Ricksh...
// consulta1 and resultado1 are for the query to retrieve the last element date, to make sure
// the graphic adjustment will work, adds the value 100 at the last datetime and at the 
// beggining, without touching the database, just a query without modifiying the db.
// resu and esta are for getting the TX signal
echo "var palette = new Rickshaw.Color.Palette();\n";
foreach ($ip_arr as $key) {
	$countr++;
	#echo "$key";
	$res = mysqli_query($db,"SELECT SUM(diferencia) FROM (SELECT DATEDIFF(info.date_p, t2.date_p) AS diferencia FROM info LEFT JOIN info AS t2 ON t2.id = info.id -1)diferencia");
	if (FALSE === $res) die("Select sum failed: ");
	$row = mysqli_fetch_row($res);
	$sum = $row[0];
	$d_difference = (int)$sum;

	if($d_difference >= 365) {
		$consulta = "SELECT * FROM info JOIN info AS t2 on t2.id = info.id -1 WHERE DATEDIFF(info.date_p, t2.date_p) <> 0 AND ip_db ='$key'";
	}
	elseif($d_difference < 365) {
		$consulta = "select * from info WHERE ip_db='$key' order by date_p";

	}
		$resultado = $db->query($consulta);
		$resultado1 = $db->query($consulta);
		echo "var graph$countr = new Rickshaw.Graph( {\nelement: document.querySelector('#chart$countr'),\nwidth: 440,\nheight: 230,\nrenderer: 'line',\nstroke: true,\nseries: [";
		echo "{\n";
		echo '	name: '.'"'.'RX'.'"'.',' . "\n";
		echo '	data: [';
		while ($estadistica = $resultado->fetch_assoc()) {
			#echo $estadistica['id']."\n";
			echo "\n\t\t{ x: " . strtotime($estadistica['date_p']) ;
			echo ", y: " . $estadistica['signl'] . "}," ;

		}
		echo "\n\t" . '],';
		echo "\n\t" . 'color: palette.color()';
		echo "\n\t}, \n";
		// :::::::::: TX ::::::::::::::::
		echo "{\n";
		echo '	name: '.'"'.'TX'.'"'.',' . "\n";
		echo '	data: [';
		$resu = $db->query($consulta);
		while ($esta = $resu->fetch_assoc()) {
			#echo $estadistica['id']."\n";
			echo "\n\t\t{ x: " . strtotime($esta['date_p']) ;
			echo ", y: " . $esta['tx_signal'] . "}," ;

		}
		echo "\n\t" . '],';
		echo "\n\t" . 'color: palette.color()';
		echo "\n\t}, \n";
		// ::::::::::::::::::: Graph Adjustment (put 100 as max in the graph y axis) ::::::::::::::::::::
		echo "{\n";
		echo '	name: '.'"'.'Adjustment (IGNORE)'.'"'.',' . "\n";
		echo '	data: [';
		while ($estati = $resultado1->fetch_assoc()) {
			#echo $estadistica['id']."\n";
			echo "\n\t\t{ x: " . strtotime($estati['date_p']) ;
			echo ", y: " . '100' . "}," ;

		}
		echo "\n\t" . '],';
		echo "\n\t" . 'color: palette.color()';
		echo "\n\t}, ";
		echo "	]} );\n\n";
		echo "graph$countr.render();\n";
}

// The elements. Same, loop and name variables of elements with the correct names of each
// rickshaw object, and that's it

$countr9 = 0;
foreach ($ip_arr as $key) {
	$countr9++;
	echo"\nvar xAxis$countr9 = new Rickshaw.Graph.Axis.Time( { graph: graph$countr9, timeFixture: new ";
	echo "Rickshaw.Fixtures.Time.Local()\n";
 	echo "} );\n";
	echo "xAxis$countr9.render()\n";

	echo "\nvar y_axis$countr9 = new Rickshaw.Graph.Axis.Y( {\n";
	echo "\tgraph: graph$countr9,\n";
	echo "\torientation: 'left',\n";
	echo "\ttickFormat: Rickshaw.Fixtures.Number.formatKMBT,\n";
	echo "\ttickValues: [50, 60, 70, 100],\n";
	echo "\telement: document.getElementById('y_axis$countr9'),\n";
	echo "} );\n";
	echo "y_axis$countr9.render();\n";
	# code...
}

$countr1 = 0;
foreach ($ip_arr as $key) {
	$countr1++;
	echo "\nvar preview$countr1 = new Rickshaw.Graph.RangeSlider( {\n";
	echo "	graph: graph$countr1,\n";
	echo "element: document.getElementById('preview$countr1'),\n";
	echo "} );\n";
	# code...
}

$countr2 = 0;
foreach ($ip_arr as $key) {
	$countr2++;
	echo "\nvar hoverDetail$countr2 = new Rickshaw.Graph.HoverDetail( {\n";
	echo "\tgraph: graph$countr2,\n";
	echo "\txFormatter: function(x) {\n";
	echo "\t\treturn new Date(x * 1000).toString(); \n";
	echo "}} );\n";

	echo "\nvar annotator$countr2 = new Rickshaw.Graph.Annotate( {";
	echo "\tgraph: graph$countr2,\n";
	echo "\telement: document.getElementById('timeline$countr2')";
	echo "} );\n";

	echo "\nvar legend$countr2 = new Rickshaw.Graph.Legend( {\n";
	echo "\telement: document.querySelector('#legend$countr2'),\n";
	echo "\tgraph: graph$countr2\n";
	echo "} );\n";

	echo "\nvar shelving$countr2 = new Rickshaw.Graph.Behavior.Series.Toggle( {\n";
	echo "\tgraph: graph$countr2,\n";
	echo "\tlegend: legend$countr2\n } );";

	echo "\nvar order$countr2 = new Rickshaw.Graph.Behavior.Series.Order( {\n";
	echo "\tgraph: graph$countr2,\n";
	echo "\tlegend: legend$countr2\n";
	echo "} );\n";


	echo "\nvar highlighter$countr2 = new Rickshaw.Graph.Behavior.Series.Highlight( {\n";
	echo "\tgraph: graph$countr2,\n";
	echo "\tlegend: legend$countr2\n";
	echo "} );\n";
}
?>
