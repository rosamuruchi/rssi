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
// Define variables and initialize with empty values
$ip = $oid_rx = $oid_tx = "";
$ip_err = $oid_rx_err = $oid_tx_err = $location_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate ip
    if(empty(trim($_POST["ip"]))){
        $ip_err = "Ingresar ip.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id_equipo FROM equipos WHERE ip = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_ip);
            
            // Set parameters
            $param_ip = trim($_POST["ip"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $ip_err = "La ip ya esta ingresada.";
                } else{
                    $ip = trim($_POST["ip"]);
                }
            } else{
                echo "Algo fue mal. Intente luego nuevamente.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Validate oid_rx
    if(empty(trim($_POST['oid_rx']))){
        $oid_rx_err = "Ingresar un OID para el RX.";     
    } elseif(strlen(trim($_POST['oid_rx'])) < 6){
        $oid_rx_err = "Un OID tiene que tener mas caracteres.";
    } else{
        $oid_rx = trim($_POST['oid_rx']);
    }
    
    // Validate oid_tx
    if(empty(trim($_POST['oid_tx']))){
        $oid_tx_err = "Ingresar un OID para el TX.";     
    } elseif(strlen(trim($_POST['oid_tx'])) < 6){
        $oid_tx_err = "Un OID tiene que tener mas caracteres.";
    } else{
        $oid_tx = trim($_POST['oid_tx']);
    }

    
    $location = $_POST['location'];
    // Check input errors before inserting in database
    if(empty($ip_err) && empty($oid_rx_err) && empty($oid_tx_err)){
        // Prepare an insert statement

        $sql = "INSERT INTO equipos (ip, oid_rx, oid_tx, device_name, location, alert_value) VALUES (?, ?, ?, ?, ?, ?)";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssss", $param_ip, $param_oid_rx, $param_oid_tx, $param_device_name, $param_location, $param_value_alert);
            $name_session = new SNMP(SNMP::VERSION_1, "$ip", "public");
            $dname = $name_session->walk("sysName");
            //echo "<h1>error</h1>";
            if($dname == '') {
                $device_name = substr(varDumpToString($name_session), 10);
            }
            // this whole lien is just for getting the device name. I think it could be more practical. But it works so...
            else $device_name = substr($dname['SNMPv2-MIB::sysName.0'], strpos($dname['SNMPv2-MIB::sysName.0'], 'STRING:')+8, strpos($dname['SNMPv2-MIB::sysName.0'], substr($dname['SNMPv2-MIB::sysName.0'], -2)));            
            // Get average first RSSI value (rx and tx)
            $query_rx_first = "SELECT rx_signal FROM data WHERE ip_db LIKE '$ip' AND rx_signal > -100 order by date_p ASC LIMIT 1";
            $exec_rx_first = $link->query($query_rx_first);
            $first_rx = $exec_rx_first->fetch_assoc();
            // Get first tx value
            $query_tx_first = "SELECT tx_signal FROM data WHERE ip_db LIKE '$ip' AND tx_signal > -100  order by date_p ASC LIMIT 1";
            $exec_tx_first = $link->query($query_tx_first);
            $first_tx = $exec_tx_first->fetch_assoc();
            $average_first = (($first_rx['rx_signal']*-1) + ($first_tx['tx_signal']*-1))/2;
            $param_value_alert = $average_first;
            $param_device_name= $device_name;
            $param_ip = $ip;
            $param_oid_rx = $oid_rx;
            $param_oid_tx = $oid_tx;
            $param_location = $location;
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: welcome.php?estado=equipo_agregado");
            } else{
                echo "Algo va mal. Intente nuevamente luego.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agregar IP</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <p><a href="welcome.php" class="btn btn-danger">Volver a Inicio</a></p>
        <h2>Agregar IP</h2>
        <p>Ganancia TECOAR. Rellenar campos. Todos son obligatorios.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($ip_err)) ? 'has-error' : ''; ?>">
                <label>IP</label>
                <input type="text" name="ip" class="form-control" value="<?php echo $ip; ?>">
                <span class="help-block"><?php echo $ip_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($oid_rx_err)) ? 'has-error' : ''; ?>">
                <label>OID para RX</label>
                <input type="oid_rx" name="oid_rx" class="form-control" value="<?php echo $oid_rx; ?>">
                <span class="help-block"><?php echo $oid_rx_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($oid_tx_err)) ? 'has-error' : ''; ?>">
                <label>OID para TX</label>
                <input type="oid_tx" name="oid_tx" class="form-control" value="<?php echo $oid_tx; ?>">
                <span class="help-block"><?php echo $oid_tx_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($location_err)) ? 'has-error' : ''; ?>">
                <label>Provincia</label>
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
                <span class="help-block"><?php echo $location_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Guardar datos">
                <input type="reset" class="btn btn-default" value="Reinciar campos">
            </div>
        </form>
    </div>    
</body>
</html>