<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("location: login.php");
    exit;
}
?>

<?php
// Include config file
require_once 'config.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Modificar datos IP</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
        <style type="text/css">
            body{ font: 14px sans-serif; }
            .wrapper{ width: 350px; padding: 20px; }
            .rutas{  width: 600px; padding: 20px; font: 11px sans-serif; }  /* olga 16/11/18 */ 
        </style>
    </head>
    <body>
        <div class="wrapper"> 
            <p><a href="welcome.php" class="btn btn-danger">Volver a Inicio</a></p>
            <h2>Seleccione el equipo a modificar.</h2>
            <p>ID - Nombre del equipo - IP</p>
            <form action="./modificar_datos.php" method="get">
                
                <br /><br />
                <select name='id_equipos'>
                    <?php
                    include('config.php');
                    
                    
                    $query_por_id = "SELECT * FROM equipos";
                    $do_query_pid = $link->query($query_por_id);
                    while ($data_id = $do_query_pid->fetch_assoc()) {
                        if ($data_id['send_alert'] == '1') {
                            $alerta = 'Alertas encendidas';
                        } elseif ($data_id['send_alert'] == '0') {
                            $alerta = 'Alertas apagadas';
                        }
                        echo "<option value='" . $data_id['id_equipo'] . "'>" . $data_id['id_equipo'] . ' - ' . $data_id['device_name'] . ' - ' . $data_id['ip'] . ' - ' . $data_id['location'] . ' - ' . $alerta . "</option>\n";
                    }
                    ?>
                </select>
                <script type="text/javascript">
                    function confirm_delete() {
                        return confirm('¿Estas seguro/a?');
                    }
                </script>
                <div class="form-group"><!-- le da el relleno azul a los botones -->
                    <input type="submit" class="btn btn-primary" value="Buscar">
                    <button name='submit' value='1' onclick="return confirm_delete()">Eliminar</button>
                </div>
            </form>
        </div>
       <!---------------------------Olga inicio 16/11/18---------------------->
        <!--
        Para cargar los equipos desde /modificar.php
        Los equipos tienen que ser ingresados empezando por el padre "mayor"
        -->

        <div class="rutas" > 
            <form >
                <label for = "descripcion">Rutas existentes (id - nombre del equipo )</label> <br/>
                <div style="background-color:  #f8f9f9 ">
                              
                    <?php
                    
                    $array_hijos_id = array();
                    $query_1 = "SELECT id_equipo_hijo FROM  padre_hijo  GROUP BY id_equipo_hijo  "; 
                    $query_2 = $link->query($query_1);
                    while ($gett = $query_2->fetch_assoc()) {
                        array_push($array_hijos_id, $gett['id_equipo_hijo']); 
                    }
                    
                    

                    $array__repetidos = array();
                    $query_1a = "SELECT id_equipo_padre FROM  padre_hijo  GROUP BY id_equipo_padre  "; 
                    $query_2a = $link->query($query_1a);
                    while ($get = $query_2a->fetch_assoc()) {
                        array_push($array__repetidos, $get['id_equipo_padre']); 
                    }
                    $rep = 0;


                    echo "<br>";
                    for ($count = 0; $count < count($array_hijos_id); $count++) { 
                        $rep = 0; 

                        for ($count2 = 0; $count2 < count($array__repetidos); $count2++) {
                            if ($array__repetidos[$count2] == $array_hijos_id[$count]) {
                                $rep = $rep + 1; 
                            }
                        }
                        if ($rep == 0) { 

                            $array_padres_id = array();
                            $array_padres_name = array();
                            $array_padres_send = array();
                            $query_2 = "SELECT *FROM  padre_hijo WHERE  id_equipo_hijo= '$array_hijos_id[$count]' ";
                            $query_3 = $link->query($query_2); 
                            while ($getto = $query_3->fetch_assoc()) {
                                array_push($array_padres_id, $getto['id_equipo_padre']);
                                array_push($array_padres_name, $getto['device_name_padre']);
                                array_push($array_padres_send, $getto['send_alert_padre']);
                            }
                            for ($count3 = 0; $count3 < count($array_padres_id); $count3++) { 

                                echo "(";
                                echo $array_padres_id[$count3];
                                echo ") ";
                                echo $array_padres_name[$count3];
                                if ($array_padres_send[$count3] == 0) {
                                    echo "<FONT FACE='raro, courier' SIZE=1 COLOR='red'> Apagado </FONT>";
                                }
                                echo " <-- ";
                            }
                            
                            $query_4 = "SELECT DISTINCT * FROM `padre_hijo` WHERE `id_equipo_hijo` ='$array_hijos_id[$count]' ";
                            $query_5 = $link->query($query_4); 
                            $datos_hijo = $query_5->fetch_assoc();
                            echo "(";
                            echo $array_hijos_id[$count];
                            echo ") ";
                            echo $datos_hijo['device_name_hijo'];  
                            if ($datos_hijo['send_alert_hijo'] == 0) {
                                echo "<FONT FACE='raro, courier' SIZE=1 COLOR='red'> Apagado </FONT>";
                            }
                            echo "<br>";
                            echo "<br>";
                        }
                    } // FOR PRINCIPAL
                    //---------------------------
                    ?> 
                   
                </div>

            </form>


        </div>
        <!---------------------------Olga fin 16/11/18 ------------------------->
    </body>
</html>