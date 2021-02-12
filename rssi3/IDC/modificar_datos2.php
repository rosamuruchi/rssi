<?php
// Initialize the session
session_start();
 
// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
  header("location: login.php");
  exit;
}
?>

<?php
// Include config file
require_once 'config.php';
include('../includes/functions.php');

if($_GET['submit']==1) {
    $link->query("SET FOREIGN_KEY_CHECKS=0");
    $id_delete = $_GET['id_equipos'];
    $query_delete = "DELETE FROM equipos WHERE id_equipo = '$id_delete'";
    if($link->query($query_delete)===TRUE) {
        echo "Consulta exitosa";
        $link->query("SET FOREIGN_KEY_CHECKS=1");
        header("Location: ./welcome.php?estado=equipo_eliminado");
    }
    else echo "Error en la consulta de eliminar ". $link->error;
}

else{
// Define variables and initialize with empty values
$ip = $oid_rx = $oid_tx = $location = "";
$ip_err = $oid_rx_err = $oid_tx_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate ip
    if(empty(trim($_POST["ip"]))){
        $ip_err = "Please enter a ip.";
    } else {
        $ip = trim($_POST['ip']);
    }

    // Validate oid_rx
    if(empty(trim($_POST['oid_rx']))){
        $oid_rx_err = "Por favor ingrese un OID para la calidad RX.";     
    } elseif(strlen(trim($_POST['oid_rx'])) < 6){
        $oid_rx_err = "Un OID tiene que tener mas caracteres.";
    } else{
        $oid_rx = trim($_POST['oid_rx']);
    }
    
    // Validate oid_tx
    if(empty(trim($_POST['oid_tx']))){
        $oid_tx_err = "Por favor ingrese un OID para la calidad TX.";     
    } elseif(strlen(trim($_POST['oid_tx'])) < 6){
        $oid_tx_err = "Un OID tiene que tener mas caracteres.";
    } else{
        $oid_tx = trim($_POST['oid_tx']);
    }

    $location = $_POST['location'];
    $send_alert = $_POST['send_alert'];
    $val_alert = $_POST['value_alert'];
    // Check input errors before inserting in database
    if(empty($ip_err) && empty($oid_rx_err) && empty($oid_tx_err)) {
        // Prepare an insert statement
        $id_equipos = $_SESSION['id_equipos'];
        $sql = "UPDATE equipos SET ip = ?, oid_rx = ?, oid_tx = ?, device_name = ?, location = ?, alert_value = ?, send_alert = ? WHERE id_equipo = '$id_equipos'";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssssi", $param_ip, $param_oid_rx, $param_oid_tx, $param_device_name, $param_location, $param_alert_value, $param_send_alert);
            // Set parameters
            $name_session = new SNMP(SNMP::VERSION_1, $ip, "public");
            $dname = $name_session->walk("sysName");
            if($dname == '') {
                $device_name = substr(varDumpToString($name_session), 10);
            }
            else $device_name = substr($dname['SNMPv2-MIB::sysName.0'], strpos($dname['SNMPv2-MIB::sysName.0'], 'STRING:')+8, strpos($dname['SNMPv2-MIB::sysName.0'], substr($dname['SNMPv2-MIB::sysName.0'], -2)));
            $param_send_alert = $send_alert;
            $param_alert_value = $val_alert;
            $param_device_name= $device_name;
            $param_ip = $ip;
            $param_oid_rx = $oid_rx;
            $param_oid_tx = $oid_tx;
            $param_location = $location;
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
            	// Also, executes this query to update values on data too
            	$sql_data = "UPDATE data SET ip_db = '$param_ip', location = '$param_location' WHERE id_equipo= '$id_equipos'";
            	if ($link->query($sql_data) === TRUE) {
    				printf("Se realizo la consulta con exito");
				}
				else {
					printf("Error. No se pudo realizar la consulta");
				}
                // Redirect to login page
                header("Location: ./welcome.php?estado=equipo_modificado");
            } else{
                echo "Algo va mal. Intente nuevamente luego. ". $stmt->error ;
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
}
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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<?php 
$id_equipos = $_GET['id_equipos'];
$_SESSION['id_equipos'] = $id_equipos;
$do_q= $link->query("SELECT ip, oid_rx, oid_tx, location, device_name, alert_value FROM equipos WHERE id_equipo = '$id_equipos'");
$data_modificar = $do_q->fetch_assoc();
$ip_modify = $data_modificar['ip'];
$query_rx_first = "SELECT rx_signal FROM data WHERE ip_db LIKE '$ip_modify' AND rx_signal > -100 order by date_p ASC LIMIT 1";
$exec_rx_first = $link->query($query_rx_first);
$first_rx = $exec_rx_first->fetch_assoc();
// Get first tx value
$query_tx_first = "SELECT tx_signal FROM data WHERE ip_db LIKE '$ip_modify' AND tx_signal > -100  order by date_p ASC LIMIT 1";
$exec_tx_first = $link->query($query_tx_first);
$first_tx = $exec_tx_first->fetch_assoc();
$average_first = round((($first_rx['rx_signal']*-1) + ($first_tx['tx_signal']*-1))/2);
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
                        <option value='BA'>Buenos Aires</option>
                        <option value='CABA'>CABA</option>
                        <option value='CC'>Chaco</option>
                        <option value='CH'>Chubut</option>
                        <option value='CB'>Cordoba</option>
                        <option value='CN'>Corrientes</option>
                        <option value='ER'>Entre Rios</option>
                        <option value='FM'>Formosa</option>
                        <option value='JY'>Jujuy</option>
                        <option value='LP'>La Pampa</option>
                        <option value='LR'>La Rioja</option>
                        <option value='MZ'>Mendoza</option>
                        <option value='MN'>Misiones</option>
                        <option value='NQ'>Neuquen</option>
                        <option value='RN'>Rio Negro</option>
                        <option value='SA'>Salta</option>
                        <option value='SJ'>San Juan</option>
                        <option value='SL'>San Luis</option>
                        <option value='SC'>Santa Cruz</option>
                        <option value='SF'>Santa Fe</option>
                        <option value='SE'>Santiago del Estero</option>
                        <option value='TF'>Tierra del Fuego</option>
                        <option value='TM'>Tucuman</option>
                </select>
            </div>
            <div class="form-group">
                <label><strong>Activar alertas para este enlace</strong></label>
                <select name='send_alert'>
                        <option value='1'>Si, activar para este enlace</option>
                        <option value='0'>No, desactivar</option>
                </select>
            </div>
            <div class="form-group">
                <div data-role="main" class="ui-content">
                  <label for="points"><strong>Valor de referencia para enviar alerta si el promedio de las ultimas 6 horas lo supera por 15 puntos:</strong></label>
                  <input type="range" name="value_alert" id="points" value="<?php echo $average_first ?>" min="20" max="75" data-show-value="true">
              </div>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Guardar datos">
                <input type="reset" class="btn btn-default" value="Reinciar campos">
            </div>
        </form>
    </div>

</body>
</html>