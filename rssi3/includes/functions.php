<?php
include('db_ip.php');

function varDumpToString($var) {
    ob_start();
    var_dump($var->getError());
    $result = ob_get_clean();
    return $result;
}

function sendEmail($db, $ip, $average_diff, $date_s) {
                $query_name = "SELECT device_name, location, id_equipo, alert_value FROM equipos WHERE ip LIKE '$ip'";
                $exec_name = $db->query($query_name);
                $data_device = $exec_name->fetch_assoc();

                $name_device = $data_device['device_name'];
                $location = $data_device['location'];
                $id_equipo = $data_device['id_equipo'];
                $alert_value = $data_device['alert_value'];
                //$link_chart = 'http://intranet.tecoar.com.ar/rssi/rssi.php?region='.$location.'#container'.$data_device['id_equipo'];

                //04/02/2019
    			$link_chart = 'http://intranet.tecoar.com.ar/rssi/rssiEvento.php?ip=' . $ip ;
    			//


                $mail = new PHPMailer\PHPMailer\PHPMailer;
                $mail->IsSMTP(); // telling the class to use SMTP
                $mail->Host       = "mail.tecoar.com.ar"; // SMTP server
                $mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
                                                           // 1 = errors and messages
                                                           // 2 = messages only
                $mail->SMTPAuth   = true;                  // enable SMTP authentication
                $mail->SMTPSecure = "ssl";
                $mail->Host       = "smtp.gmail.com";      // SMTP server
                $mail->Port       = 465;                   // SMTP port
                
                //$mail->Username   = "david.penott@tecoar.com.ar";  // username de su cuenta en Gmail SMTP  //comentado dic-13
                //$mail->Password   = "#12&98-Seis_Veinte";            // password de su cuenta en Gmail SMTP  //comentado dic-13

                //de:  Sistema de Alertas <david.penott@tecoar.com.ar>
                $mail->Username   = "notificaciones@tecoar.com.ar";  // usuario y contraseña de cuenta en el hosting SMTP, para enviar mensajes
                $mail->Password   = "cyb3rsh0t2020";  //aca va mi contraseña !!

                $mail->SetFrom('alertas@tecoar.com.ar', 'Sistema de Alertas');
                $mail->Subject    = "Alerta - perdida de ganancia enlace $name_device ";
                $mail->MsgHTML("La perdida del promedio de las ultimas 6 horas es mayor o igual a 15 dbi respecto al primer valor historico.
                    <br />El valor de referencia actual es de $alert_value
                    <br />La diferencia lo supera por $average_diff
                    <br /><strong>Equipo: $id_equipo - $name_device - $location <strong />
                    <br />IP: $ip
                    <br />Fecha: $date_s
                    <br /><a href='$link_chart'>Ver gráficos.</a>
                    <br />
                    <br />
                    <br /><pre>&nbsp;</pre>
                    <br />RSSI- TECOAR");

                //$address = "alejandropenott@gmail.com"; //comentado dic 13
                //$mail->AddAddress($address, "David R."); //comentado dic 13

                //para: "Olga L." <olga.lopez@tecoar.com.ar>
                $address = "gustavo.alvarez@tecoar.com.ar";  //temporal
                $mail->AddAddress($address, "Gustavo A.");

                Cc:
                $recipients = array(
                  'nicolasc.siasa@gmail.com' => 'Nicolas S.',
                  'miguel.telecemian@tecoar.com.ar' => 'Miguel T.',
                  'gilberto.lopez@tecoar.com.ar' => 'Gilberto L.',
                  'elvira.condoluci@tecoar.com.ar' => 'Elvira C.',
               );
                foreach($recipients as $email => $name)
                {
                   //$mail->AddCC($email, $name);
                }
                if(!$mail->Send()) {
                  echo "Mailer Error: " . $mail->ErrorInfo;
                } else {
                  echo "Message sent!";
                }
                //$rx_diff = round($rx_diff);
                $query_insert_log = "INSERT INTO alert_log (ip, difference, date_p) VALUES ('$ip', '$average_diff', '$date_s')";
                if($db->query($query_insert_log) == true) {
                    echo "\n\nSucessfully added to the alert_log\n\n";
                }
                else echo "\n\n\n\n\n ERRORRRRRRRRRRRR $db->error \n\n\n\n";
}

?>
