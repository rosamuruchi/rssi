<?php
date_default_timezone_set('America/Argentina/Ushuaia');
include('db_ip.php');
// this php is for inserting data to the database. but as well is for creating the dinamyc php webpages of each ip address.

$date_s = date('Y-m-d H:i:s');


// Get rows with all columns fromequipos sorting by different id_equipo

$query_ganancia = "SELECT * FROM equipos WHERE id_equipo IN (SELECT DISTINCT id_equipo FROM equipos)";

// Empty array. The format is: ['IP']=>['Oid_equipo for RX', 'Oid_equipo for TX', 'id_equipo', 'device name']
$ganancia_array= [];

// query to database
$get_ganancia = $db->query($query_ganancia);
// With the while, it takes the data per row
while($data_ganancia = $get_ganancia->fetch_assoc()) {
    $ganancia_array[$data_ganancia['ip']] = [$data_ganancia['oid_rx'], $data_ganancia['oid_tx'], $data_ganancia['id_equipo'], $data_ganancia['device_name'], $data_ganancia['priority']];
}

// foreach row ips is the ip and $value is the array that's is formatted as explained above
foreach ($ganancia_array as $ips => $value) {

    // get rx signal ($value[0])
    $comm = "snmpget -v 2c -c public ".$ips." ".$value[0];
    $outp = exec($comm, $output, $signal_s);
    $powr = substr($outp, -3);
    $powr = str_replace('-', '', $powr);
    $powr = filter_var($powr, FILTER_SANITIZE_NUMBER_INT);
    // get TX signal ($value[1])
    $comm2 = "snmpget -v 2c -c public ".$ips." ".$value[1];
    $outp2 = exec($comm2, $output, $signal_s2);
 
    $powr2 = substr($outp2, -3);
    $powr2 = str_replace('-', '', $powr2);
    $powr2 = filter_var($powr2, FILTER_SANITIZE_NUMBER_INT);
    // Neanderthal way of errors excepcion/handling lmao
    if ($outp === '') {
        if ($outp2 === '')
            $sql = "INSERT INTO data (date_p, signl, tx_signal, ip_db, errors, id_equipo, priority) VALUES ('$date_s', '0', '0', '$ips', 'Timeout', '$value[2]', '10')";

        else {
            $sql = "INSERT INTO data (date_p, signl, tx_signal, ip_db, errors, id_equipo, priority) VALUES ('$date_s', '0', '$outp2', '$ips', 'Timeout', '$value[2]', '10')";

        }
    }
    if($outp2 === '') {
        if($outp === '') {
            $sql = "INSERT INTO data (date_p, signl, tx_signal, ip_db, errors, id_equipo, priority) VALUES ('$date_s', '0', '0', '$ips', 'Timeout', '$value[2]', '10')";

        }
        else {
            $sql = "INSERT INTO data (date_p, signl, tx_signal, ip_db, errors, id_equipo, priority) VALUES ('$date_s', '$outp', '0', '$ips', 'Timeout', '$value[2]', '10')";

        }
    }
     elseif (strpos($outp, 'OID') !== false) {
        $sql = "INSERT INTO data (date_p, signl, ip_db, errors, tx_signal, id_equipo, priority) VALUES ('$date_s', '0', '$ips', 'No Such Obj Available On This Agent at This OID', '0', '$value[2]', '10')";
    } else {
        $sql = "INSERT INTO data (date_p, signl, ip_db, tx_signal, id_equipo, priority) VALUES ('$date_s','$powr','$ips','$powr2', '$value[2]', '$value[4]')";
    }
    // if the query gets executed successfully, else, show error.
    if ($db->query($sql) === TRUE) {
    echo "\n\nNew record created successfully\n";
    } 
    else {
    echo "\n\nError: " . $sql . $db->error;
    }

}
$db->query('SET FOREIGN_KEY_CHECKS=1');
$db->close();
?>