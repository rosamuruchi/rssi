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
// create array of ips, order by ip
$array_ips = array();
$array_ips_query = "SELECT DISTINCT ip FROM equipos ORDER BY ip";
$exec_ips = $db->query($array_ips_query);
while ($gett = $exec_ips->fetch_assoc()) {
    array_push($array_ips, $gett['ip']);
}

// define an empty array of associative arrays,
// which will be formatted as -> [ip1 => [rx average, tx_average]], [ip2 => [rx_average, tx_average]]
$array_average = [];
foreach ($array_ips as $key) {
    // get rx average of the given ip
    $query_rx_avg = "SELECT AVG(rx_signal) as rx_average FROM data WHERE date_p >= DATE_SUB(NOW(), INTERVAL 6 HOUR) AND ip_db LIKE '$key'";
    $exec_rx_avg = $db->query($query_rx_avg);
    $rx_avg = $exec_rx_avg->fetch_assoc();
    // get tx average of given ip
    $query_tx_avg = "SELECT AVG(tx_signal) as tx_average FROM data WHERE date_p >= DATE_SUB(NOW(), INTERVAL 6 HOUR) AND ip_db LIKE '$key'";
    $exec_tx_avg = $db->query($query_tx_avg);
    $tx_avg = $exec_tx_avg->fetch_assoc();
    // array of round(average)
    $array_average[$key] = [round($rx_avg['rx_average']), round($tx_avg['tx_average'])];
}
//print_r($array_average);
$db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY',''))");
// Enable 'GROUP BY' in MYSQL
//print_r($array_first);
    $difference = [];
for($count = 0; $count < count($array_average); $count++) {
    $query_send = "SELECT send_alert FROM equipos WHERE ip LIKE '$array_ips[$count]'";
    $exec_query_send = $db->query($query_send);
    $send_alert = $exec_query_send->fetch_assoc();
    if($send_alert['send_alert'] == 1) {
        $query_val_alert = "SELECT alert_value, device_name FROM equipos WHERE ip LIKE '$array_ips[$count]'";
        $exec_val_alert = $db->query($query_val_alert);
        $val_alert = $exec_val_alert->fetch_assoc();
        $average_hours = round((($array_average[$array_ips[$count]][0]*-1) + ($array_average[$array_ips[$count]][1]*-1))/2);
        $average_diff = ((int)$average_hours) - ((int)$val_alert['alert_value']);
        echo $average_diff."\n\n\n";
        // Condition for sending the email: if the difference of the rx&tx average is equal/more than 15 dbi...

        if($average_diff >= 15) {
            // query for checking the alert_log table
            $query_alert_log = "SELECT * FROM alert_log WHERE ip LIKE '$array_ips[$count]'";
            $exec_verify_log = $db->query($query_alert_log);
            // if there is information, then check the date, calculate difference between the log and today AND...
            if($exec_verify_log->num_rows > 0) {
                $log_data = $exec_verify_log->fetch_assoc();
                $fecha_ultimo = $log_data['date_p'];
                /*
                echo $fecha_ultimo;
                echo "\n\n today: ";
                echo $date_s;
                echo "\n\n diff\n\n";*/
                $hourdiff = round((strtotime($date_s) - strtotime($fecha_ultimo))/3600, 1);
                //echo $hourdiff;

                // if the difference is >24 hours, then delete the log
                
                if($hourdiff >= 24) {
                    $query_delete_log = "DELETE FROM alert_log WHERE ip LIKE '$array_ips[$count]'";
                    $exec_delete_log = $db->query($query_delete_log);
                    if($exec_delete_log == true) echo "\n\nEliminado con exito\n\n";
                    //sendEmail($db, $array_ips[$count], $average_diff, $date_s);
                    
                    //---Olga 21/11/18
                    if (condicional_mail($array_ips[$count], $db)>0) { 
                        sendEmail($db, $array_ips[$count], $average_diff, $date_s);
                    }else { //si no envia el mail, de igual manera tiene que guardar los datos en tabla alert_log
                        $ip = $array_ips[$count];
                        $query_insert_log = "INSERT INTO alert_log (ip, difference, date_p) VALUES ('$ip', '$average_diff', '$date_s')";
                        if ($db->query($query_insert_log) == true) {
                            echo "\n\nSucessfully added to the alert_log\n\n";
                        } else
                            echo "\n\n\n\n\n ERRORRRRRRRRRRRR $db->error \n\n\n\n";
                    }
                    //---
                }
            }
            
            //echo $date_s - $fecha_ultimo;

            // If the past conditions doesn't get triggered, check if the result of the query is empty
            // that means that there is no log. Then if there is no data in the log, send email and log it.
            // this is adviced in the sendEmail function btw
            elseif($exec_verify_log->num_rows == 0) {
                // delete rx_diff variable usage, there is no such thing anymore and we don't use it at all in the email function in functions.php inside the includes folder
                sendEmail($db, $array_ips[$count], $average_diff, $date_s);
            }
        }
        else echo "\n\nIgnore sending alert\n\n";
        /*$difference[$array_ips[$count]] = [$rx_diff, $tx_diff];
        echo $array_ips[$count] ."\nrx: " . $rx_diff . ' tx: '.$tx_diff."\n\n";*/
    }
    else echo "Ignore send alert, the option is disabled manually for this device";
}
	
//print_r($difference);
$db->close();
?>