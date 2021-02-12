<?php
date_default_timezone_set('America/Argentina/Ushuaia');
require 'vendor/autoload.php';
include('db_ip.php');
include('includes/functions.php');
require_once 'ENVIAR_MAIL.php'; //<----------------------------------Olga 21/11/18
// this php is for:
// --- inserting rx_signal and tx_signal to the database (db: rssi; table: data).
// --- sending and automate alert email when rssi drops more than 15 dbi.

$date_s = date('Y-m-d H:i:s');

// Get rows with all columns from equipos sorting by different id_equipo
$query_ganancia = "SELECT * FROM equipos WHERE id_equipo IN (SELECT DISTINCT id_equipo FROM equipos)";

// Empty array. The format is: ['IP']=>['Oid_equipo for RX', 'Oid_equipo for TX', 'id_equipo', 'device name']
$ganancia_array= [];

// query to database
$get_ganancia = $db->query($query_ganancia);
// With the while, it takes the data per row
while($data_ganancia = $get_ganancia->fetch_assoc()) {
    $ganancia_array[$data_ganancia['ip']] = [$data_ganancia['oid_rx'], $data_ganancia['oid_tx'], $data_ganancia['id_equipo'], $data_ganancia['device_name'], $data_ganancia['location']];
}

// foreach row ips is the ip and $value is the array that's is formatted as explained above
foreach ($ganancia_array as $ips => $value) {
    // Get rx signal ($value[0] = oid for rx)

    // set variables where any further error will be saved
    $error_rx= '0';
    $error_tx = '0';
    // using php-snmp get rx signal value using value[0] which is the oid for rx
    //----------------------------------------------------------------------------------------------------------
    $rx_session = new SNMP(SNMP::VERSION_1, "$ips", "public");
    $comm_rx = $rx_session->get("$value[0]");
    // if command rx is false, fill rx_signal var with -100 and fill the error rx
    if($comm_rx == false) {
        $rx_signal = '-100';
        //$error_rx = substr(varDumpToString($rx_session), 10);
        $largo_rx = substr(varDumpToString($rx_session), 10); //modificacion  si $rx_session devuelve error de no such variable oid
        if(strlen($largo_rx) > 100){
        	$error_rx =substr($largo_rx, 1, 18);
        }
        else $error_rx = substr(varDumpToString($rx_session), 10);


    }
    // else if, we don't get a false executing the snmp, but get a -1 as returning value, 
    // fill the rx_signal var with -100 and fill the rx error var. It's not normal to get just -1 dbi
    
    elseif(filter_var($comm_rx, FILTER_SANITIZE_NUMBER_INT) >= -1) {
        $rx_signal = '-100';
        $error_rx = filter_var($comm_rx, FILTER_SANITIZE_NUMBER_INT);
    } else $rx_signal = filter_var($comm_rx, FILTER_SANITIZE_NUMBER_INT);

    // Get tx sigmal ($value[1] = oid for tx) 




    //----------------------------------------------------------------------------------------------------------
    $tx_session = new SNMP(SNMP::VERSION_1, "$ips", "public");
    $comm_tx = $tx_session->get("$value[1]");
    // if gets bool false, it means therer was an error, so, fill the variable with -100 and the error variable
    if($comm_tx == false) {
        $tx_signal = '-100';
        //$error_tx = substr(varDumpToString($tx_session), 10);
        $largo_tx = substr(varDumpToString($tx_session), 10);
        if(strlen($largo_tx) > 100){
        	$error_tx =substr($largo_tx, 1, 18);
        }
        else $error_tx = substr(varDumpToString($tx_session), 10);


    }
    // elseif, we don't get a false, and get value higher/equal to -1, fill with -100 and fill error var
    elseif(filter_var($comm_tx, FILTER_SANITIZE_NUMBER_INT) >= -1) {
        $tx_signal = '-100';
        $error_tx = filter_var($comm_tx, FILTER_SANITIZE_NUMBER_INT);
    // else, tx_signal is the int the snmp exec returns and there was no tx error
    } else $tx_signal = filter_var($comm_tx, FILTER_SANITIZE_NUMBER_INT);
    // get actual path for log

    //----------------------------------------------------------------------------------------------------------

    $get_path = realpath(dirname(__FILE__))."/log_output.txt";
    // giving permissions
    chmod('./log_output.txt', 0777);
    // creating the file
    $log = fopen($get_path, "a+");
    // output text
    $output_log= "\n".$date_s. ' '. $ips. ' - '. $value[3]. ' rx:' . $comm_rx .' tx: '.$comm_tx."\n";
    // write and close
    fwrite($log, $output_log);
    fclose($log);
    // Errors
    /*$error_rx = substr($error_rx, 25);
    $error_tx = substr($error_tx, 25);
    $errors = str_replace("'", '', $errors);
    $errors = str_replace('"', "", $errors);*/
    $error_rx = preg_replace('/\s+/','',$error_rx);
    $error_tx = preg_replace('/\s+/','',$error_tx);
    $errors = 'rx: '.  $error_rx   . ' tx: '.$error_tx;
    // if there was no errors, $errors variable will be: rx: 0   tx: 0   which stands for zero errors

    $sql = "INSERT INTO data (date_p, rx_signal, ip_db, tx_signal, id_equipo, location, errors) VALUES ('$date_s','$rx_signal','$ips','$tx_signal', '$value[2]', '$value[4]', '$errors')"; 
    // if the query gets executed successfully, else, show error.
    if ($db->query($sql) === TRUE) {
        echo "\n\nNew record created successfully\n";
        $rx_session->close();
        $tx_session->close();
    } 
    else echo "\n\nError: " . $sql . $db->error;
}

	
//print_r($difference);
$db->close();
?>