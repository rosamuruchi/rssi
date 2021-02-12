<?php 
include('db_ip.php');

    

    $region = strtoupper($_GET['region']);
    $query_ganancia = "SELECT ip, id_equipo, device_name,location FROM equipos WHERE location LIKE '$region' ORDER BY location DESC, device_name ASC";

    $id_equipo_array= [];
    $get_ganancia = $db->query($query_ganancia);
     
    $rxDato;
    $txDato;

    if($get_ganancia->num_rows==0)
    {
        echo "<h1>No hay enlaces acá</h1>";
    } 

    while ($ids = $get_ganancia->fetch_assoc()) {
        $id_equipo_array[$ids['id_equipo']] = [$ids['ip'], $ids['device_name'], $ids['location']];
        
        
    }

            // variable $highc consist on javascript code written in PHP nowdoc. there is three variables called $highcx 
            // because there is two loops (can't use loops inside EOT) and a piece of javascript code that needs
            // to be executed for selecting the range of the last day (Variable $selectr)
            foreach ($id_equipo_array as $id => $ip) {
                // query select everything where id of equipments equals to loop and order it by date
                //
                //$consulta = "select * from data WHERE id_equipo = '$id' order by date_p"; //cometado, olga dic-11-2018
                //
                //$consulta = "select date_p, rx_signal, tx_signal from data WHERE id_equipo = '$id' order by date_p";//olga dic-11-2018
                //$consulta_rx = "select date_p, rx_signal from data WHERE id_equipo = '$id' order by date_p"; //

                $consulta_rx = "select date_p, rx_signal from data WHERE id_equipo = '$id' AND date_p > '2021-01-12' order by date_p";
                //$consulta_rx = "select date_p, rx_signal from data WHERE id_equipo = '$id' AND date_p > '2020-10-19' order by date_p";
                
                $resultado = $db->query($consulta_rx);
                
                $highc= <<<EOT
                // Create the chart dyn_data
                var chart = new Highcharts.stockChart('container$id', {

                    rangeSelector: {
                        selected: 1
                    },
                    async:false,

                    title: {
                        text: '{$ip[0]} | {$ip[1]} | {$ip[2]}'
                    },
                    yAxis: {
                        min: -100, max: 1
                    },
                    series: [{
                        name: 'RX',
                        data: [ 
EOT;
            // echo $highc which is the first part of the Highchart Javascript code
            // Then do this loops to get the RX data, which is strtotime date and rx_signal
                echo $highc;
                while ($estadistica = $resultado->fetch_assoc()) {
                    echo "\n \t\t\t\t\t\t [".strtotime($estadistica['date_p'])."000".",".$estadistica['rx_signal']."],";
                    /*$rxDato=$estadistica['rx_signal'];
                    echo($rxDato);*/
                }
                // then, the tooltip of this chart and the end of the chart
                // then in the other lines of this eot, write the starting code (name and data parameters) for the TX Signal
                $highc2 = <<<'EOT'
                            ],
                        tooltip: {
                            valueDecimals: 0
                        }
                    },
                    

                    
                    {
                        name: 'TX',
                        data: [
EOT;
                // echo this then do loop for getting TX Signal data. Adding the last 000 to date_p
                // is because the timestamp that uses highchart (or javascript) is the linux/unix timestamp
                // but multiplied by 1000. And since php process strings unions faster than int multiplication...
                echo $highc2;
                //$consulta_tx = "select date_p, tx_signal from data WHERE id_equipo = '$id' order by date_p";
                
                $consulta_tx = "select date_p, tx_signal from data WHERE id_equipo = '$id' AND date_p > '2021-01-12' order by date_p";
                //$consulta_tx = "select date_p, tx_signal from data WHERE id_equipo = '$id' AND date_p > '2020-10-19' order by date_p";

                $resu = $db->query($consulta_tx);
                while ($esta = $resu->fetch_assoc()) {
                        echo "\n\t\t\t\t\t\t  [".strtotime($esta['date_p'])."000".",".$esta['tx_signal']."],";
                    }
                // EOT OF the tool tip of this series, or the TX signal
                $highc3 = <<<'EOT'
                            ],
                        tooltip: {
                            valueDecimals: 0
                        }
                    }]
                });
EOT;
                // echo this for writting it
                echo $highc3;
                // $Selectr which is the Select range, or the zoomed in chart to the last 2 days.
                $selectr = <<<'EOT'

                var d = new Date();
                chart.xAxis[0].setExtremes(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()-4 ), Date.UTC(d.getFullYear(), d.getMonth(), d.getDate() +1));


EOT;
                echo $selectr."\n\n";
                
            }
        
        ?>

