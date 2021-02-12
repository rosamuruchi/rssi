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
require_once 'ELIMINAR.php'; //---------------- olga 20/11/18
require_once 'AGREGAR_PADRE.php'; //---------------- olga 20/11/18
include('../includes/functions.php');

if ($_GET['submit'] == 1) { // elimnia el equipo seleccionado para eliminar
    $link->query("SET FOREIGN_KEY_CHECKS=0");
    $id_delete = $_GET['id_equipos'];
    $query_delete = "DELETE FROM equipos WHERE id_equipo = '$id_delete'";

    eliminar_todo($id_delete, $link);  //-------elimina en la tabla padre_hijo ------------- Olga 20/11/18

    if ($link->query($query_delete) === TRUE) {
        echo "Consulta exitosa";
        $link->query("SET FOREIGN_KEY_CHECKS=1");
        header("Location: ./welcome.php?estado=equipo_eliminado");
    } else
        echo "Error en la consulta de eliminar " . $link->error;
}

else {
// Define variables and initialize with empty values
    $ip = $oid_rx = $oid_tx = $location = "";
    $ip_err = $oid_rx_err = $oid_tx_err = "";
    $id_hijo =  $id_equipo_padre ="";

// Processing form data when form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Validate ip
        if (empty(trim($_POST["ip"]))) { //trim(), ltrim() y rtrim() podemos eliminar espacios en blanco u 
            //otros caracteres al inicio y final de una cadena de texto.
            $ip_err = "Please enter a ip.";
        } else {
            $ip = trim($_POST['ip']);
        }

        // Validate oid_rx
        if (empty(trim($_POST['oid_rx']))) {
            $oid_rx_err = "Por favor ingrese un OID para la calidad RX.";
        } elseif (strlen(trim($_POST['oid_rx'])) < 6) {
            $oid_rx_err = "Un OID tiene que tener mas caracteres.";
        } else {
            $oid_rx = trim($_POST['oid_rx']);
        }

        // Validate oid_tx
        if (empty(trim($_POST['oid_tx']))) {
            $oid_tx_err = "Por favor ingrese un OID para la calidad TX.";
        } elseif (strlen(trim($_POST['oid_tx'])) < 6) {
            $oid_tx_err = "Un OID tiene que tener mas caracteres.";
        } else {
            $oid_tx = trim($_POST['oid_tx']);
        }

        $location = $_POST['location'];
        $send_alert = $_POST['send_alert'];
        $val_alert = $_POST['value_alert'];
        //$id_equipo = $_POST['id_equipo'];
        //-------------- Olga inicio 20/11/18-----------------------------------
        //esta funcion tendria que ir mas abajo
        //pero SNMP da error, asi que por ahora la dejo en este lugar
        $id_hijo = $_SESSION['id_equipos'];
        $id_equipo_padre = $_POST['id_equipo_padre']; // $_SESSION['id_equipos']=  18
        // accion si se presiona
        if ($id_equipo_padre == ' ') { // significa que la casilla padre se selecciono "no tiene" 
            eliminar_padre($_SESSION['id_equipos'], $link);
        } else {
            eliminar_padre($_SESSION['id_equipos'], $link); // $_SESSION['id_equipos']=  18

            agregar_padre2($_SESSION['id_equipos'],$send_alert, $id_equipo_padre, $link);
        }
        agregar_padre3($id_hijo, $send_alert, $link);

        //-------------Olga fin 20/11/18----------------------------------------
        //
        // Check input errors before inserting in database
        if (empty($ip_err) && empty($oid_rx_err) && empty($oid_tx_err)) {
            // Prepare an insert statement
            $id_equipos = $_SESSION['id_equipos'];
            $sql = "UPDATE equipos SET ip = ?, oid_rx = ?, oid_tx = ?, device_name = ?, location = ?, alert_value = ?, send_alert = ? WHERE id_equipo = '$id_equipos'";
            if ($stmt = mysqli_prepare($link, $sql)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "ssssssi", $param_ip, $param_oid_rx, $param_oid_tx, $param_device_name, $param_location, $param_alert_value, $param_send_alert);
                // Set parameters
                $name_session = new SNMP(SNMP::VERSION_1, $ip, "public");
                $dname = $name_session->walk("sysName");
                if ($dname == '') {
                    $device_name = substr(varDumpToString($name_session), 10);
                } else
                    $device_name = substr($dname['SNMPv2-MIB::sysName.0'], strpos($dname['SNMPv2-MIB::sysName.0'], 'STRING:') + 8, strpos($dname['SNMPv2-MIB::sysName.0'], substr($dname['SNMPv2-MIB::sysName.0'], -2)));
                if (strpos($device_name, 'No response') == !false) {
                    $query_name = "SELECT device_name FROM equipos WHERE id_equipo = '$id_equipos'";
                    $exec_query_name = $link->query($query_name);
                    $fetch_name = $exec_query_name->fetch_assoc();
                    $device_name = $fetch_name['device_name'];
                }
                $param_send_alert = $send_alert;
                $param_alert_value = $val_alert;
                $param_device_name = $device_name;
                $param_ip = $ip;
                $param_oid_rx = $oid_rx;
                $param_oid_tx = $oid_tx;
                $param_location = $location;
                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    // Also, executes this query to update values on data too
                    $sql_data = "UPDATE data SET ip_db = '$param_ip', location = '$param_location' WHERE id_equipo= '$id_equipos'";
                    if ($link->query($sql_data) === TRUE) {
                        printf("Se realizo la consulta con exito");
                    } else {
                        printf("Error. No se pudo realizar la consulta");
                    }
                    // Redirect to login page
                    header("Location: ./welcome.php?estado=equipo_modificado");
                } else {
                    echo "Algo va mal. Intente nuevamente luego. " . $stmt->error;
                }
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }
        // Close connection
        mysqli_close($link);
    } // termina if ($_SERVER["REQUEST_METHOD"] == "POST") 
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Modificar datos IP</title>
        <!--<meta name="viewport" content="width=device-width, initial-scale=1">-->
        <link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
        <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
        <script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
        </script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
        <style type="text/css">
            body{ font: 14px sans-serif; }
            .wrapper{ width: 350px; padding: 20px; }
        </style>

    </head>
    <?php
    $id_equipos = $_GET['id_equipos'];
    $_SESSION['id_equipos'] = $id_equipos;
//se modifico la consulta, se agrego el campo "id_equipo" 15/11/18
    $do_q = $link->query("SELECT ip, oid_rx, oid_tx, location, device_name, alert_value, send_alert, id_equipo FROM equipos WHERE id_equipo = '$id_equipos'");
    $data_modificar = $do_q->fetch_assoc();
    $ip_modify = $data_modificar['ip'];
    $query_rx_first = "SELECT rx_signal FROM data WHERE ip_db LIKE '$ip_modify' AND rx_signal > -100 order by date_p ASC LIMIT 1";
    $exec_rx_first = $link->query($query_rx_first);
    $first_rx = $exec_rx_first->fetch_assoc();
// Get first tx value
    $query_tx_first = "SELECT tx_signal FROM data WHERE ip_db LIKE '$ip_modify' AND tx_signal > -100  order by date_p ASC LIMIT 1";
    $exec_tx_first = $link->query($query_tx_first);
    $first_tx = $exec_tx_first->fetch_assoc();
    $average_first = round((($first_rx['rx_signal'] * -1) + ($first_tx['tx_signal'] * -1)) / 2);
    ?>
    <body>
        <div class="wrapper"style="width: 50%; margin: 0 auto;">
            <p><a href="modificar.php" data-ajax="false" class="btn btn-danger" style="color:white; margin-left:35%;">Volver atras</a>  <a href="welcome.php" data-ajax="false" class="btn btn-warning" style="color:white;">Ir a inicio</a></p>
            <h2>Ingrese los nuevos datos</h2>
            <p>Ganancia TECOAR. Rellenar campos. Todos son obligatorios.</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" data-ajax="false">
                <br /><br />
                <label><?php echo $data_modificar['device_name']; ?></label>
                <hr />
                <div class="form-group <?php echo (!empty($ip_err)) ? 'has-error' : ''; ?>">
                    <label><strong>IP</strong></label>
                    <input type="text" name="ip" class="form-control" value="<?php echo $data_modificar['ip']; ?>">
                    <span class="help-block"><?php echo $ip_err; ?></span>
                </div>    
                <div class="form-group <?php echo (!empty($oid_rx_err)) ? 'has-error' : ''; ?>">
                    <label><strong>OID para RX</strong></label>
                    <input type="oid_rx" name="oid_rx" class="form-control" value="<?php echo $data_modificar['oid_rx']; ?>">
                    <span class="help-block"><?php echo $oid_rx_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($oid_tx_err)) ? 'has-error' : ''; ?>">
                    <label><strong>OID para TX</strong></label>
                    <input type="oid_tx" name="oid_tx" class="form-control" value="<?php echo $data_modificar['oid_tx']; ?>">
                    <span class="help-block"><?php echo $oid_tx_err; ?></span>
                </div>
                <div class="form-group">
                    <label><strong>Ubicacion actual: <?php echo $data_modificar['location']; ?></strong></label>
                    <select name='location'>
                        <option <?php if ($data_modificar['location'] == 'BA') echo 'selected="selected"'; ?> value='BA'>Buenos Aires</option>
                        <option <?php if ($data_modificar['location'] == 'CABA') echo 'selected="selected"'; ?> value='CABA'>CABA</option>
                        <option <?php if ($data_modificar['location'] == 'CC') echo 'selected="selected"'; ?> value='CC'>Chaco</option>
                        <option <?php if ($data_modificar['location'] == 'CH') echo 'selected="selected"'; ?> value='CH'>Chubut</option>
                        <option <?php if ($data_modificar['location'] == 'CB') echo 'selected="selected"'; ?> value='CB'>Cordoba</option>
                        <option <?php if ($data_modificar['location'] == 'CN') echo 'selected="selected"'; ?> value='CN'>Corrientes</option>
                        <option <?php if ($data_modificar['location'] == 'ER') echo 'selected="selected"'; ?> value='ER'>Entre Rios</option>
                        <option <?php if ($data_modificar['location'] == 'FM') echo 'selected="selected"'; ?> value='FM'>Formosa</option>
                        <option <?php if ($data_modificar['location'] == 'JY') echo 'selected="selected"'; ?> value='JY'>Jujuy</option>
                        <option <?php if ($data_modificar['location'] == 'LP') echo 'selected="selected"'; ?> value='LP'>La Pampa</option>
                        <option <?php if ($data_modificar['location'] == 'LR') echo 'selected="selected"'; ?> value='LR'>La Rioja</option>
                        <option <?php if ($data_modificar['location'] == 'MZ') echo 'selected="selected"'; ?> value='MZ'>Mendoza</option>
                        <option <?php if ($data_modificar['location'] == 'MN') echo 'selected="selected"'; ?> value='MN'>Misiones</option>
                        <option <?php if ($data_modificar['location'] == 'NQ') echo 'selected="selected"'; ?> value='NQ'>Neuquen</option>
                        <option <?php if ($data_modificar['location'] == 'RN') echo 'selected="selected"'; ?> value='RN'>Rio Negro</option>
                        <option <?php if ($data_modificar['location'] == 'SA') echo 'selected="selected"'; ?> value='SA'>Salta</option>
                        <option <?php if ($data_modificar['location'] == 'SJ') echo 'selected="selected"'; ?> value='SJ'>San Juan</option>
                        <option <?php if ($data_modificar['location'] == 'SL') echo 'selected="selected"'; ?> value='SL'>San Luis</option>
                        <option <?php if ($data_modificar['location'] == 'SC') echo 'selected="selected"'; ?> value='SC'>Santa Cruz</option>
                        <option <?php if ($data_modificar['location'] == 'SF') echo 'selected="selected"'; ?> value='SF'>Santa Fe</option>
                        <option <?php if ($data_modificar['location'] == 'SE') echo 'selected="selected"'; ?> value='SE'>Santiago del Estero</option>
                        <option <?php if ($data_modificar['location'] == 'TF') echo 'selected="selected"'; ?> value='TF'>Tierra del Fuego</option>
                        <option <?php if ($data_modificar['location'] == 'TM') echo 'selected="selected"'; ?> value='TM'>Tucuman</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><strong>Estado de alertas para este enlace, actualmente estan: <?php
                            if ($data_modificar['send_alert'] == '1')
                                echo 'encendidas';
                            elseif ($data_modificar['send_alert'] == '0')
                                echo 'apagadas';
                            ?> </strong></label>
                    <select name='send_alert'>
                        <option <?php if ($data_modificar['send_alert'] == '1') echo 'selected="selected"'; ?> value='1'>Encendidas</option>
                        <option <?php if ($data_modificar['send_alert'] == '0') echo 'selected="selected"'; ?> value='0'>Apagadas</option>
                    </select>
                </div> 
                <div class="form-group">
                    <div data-role="main" class="ui-content">
                        <label for="points"><strong>Valor de referencia para enviar alerta si el promedio de las ultimas 6 horas lo supera por 15 puntos:
                        <?php 
                        echo '( Primer referencia historica '.$average_first. ' )'; 
                        ?>
                        </strong></label>
                        <input type="range" name="value_alert" id="points" value="<?php echo  $data_modificar['alert_value'] ?>" min="20" max="75" data-show-value="true">
                    </div>
                </div>

                <!-- Olga inicio 15/11/18 -->

                <div class="form-group "><label><strong>Padre</strong></label>
                    <select name='id_equipo_padre'>
                        <?php
                        $array_ids = array();
                        $array_name = array();
                        $cantidad = 0; // guarda la cantidad maxima de veces que aparecio un mismo padre

                        $query_1 = "SELECT *FROM padre_hijo WHERE id_equipo_hijo='$id_equipos' ";
                        $query_2 = $link->query($query_1);
                        while ($gett = $query_2->fetch_assoc()) {
                            array_push($array_ids, $gett['id_equipo_padre']); // (14 21)
                            array_push($array_name, $gett['device_name_padre']);
                        } // termina while
                        for ($count = 0; $count < count($array_ids); $count++) {
                            $query_3 = "SELECT COUNT( id_equipo_hijo) FROM padre_hijo WHERE id_equipo_hijo = '$array_ids[$count]' "; // si aparece el 14 o 21 como //hijo, me devuelve la cant de veces que aparecio c/uno
                            $query_4 = $link->query($query_3);
                            if ($query_4 >= $cantidad) {
                                $max_padre = $array_ids[$count];
                                $max_name = $array_name[$count];
                            }
                        }

                        if ($max_padre != NULL) {
                            echo "<option value= ' " . $max_padre . "'>" . $max_padre . ' - ' . $max_name . "</option>";
                        }
                        $query_por_id = "SELECT id_equipo, device_name FROM equipos";
                        $do_query_pid = $link->query($query_por_id);
                        echo "<option value= ' '>No tiene</option>";
                        while ($data_id = $do_query_pid->fetch_assoc()) {
                            echo "<option value='" . $data_id['id_equipo'] . "'>" . $data_id['id_equipo'] . ' - ' . $data_id['device_name'] . "</option>";
                        }
                        ?>

                    </select>
                </div>

                <!-- Olga fin 15/11/18 -->


                <div class="form-group">
                    <input type="submit"   class="btn btn-primary" value="Guardar datos" >
                    <input type="reset" class="btn btn-default" value="Reinciar campos">
                </div>
            </form>
        </div>

    </body>
</html>





















